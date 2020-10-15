<?php
namespace topshelfcraft\canary\view\renderers;

use topshelfcraft\canary\ErrorReport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class WebRenderer
{

	/**
	 * @param ErrorReport $report
	 * @param array $context
	 *
	 * @return string
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderErrorPage(ErrorReport $report, $context = [])
	{

		$templatesPath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..') . DIRECTORY_SEPARATOR . 'views';
		$loader = new FilesystemLoader($templatesPath);
		$twig = new Environment($loader);

		return $twig->render(
			'errorPage.twig',
			array_merge(['report' => $report], $context)
		);

	}

}
