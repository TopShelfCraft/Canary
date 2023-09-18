<?php
namespace TopShelfCraft\Canary\context;

use TopShelfCraft\Canary\view\renderers\WebRenderer;

class VarDumpContext extends ContextType
{

	public function renderWeb()
	{
		return (new WebRenderer())->renderVarDumpContext($this);
	}

}
