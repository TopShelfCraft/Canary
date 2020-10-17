<?php
namespace topshelfcraft\canary\view\renderers;

use craft\helpers\Template;
use topshelfcraft\canary\context\ValueListContext;
use topshelfcraft\canary\context\VarDumpContext;
use topshelfcraft\canary\ErrorReport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class WebRenderer
{

	/**
	 * @var Environment
	 */
	protected $twig;

	/**
	 *
	 */
	public function __construct()
	{
		$templatesPath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..') . DIRECTORY_SEPARATOR . 'views';
		$loader = new FilesystemLoader($templatesPath);
		$this->twig = new Environment($loader);
	}

	/**
	 * @param ErrorReport $report
	 * @param array $with
	 *
	 * @return string
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderErrorPage(ErrorReport $report, $with = [])
	{
		return $this->twig->render(
			'errorPage.twig',
			array_merge(['report' => $report], $with)
		);
	}

	/**
	 * @param ValueListContext $context
	 * @param array $with
	 *
	 * @return string
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderValueListContext(ValueListContext $context, $with = [])
	{
		$rendered = $this->twig->render(
			'_valueListContext.twig',
			array_merge(['context' => $context], $with)
		);
		return Template::raw($rendered);
	}

	/**
	 * @param VarDumpContext $context
	 * @param array $with
	 *
	 * @return string
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderVarDumpContext(VarDumpContext $context, $with = [])
	{
		return $this->twig->render(
			'_varDumpContext.twig',
			array_merge(['context' => $context], $with)
		);
	}

}
