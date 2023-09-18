<?php
namespace TopShelfCraft\Canary\view\renderers;

use TopShelfCraft\Canary\context\ValueListContext;
use TopShelfCraft\Canary\context\VarDumpContext;
use TopShelfCraft\Canary\ErrorReport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class WebRenderer
{

	protected Environment $twig;

	public function __construct()
	{
		$templatesPath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..') . DIRECTORY_SEPARATOR . 'views';
		$loader = new FilesystemLoader($templatesPath);
		$this->twig = new Environment($loader);
	}

	/**
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderErrorPage(ErrorReport $report, array $with = []): string
	{
		return $this->twig->render(
			'errorPage.twig',
			array_merge(['report' => $report], $with)
		);
	}

	/**
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderValueListContext(ValueListContext $context, array $with = []): string
	{
		/*
		 * TODO: Return this as Twig\Markup to avoid needing `raw` in the template.
		 * (Maybe some values might be user-generated, and failing to escape them could be a security concern?)
		 * See `Temeplate::raw()`
		 */
		return $this->twig->render(
			'_valueListContext.twig',
			array_merge(['context' => $context], $with)
		);
	}

	/**
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderVarDumpContext(VarDumpContext $context, array $with = []): string
	{
		throw new \Exception("Not implemented yet.");
		// TODO: Implement
	}

}
