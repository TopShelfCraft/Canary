<?php
namespace topshelfcraft\canary;

use Craft;
use craft\helpers\StringHelper;
use Stringy\Stringy;
use Whoops\Exception\Frame;
use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;
use yii\helpers\Markdown;

class ErrorHandler extends \craft\web\ErrorHandler
{

	/**
	 *
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * @param \Exception $exception
	 *
	 * @return void
	 */
	protected function renderException($exception)
	{
		$this->exceptionView = __DIR__.'/view/views/whoops.php';
		$this->exceptionView = __DIR__.'/view/views/errorPage.php';
//		$this->exceptionView = '@yii/views/errorHandler/exception.php';
		return parent::renderException($exception);
	}

	/**
	 * @return ErrorReport
	 */
	public function getErrorReport()
	{
		return new ErrorReport($this->exception);
	}

	/**
	 * @param \Throwable $error
	 *
	 * @return string|null
	 */
	public function getErrorMessage(\Throwable $error)
	{
		return $error->getMessage();
	}











	/**
	 * @param \Throwable $exception
	 *
	 * @return string
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function ___getWhoopsBody(\Throwable $exception)
	{

		/* @var $whoops RunInterface */
		$whoops = Craft::createObject(Run::class);

		$handler = new PrettyPageHandler();
		$handler->handleUnconditionally(true); // Prevent it from recognizing CLI mode (which is a test necessity) and therefore quitting

		$handler->setPageTitle("F!");
		$handler->setEditor("phpstorm");

		$whoops->pushHandler($handler);

		$whoops->pushHandler(function($exception, $exceptionInspector, $runInstance) {
			foreach($exceptionInspector->getFrames() as $i => $frame) {
				/** @var Frame $frame */

				$frame->addComment($this->argumentsToString($frame->getArgs()));

				if (method_exists($exception, 'renderSuggestion'))
				{
					$frame->addComment($exception->renderSuggestion(), 'suggestion');
				}

			}

			return Handler::DONE;
		});

		$whoops->pushHandler(function($exception, $exceptionInspector, $runInstance) {
			foreach($exceptionInspector->getFrames() as $i => $frame) {
				continue;
				/** @var Frame $frame */

				if ($frame->getClass() && StringHelper::startsWith($frame->getClass(), 'craft', false))
				{

					$__reflect_method = (new \ReflectionClass($frame->getClass()))->getMethod($frame->getFunction());
					$__reflect_docblock = $__reflect_method->getDocComment();

					if ($__reflect_docblock)
					{
//						$factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();

						$frame->addComment(Markdown::process($__reflect_docblock));
					}

					$craftClass = (string) Stringy::create($frame->getClass())
						->replace('\\', '-')
						->toLowerCase();

					$url = 'https://docs.craftcms.com/api/v3/' . $craftClass . '.html#method-' . strtolower($frame->getFunction());
					$frame->addComment($url);
				}

			}

			return Handler::DONE;
		});

		$whoops->allowQuit(false);
		$whoops->writeToOutput(false); // We will take the output and put it into the Response
		$whoops->sendHttpCode(false); // We take care of this ourselves

		return $whoops->handleException($exception);

	}

}
