<?php
/* @var $this \yii\web\View */
/* @var $exception \Exception */
/* @var $handler \topshelfcraft\canary\ErrorHandler */
?>

<?php if (method_exists($this, 'beginPage')): ?>
	<?php $this->beginPage() ?>
<?php endif ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>

    <title><?php
		$name = $handler->getExceptionName($exception);
		if ($exception instanceof \yii\web\HttpException) {
			echo (int) $exception->statusCode . ' ' . $handler->htmlEncode($name);
		} else {
			if ($name !== null) {
				echo $handler->htmlEncode($name . ' – ' . get_class($exception));
			} else {
				echo $handler->htmlEncode(get_class($exception));
			}
		}
		?></title>
</head>

<body>
<?php echo $handler->___getWhoopsBody($exception); ?>
</body>

</html>
<?php if (method_exists($this, 'endPage')): ?>
	<?php $this->endPage() ?>
<?php endif ?>
