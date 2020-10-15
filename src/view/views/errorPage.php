<?php
/* @var $this \yii\web\View */
/* @var $exception \Exception */
/* @var $handler \topshelfcraft\canary\ErrorHandler */

if (method_exists($this, 'beginPage'))
{
	$this->beginPage();
}

$renderer = new \topshelfcraft\canary\view\renderers\WebRenderer();
echo $renderer->renderErrorPage($handler->getErrorReport(), ['view' => $this]);

if (method_exists($this, 'endPage'))
{
	$this->endPage();
}
