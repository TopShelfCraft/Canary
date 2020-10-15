<?php
namespace topshelfcraft\canary;

use yii\helpers\Markdown;

trait SolvableExceptionTrait {

	private $_message;

	public function setSuggestion($message)
	{
		$this->_message = $message;
		return $this;
	}

	public function renderSuggestion()
	{
		return Markdown::process($this->_message);
	}

}
