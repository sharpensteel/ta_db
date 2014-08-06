<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>
<a href="<?=createUrl("backend/packet_parser/upload")?>">parser_packet/upload</a><br><br>
<a href="<?=createUrl("backend/default/parse_all/?packet_type_id=1")?>">parse not-parsed attack log records</a><br><br>
<a href="<?=createUrl("backend/default/parse_all/?packet_type_id=1&forse_all=1")?>">re-parse all attack log records</a><br><br>
<a href="<?=createUrl("backend/curl_manager")?>">manager for cURL</a><br><br>