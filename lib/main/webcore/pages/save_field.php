<?php

$parameterData = read_var ('inputText');
$contentObjectId = read_var ('id');

$input = iconv("UTF-8", "CP1252", $parameterData);

$entry_query = $App->login->all_entry_query();

/** @var CONTENT_OBJECT $obj */
$obj = $entry_query->object_at_id($contentObjectId);

if ($obj != null)
{
  $obj->description = $input;

  $obj->store();

  $App->show_message('Data was saved.', 'info');
}
else
{
  $App->show_message('Object for that ID was not found.');
}


