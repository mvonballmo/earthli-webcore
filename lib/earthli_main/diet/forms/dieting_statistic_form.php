<?php

require_once ('webcore/forms/unique_object_form.php');

class DIETING_STATISTIC_FORM extends UNIQUE_OBJECT_FORM
{
  var $button = 'Add';
  var $button_icon = '{icons}buttons/add';

  function DIETING_STATISTIC_FORM (&$context)
  {
    UNIQUE_OBJECT_FORM::UNIQUE_OBJECT_FORM ($context);

    $field = new FLOAT_FIELD ();
    $field->id = 'weight';
    $field->title = 'Weight';
    $field->required = TRUE;
    $field->min_value = 100;
    $this->add_field ($field);

    $field = new FLOAT_FIELD ();
    $field->id = 'body_fat';
    $field->title = 'Body fat';
    $field->required = TRUE;
    $field->min_value = 5;
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'date';
    $field->title = 'Date';
    $field->required = TRUE;
    $this->add_field ($field);
  }

  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('weight', $obj->weight);
    $this->set_value ('body_fat', $obj->body_fat);
    $this->set_value ('date', $obj->date);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('date', $this->context->make_date_time ());
  }

  /**
   * Store the form's values to this recipe.
    * @param RECIPE &$obj
    * @access private
    */
  function _store_to_object (&$obj)
  {
    $obj->weight = $this->value_as_text ('weight');
    $obj->body_fat = $this->value_as_text ('body_fat');
    $obj->date = $this->value_for ('date');
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->set_width ('12em');
    $renderer->start ();
    $renderer->draw_text_line_row ('weight');
    $renderer->draw_text_line_row ('body_fat');
    $renderer->draw_date_row ('date');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}

?>