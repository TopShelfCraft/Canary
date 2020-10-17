<?php
namespace topshelfcraft\canary;

use Craft;
use craft\base\Plugin;

class Canary extends Plugin
{

	/**
	 * @var bool
	 */
	public $hasCpSection = false;

	/**
	 * @var bool
	 */
	public $hasCpSettings = false;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		Craft::$app->set(
			'errorHandler',
			[
				'class' => ErrorHandler::class
			]
		);
		Craft::$app->getErrorHandler()->register();
	}

}
