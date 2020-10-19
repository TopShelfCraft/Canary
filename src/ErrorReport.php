<?php
namespace topshelfcraft\canary;

use Craft;
use craft\base\PluginInterface;
use craft\helpers\App;
use Throwable;
use topshelfcraft\canary\context\ValueListContext;
use Yii;
use yii\base\Module;

class ErrorReport
{

	/**
	 * @var Throwable
	 */
	protected $error;

	/**
	 * @var ErrorHandler
	 */
	protected $handler;

	/**
	 * @var int maximum number of source code lines to be displayed. Defaults to 19.
	 *
	 * TODO: Make configurable
	 */
	protected $maxSourceLines = 19;

	/**
	 * @var int maximum number of trace source code lines to be displayed. Defaults to 13.
	 *
	 * TODO: Make configurable
	 */
	protected $maxTraceSourceLines = 13;

	/**
	 * @param Throwable $error
	 * @param ErrorHandler|null $handler
	 */
	public function __construct(Throwable $error, ErrorHandler $handler = null)
	{
		$this->error = $error;
		$this->handler = $handler;
	}

	/**
	 * @return Throwable
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return string|null
	 */
	public function getErrorName()
	{

		if ($this->handler && $this->error instanceof \Exception)
		{
			return $this->handler->getExceptionName($this->error);
		}

		if (method_exists($this->error, 'getName'))
		{
			return $this->error->getName();
		}

		return get_class($this->error);

	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{

		if ($this->handler && ($message = $this->handler->getErrorMessage($this->error)))
		{
			return $message;
		}

		if ($message = $this->error->getMessage())
		{
			return $message;
		}

		// TODO: Come up with a less ominous default message?
		return "Something went wrong...";

	}

	/**
	 * @return array
	 */
	public function getFrames()
	{

		$frames = array_merge(
			[
				[
					'file' => $this->error->getFile(),
					'line' => $this->error->getLine(),
					'class' => get_class($this->error),
					'function' => null,
					'args' => null,
				]
			],
			$this->error->getTrace()
		);

		$out = [];

		foreach ($frames as $frameIndex => $frame)
		{

			$lines = [];

			$begin = $end = 0;

			$file = $frame['file'] ?? null;
			$line = $frame['line'] ?? null;

			if ($file !== null && $line !== null) {

				$line--; // adjust line number from one-based to zero-based
				$lines = @file($file);

				if (is_array($lines))
				{
					// Shift the lines to be 1-indexed, so array keys match line numbers.
					array_unshift($lines, null);
					unset($lines[0]);
				}

				if ($line < 0 || $lines === false || ($lineCount = count($lines)) < $line) {
					// Dummy values if something went wonky.
					// TODO: Happy path
					$lines = [];
					$start = null;
					$end = null;
				}
				else
				{
					// TODO: Happy path
					$linesToShow = ($frameIndex === 0 ? $this->maxSourceLines : $this->maxTraceSourceLines);
					$half = (int) ($linesToShow / 2);
					$begin = $line - $half > 0 ? $line - $half : 1;
					$end = $line + $half <= $lineCount ? $line + $half : $lineCount;
				}

			}

			$out[] = $frame +
				[
					'lines' => $lines,
					'begin' => $begin,
					'end' => $end,
				];

		}

		return $out;

	}

	/**
	 * @return array
	 */
	public function getContextTabs()
	{

		$tabs = [
			"Request" => [],
			"User" => [],
			"App" => [],
			"Environment" => [],
			"Debug" => [],
		];

		try
		{
			if (!empty($queryParams = Yii::$app->getRequest()->getQueryParams()))
			{
				$tabs["Request"]["Query Params / GET"] = new ValueListContext(array_map('json_encode', $queryParams));
			}

		}
		catch(Throwable $e)
		{
			// TODO: Display error message.
		}

		try
		{
			if (!empty($bodyParams = Yii::$app->getRequest()->getBodyParams()))
			{
				$tabs["Request"]["Body Parameters / POST"] = new ValueListContext(array_map('json_encode', $bodyParams));
			}
		}
		catch(Throwable $e)
		{
			// TODO: Display error message.
		}

		try
		{
			$tabs["Request"]["Route"] = new ValueListContext([
				'Controller / Action' => Craft::$app->requestedRoute,
				// TODO: Craft::$app->controller->actionParams
			]);
		}
		catch(Throwable $e)
		{
			// TODO: Display error message.
		}

		try
		{
			$tabs["Request"]["Session"] = new ValueListContext([
				'ID' => Yii::$app->getSession()->getId()
			]);
		}
		catch(Throwable $e)
		{
			// TODO: Display error message.
		}

		// TODO: Add Cookies to "Request" tab.



		try
		{
			$tabs["User"]["User"] = new ValueListContext([
				"ID" => ($id = Craft::$app->getUser()->getId()) ? $id : "Guest"
			]);
		}
		catch(Throwable $e)
		{
			// TODO: Display error message
		}

		// TODO: Add Browser to "User" tab
		// TODO: Device to "User" tab



		try
		{
			$tabs["App"]["Application Info"] = new ValueListContext([
				'PHP version' => App::phpVersion(),
				'OS version' => PHP_OS . ' ' . php_uname('r'),
				'Craft edition & version' => 'Craft ' . App::editionName(Craft::$app->getEdition()) . ' ' . Craft::$app->getVersion(),
				'Yii version' => Yii::getVersion(),
			]);
		}
		catch(Throwable $e)
		{
			// TODO: Render useful error message as Message context.
		}

		try
		{

			$modules = [];
			foreach (Craft::$app->getModules() as $id => $module) {
				if ($module instanceof PluginInterface) {
					continue;
				}
				if ($module instanceof Module) {
					$modules[$id] = get_class($module);
				} else if (is_string($module)) {
					$modules[$id] = $module;
				} else if (is_array($module) && isset($module['class'])) {
					$modules[$id] = $module['class'];
				} else {
					$modules[$id] = "(Unknown type)";
				}
			}

			$tabs["App"]["Modules"] = new ValueListContext($modules);

		}
		catch(Throwable $e)
		{
			// TODO: Display error message
		}

		try
		{

			$plugins = array_map(
				function(PluginInterface $plugin) {
					return get_class($plugin);
				},
				Craft::$app->getPlugins()->getAllPlugins()
			);
			ksort($plugins);

			$tabs["App"]["Plugins"] = new ValueListContext($plugins);

		}
		catch(Throwable $e)
		{
			// TODO: Render useful error message as Message context.
		}

		try
		{

			$aliases = [];
			foreach (Craft::$aliases as $alias => $value) {
				if (is_array($value)) {
					foreach ($value as $a => $v) {
						$aliases[$a] = $v;
					}
				} else {
					$aliases[$alias] = $value;
				}
			}
			ksort($aliases);

			$tabs["App"]["Aliases"] = new ValueListContext($aliases);

		}
		catch(Throwable $e)
		{
			// TODO: Render useful error message as Message context.
		}



		try
		{

			// Remove any arrays from $_SERVER to get around an "Array to string conversion" error
			$serverVals = [];
			if (isset($_SERVER))
			{
				foreach ($_SERVER as $key => $value)
				{
					if (is_array($value))
					{
						$value = '(Array)';
					}
					$serverVals[$key] = Craft::$app->getSecurity()->redactIfSensitive($key, $value);
				}
			}

			// Remove any arrays from $_ENV to get around an "Array to string conversion" error
			if (isset($_ENV))
			{
				foreach ($_ENV as $key => $value)
				{
					if (is_array($value))
					{
						$value = '(Array)';
					}
					$serverVals[$key] = Craft::$app->getSecurity()->redactIfSensitive($key, $value);
				}
			}

			$tabs["Environment"]['Server/Environment Values'] = new ValueListContext($serverVals);

		}
		catch(Throwable $e)
		{
			// TODO: Render useful error message as Message context.
		}

		try
		{

			// Remove any arrays from $_COOKIE to get around an "Array to string conversion" error
			$cookieVals = [];
			if (isset($_COOKIE))
			{
				foreach ($_COOKIE as $key => $value)
				{
					if (is_array($value))
					{
						$value = '(Array)';
					}
					$cookieVals[$key] = Craft::$app->getSecurity()->redactIfSensitive($key, $value);
				}
			}

			$tabs["Environment"]['Cookies'] = new ValueListContext($cookieVals);

		}
		catch(Throwable $e)
		{
			// TODO: Render useful error message as Message context.
		}



		// TODO: Add Chirps to "Debug" tab.
		// TODO: Add Timing to "Debug" tab.



		return $tabs;

	}

}
