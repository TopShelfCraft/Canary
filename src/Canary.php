<?php
namespace TopShelfCraft\Canary;

use Craft;
use TopShelfCraft\base\Plugin;

class Canary extends Plugin
{
	public function init()
	{
		if (Craft::$app->getRequest()->getIsConsoleRequest()) {
			return;
		}

		Craft::setAlias("@TopShelfCraft/Canary", __DIR__);

		Craft::$app->set('errorHandler', ErrorHandler::class);
		Craft::$app->getErrorHandler()->register();

	}

}
