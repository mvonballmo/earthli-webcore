<?php

class TEXT_VALIDATION_ERROR
{
  public $message;
  public $line_number;
  public $column_start;
  public $column_end;
}

$parameterData = read_var ('inputText');
$contentObjectId = read_var ('id');

$input = iconv("UTF-8", "CP1252", $parameterData);

$munger = $App->html_text_formatter();

$entry_query = $App->login->all_entry_query();

/** @var CONTENT_OBJECT $obj */
$obj = $entry_query->object_at_id($contentObjectId);

$formatted_text = '';

if ($obj != null)
{
  $tag_validator = $App->make_tag_validator (Tag_validator_multi_line);
  $tag_validator->validate ($input);

  $errors = array();
  if (sizeof ($tag_validator->errors))
  {
    $message = 'Input contained errors.';
    $message_type = 'error';

    foreach ($tag_validator->errors as $error)
    {
      $validation_error = new TEXT_VALIDATION_ERROR();
      $validation_error->message = sprintf ($error->message, $error->token->data ());
      $validation_error->line_number = $error->line_number;
      $validation_error->column_start = $error->column;
      $validation_error->column_end = $error->column + strlen ($error->token->data ());

      $errors []= $validation_error;
    }
  }
  else
  {
    // Store changes; only difference from preview mode
    $obj->description = $input;
    $obj->store_as_is();
    
    $formatted_text = $munger->transform($input, $obj);
    $message = 'Data was saved; preview was updated.';
    $message_type = 'info';
  }
}
else
{
  $message = 'Object for that ID was not found.';
  $message_type = 'error';
}

$message = $App->get_begin_message ($message_type) . $message . $App->get_end_message();

$result = array(
  'text' => $formatted_text,
  'errors' => $errors,
  'message' => $message,
  'modified' => $obj->time_modified->as_iso()
);

echo json_encode($result);
