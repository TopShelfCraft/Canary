<?php
namespace topshelfcraft\canary;

use Throwable;

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

}
