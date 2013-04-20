<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/forms/form.php');
require_once ('webcore/gui/layer.php');

/**
 * Create a filter for objects in a WebCore application.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */
class ADD_TO_LIST_FORM extends ID_BASED_FORM
{
  /**
   * @var boolean
   */
  public $controls_visible = true;

  /**
   * @var string
   */
  public $button = 'Go';

  /**
   * @param APPLICATION $app Main application.
   * @param SEARCH $search
   * @param QUERY $search_query
   */
  public function __construct ($app, $search, $search_query)
  {
    parent::__construct ($app);

    $this->_search = $search;
    $this->_search_query = $search_query;
    $search->fields->add_fields ($this);

    foreach ($this->_fields as $field)
    {
      $this->set_visible ($field->id, false);
    }

    $field = new TEXT_FIELD ();
    $field->id = 'type';
    $field->caption = 'Type';
    $field->visible = false;
    $this->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'object_ids';
    $field->caption = 'Objects';
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'selected_only';
    $field->caption = 'Selected only';
    $this->add_field ($field);

    $field = $this->field_at ('id');
    $field->visible = true;
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('type', read_var ('type'));
  }

  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (sizeof ($this->value_for ('object_ids')) == 0)
    {
      $this->record_error (Form_general_error_id, 'Please select at least one object.');
    }
  }

  public function commit ($obj)
  {
    die ('committing form with ' . $this->text_value_for ('object_ids'));
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->start_row ();
      echo $renderer->submit_button_as_html ();

      echo 'Add ';

      $props = $renderer->make_list_properties ();
      $props->add_item ('Entire result set', false);
      $props->add_item ('Selected items', true);
      $props->items_per_row = 3;
      echo $renderer->radio_group_as_html ('selected_only', $props);

      echo ' to ';

      $props = $renderer->make_list_properties ();
      $props->add_item ('New list', 0);
      echo $renderer->drop_down_as_html ('id', $props);
    $renderer->finish_row ();

    $renderer->finish ();

    $grid = $this->_search->grid ();
    $grid->show_folder = true;
    $grid->set_ranges (10, 1);
    $grid->set_query ($this->_search_query);
    $grid->display ();
  }
}

?>