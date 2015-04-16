<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="stylesheet" type="text/css" href="<?=baseUrl()?>/css/common.css"/>
	<script src="<?=baseUrl()?>libs/jquery-1.11.1.min.js"></script>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body style="margin:0">
	<?
	$flash_messages = Yii::app()->user->getFlashes();
	if($flash_messages){
		?>
		<ul class="flashes" style="padding:1em 0 0em 0;margin:0;list-style-type: none;"><?
		foreach($flash_messages as $key => $message) {
			echo '<li><div class="flash_message flash_message_' . $key . '" style="">' . $message . "</div></li>\n";
		}
		?></ul><?
	} ?>
	
	<?php echo $content; ?>
</body>