<?php

class TEXT_VALIDATION_ERROR
{
  public $message;
  public $line_number;
  public $column_start;
  public $column_end;
}

$title = read_var ('title');
$description = read_var ('description');
$contentObjectId = read_var ('id');

$newTitle = iconv("UTF-8", "CP1252", $title);
$newDescription = iconv("UTF-8", "CP1252", $description);

$munger = $App->html_text_formatter();

$entry_query = $App->login->all_entry_query();

/** @var CONTENT_OBJECT $obj */
$obj = $entry_query->object_at_id($contentObjectId);

$formatted_text = '';

if ($obj != null)
{
  $tag_validator = $App->make_tag_validator (Tag_validator_multi_line);
  $tag_validator->validate ($newDescription);

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
    $now = new DATE_TIME();
    $now->set_now ();
    $f = $now->formatter ();
    $f->type = Date_time_format_date_and_time;
    $f->clear_flags ();
    $formatted_text = $munger->transform($newDescription, $obj);
    $message = 'Preview updated at ' . $now->format ($f) . '.';
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
