<?php
namespace topshelfcraft\canary\context;

use topshelfcraft\canary\view\renderers\WebRenderer;

class ValueListContext extends ContextType
{

	/**
	 * @var array
	 */
	protected $values = [];

	public function __construct(array $values = [])
	{
		$this->values = $values;
	}

	/**
	 * @return array
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @return string
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function renderWeb()
	{
		return (new WebRenderer())->renderValueListContext($this);
	}

}
