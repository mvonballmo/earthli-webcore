<?php

$parameterData = read_var ('inputText');
$contentObjectId = read_var ('id');

$input = iconv("UTF-8", "CP1252", $parameterData);

$munger = $App->html_text_formatter();

$entry_query = $App->login->all_entry_query();
$obj = $entry_query->object_at_id($contentObjectId);

$output = $munger->transform($input, $obj);

echo $output;