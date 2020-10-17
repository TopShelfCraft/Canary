<?php
namespace topshelfcraft\canary\context;

use topshelfcraft\canary\view\renderers\WebRenderer;

class VarDumpContext extends ContextType
{

	/**
	 *
	 */
	public function renderWeb()
	{
		return (new WebRenderer())->renderVarDumpContext($this);
	}

}
