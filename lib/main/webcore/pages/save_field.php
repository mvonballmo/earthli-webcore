<?php

$parameterData = read_var ('inputText');
$contentObjectId = read_var ('id');

$input = iconv("UTF-8", "CP1252", $parameterData);
$munger = $App->html_text_formatter();

$entry_query = $App->login->all_entry_query();

/** @var CONTENT_OBJECT $obj */
$obj = $entry_query->object_at_id($contentObjectId);

$formatted_text = '';
$errors = '';
$message = '';

if ($obj != null)
{
  $obj->description = $input;
  $obj->store_as_is();

  $message = $App->get_begin_message ('info') . 'Data was saved.' . $App->get_end_message();
  $formatted_text = $munger->transform($input, $obj);
}
else
{
  $message = $App->get_begin_message ('info') . 'Object for that ID was not found.' . $App->get_end_message();
}

$result = array(
  'text' => $formatted_text,
  'errors' => $errors,
  'message' => $message,
  'modified' => $obj->time_modified->as_iso()
);

echo json_encode($result);

