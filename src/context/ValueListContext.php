<?php
namespace TopShelfCraft\Canary\context;

use TopShelfCraft\Canary\view\renderers\WebRenderer;

class ValueListContext extends ContextType
{

	public function __construct(
		protected array $values = [],
	)
	{
	}

	public function getValues(): array
	{
		return $this->values;
	}

	public function renderWeb(): string
	{
		return (new WebRenderer())->renderValueListContext($this);
	}

}
