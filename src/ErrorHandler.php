<?php
namespace TopShelfCraft\Canary;

use Throwable;

class ErrorHandler extends \craft\web\ErrorHandler
{

	/**
	 * @param Throwable $exception
	 */
	protected function renderException($exception): void
	{
		$this->exceptionView = __DIR__.'/view/views/errorPage.php';
		parent::renderException($exception);
	}

	public function getErrorReport(): ErrorReport
	{
		return new ErrorReport($this->exception);
	}

	public function getErrorMessage(Throwable $error): string
	{
		return $error->getMessage();
	}

}
