<?php
namespace TopShelfCraft\Canary;

use Throwable;

class ErrorHelper
{

	/**
	 * @return Suggestion[]
	 */
	public static function suggestionsFromError(Throwable $error): array
	{

		if (method_exists($error, 'getSuggestions'))
		{
			return array_map(
				function(Suggestion|string $s) {
					return Suggestion::make($s);
				},
				$error->getSuggestions()
			);
		}

		if (method_exists($error, 'getSuggestion'))
		{
			return [Suggestion::make($error->getSuggestion())];
		}

		// https://github.com/yiisoft/friendly-exception/blob/master/src/FriendlyExceptionInterface.php
		if (method_exists($error, 'getSolution'))
		{
			// TODO: Improve rendering for title/name
			return [Suggestion::make($error->getSolution())];
		}

		// TODO: Resolve suggestions for known exceptions from internal container.

		return [];

	}

}
