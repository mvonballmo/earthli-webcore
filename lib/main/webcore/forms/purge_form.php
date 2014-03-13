<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

/**
 * Handles purging of {@link OBJECT_IN_FOLDER} (content) objects.
 * The object must be renderable so the delete form can offer a preview of it. Some objects
 * have a state flag that allows them to be marked as deleted without purging from the database.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class PURGE_OBJECT_FORM extends RENDERABLE_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * Show the object's name as a link when displayed?
   * @var boolean
   */
  public $show_object_as_link = true;

  /**
   * Show the previews before the form?
   * @var boolean
   */
  public $show_previews_first = false;
  
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    
    $field = new BOOLEAN_FIELD ();
    $field->id = 'remove_resources';
    $field->caption = 'Remove associated files';
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Delete the given object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $options = $obj->make_purge_options ();
    $this->_configure_purge_options ($options);
    $obj->purge ($options);
  }

  /**
   * Load initial properties from this object.
   * @param OBJECT_IN_FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->add_preview ($obj, $obj->title_as_html (), false);
    $this->set_value ('remove_resources', true);

    $type_info = $this->_object->type_info ();
    $field = $this->field_at ('remove_resources');
    $field->description = '<strong>Uncheck</strong> if this is a duplicate (e.g. another ' . $type_info->singular_title . ' uses the same files).';
  }

  /**
   * Apply form properties to the purge options for the object.
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _configure_purge_options ($options)
  {
    $options->remove_resources = $this->value_for ('remove_resources');
  }

  /**
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
  }
  
  /**
   * Allows descendents to draw options in the standard form.
   * Called from {@link _draw_controls()}.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    if ($this->visible ('remove_resources'))
    {
      $renderer->start_row (' ');
      echo $renderer->check_box_as_html ('remove_resources');
      $renderer->finish_row ();
    }
  }
  
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    if ($this->show_object_as_link)
    {
      $obj_text = $this->_object->title_as_link ();
    }
    else
    {
      $obj_text = $this->_object->title_as_html ();
    }

    $renderer->draw_text_row ('', 'Are you sure you want to purge ' . $obj_text . '?*');
    
    $this->_draw_options ($renderer);
    
    $renderer->draw_text_row ('', '*Purging an object permanently removes it and all contained content (entries, comments, etc.) from the database.', 'notes');

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }
}

?>