<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/forms/renderable_form.php');
require_once ('webcore/gui/layer.php');

/**
 * Create a filter for {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 */
class STORE_SEARCH_FORM extends RENDERABLE_FORM
{
  /**
   * @var boolean
   */
  public $controls_visible = true;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app, $fields)
  {
    parent::__construct ($app);

    $this->_search_fields = $fields;
    $this->_search_fields->add_fields ($this);

    $field = new TEXT_FIELD ();
    $field->id = 'type';
    $field->title = 'Type';
    $field->visible = false;
    $this->add_field ($field);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'search_title';
    $field->title = 'Title';
    $field->required = true;
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'search_description';
    $field->title = 'Description';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->_search_fields->store_to_object ($this, $this->_object);
    $this->set_value ('type', read_var ('type'));
  }

  /**
   * Load initial properties from this branch.
   * @param BRANCH $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('search_title', $obj->title);
    $this->set_value ('search_description', $obj->description);

    $this->_search_fields->load_from_object ($this, $obj);
  }

  /**
   * Store the form's values to this object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->title = $this->value_as_text ('search_title');
    $obj->description = $this->value_as_text ('search_description');
    $obj->type = $this->value_for ('type');
    $obj->user_id = $this->login->id;
    $this->_search_fields->store_to_object ($this, $obj);
  }

  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);
    $this->_search_fields->validate ($this, $obj);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $layer = $renderer->start_layer_row ('parameters', 'Parameters', '%s fields for this search.');
      $renderer->draw_separator ();
      $this->_search_fields->draw_fields ($this, $renderer);
    $renderer->finish_layer_row ($layer);

    $renderer->draw_text_line_row ('search_title');
    $renderer->draw_text_box_row ('search_description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->finish ();
  }
}

?>