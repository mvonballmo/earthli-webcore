<?php

$parameterData = read_var ('inputText');
$contentObjectId = read_var ('id');

$input = iconv("UTF-8", "CP1252", $parameterData);

$munger = $App->html_text_formatter();

$entry_query = $App->login->all_entry_query();
$obj = $entry_query->object_at_id($contentObjectId);

// TODO Add support for:
// - Validate the preview and return errors instead
// - Validation errors
// - Previewing multiple fields

$tag_validator = $App->make_tag_validator (Tag_validator_multi_line);
$tag_validator->validate ($input);
if (sizeof ($tag_validator->errors))
{
  foreach ($tag_validator->errors as $error)
  {
    $msg = sprintf ($error->message, $error->token->data ());
    $name = $this->js_name ();
    $line = $error->line_number;
    $from_col = $error->column;
    $to_col = $error->column + strlen ($error->token->data ());
    $js = "select_line_column_range (document.getElementById ('$name'), $line, $from_col, $line, $to_col)";
    $position = "$this->caption [<a href=\"#\" onclick=\"javascript:$js\">line $line, char $from_col</a>]";
    $form->record_error ($this->id, $position . ' ' . htmlspecialchars ($msg));
  }
}


$formatted_text = $munger->transform($input, $obj);
$errors = '';
$message = $message = $App->get_begin_message ('info') . 'Preview was updated.' . $App->get_end_message();

$result = array(
  'text' => $formatted_text,
  'errors' => $errors,
  'message' => $message
);

echo json_encode($result);
