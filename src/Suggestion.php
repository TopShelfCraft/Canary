<?php
namespace TopShelfCraft\Canary;

use Throwable;
use yii\helpers\Markdown;

class Suggestion
{

	public function __construct(
		public string $description,
		public ?string $title = null,
	)
	{
	}

	public function renderDescription()
	{
		// Protect against secondary errors in the Markdown processing...
		try
		{
			return Markdown::process($this->description, 'gfm');
		}
		catch (Throwable)
		{
			return $this->description;
		}
	}

	public static function make(Suggestion|string $suggestion): static
	{
		if ($suggestion instanceof self)
		{
			return $suggestion;
		}
		return new static($suggestion);
	}

}
