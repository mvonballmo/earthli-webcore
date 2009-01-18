<?php

require_once ('webcore/forms/form.php');

class GUEST_SEARCH_FORM extends FORM
{
  var $name = 'search_form';
  var $action = 'search.php';
  var $button = 'Search';

  function GUEST_SEARCH_FORM (&$context)
  {
    FORM::FORM ($context);

    $field = new TEXT_FIELD ();
    $field->id = 'search';
    $field->title = '';
    $field->required = TRUE;
    $field->min_length = 1;
    $field->max_length = 100;
    $this->add_field ($field);
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->show_required_mark = FALSE;
    $renderer->set_width ('20em');
    $renderer->start ();
      $renderer->draw_text_line_row ('search');
      $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}
?>