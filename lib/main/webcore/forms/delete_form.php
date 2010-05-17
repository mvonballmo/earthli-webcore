<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
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
require_once ('webcore/forms/purge_form.php');

/**
 * Handles deletion of {@link NAMED_OBJECT}s.
 * The object needs a name so the form can identify it in the 'are you sure?' text.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class DELETE_FORM extends ID_BASED_FORM
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
   * Delete the given object.
   * @param UNIQUE_OBJECT $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->purge ();
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->start_row ();
?>
  <p>Are you sure you want to delete <?php if ($this->show_object_as_link) echo $this->_object->title_as_link (); else echo $this->_object->title_as_html ();?>?</p>
<?php
    $renderer->finish_row ();
    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);
    $renderer->finish ();
  }
}

/**
 * Handles deletion of {@link OBJECT_IN_FOLDER} (content) objects.
 * The object must be renderable so the delete form can offer a preview of it. Some objects
 * have a state flag that allows them to be marked as deleted without purging from the database.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 */
class DELETE_OBJECT_FORM extends PURGE_OBJECT_FORM
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
   * @param APPLICATION $app Main application.
   * @param string $set_name
   */
  public function __construct ($app, $set_name)
  {
    parent::__construct ($app);

    $this->_privilege_set = $set_name;

    $field = new BOOLEAN_FIELD ();
    $field->id = 'purge';
    $field->title = 'Purge immediately';
    $this->add_field ($field);
  }

  /**
   * Delete the given object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  public function commit ($obj)
  {
    if ($this->value_for ('purge'))
    {
      parent::commit ($obj);
    }
    else
    {
      $obj->delete ();
    }
  }

  /**
   * Load initial properties from this object.
   * @param OBJECT_IN_FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_visible ('purge', $this->_is_purge_visible ($obj));
    $this->set_visible ('remove_resources', $this->_is_purge_visible ($obj));
    $this->set_enabled ('remove_resources', false);

    $type_info = $this->_object->type_info ();
    $field = $this->field_at ('purge');
    if ($this->login->is_allowed ($this->_privilege_set, Privilege_view_hidden, $this->_object))
    {
      $field->description = 'This ' . $type_info->singular_title . ' will be marked as deleted and hidden from non-admin users. Purging removes it permanently and cannot be undone.';
    }
    else 
      $field->description = 'This ' . $type_info->singular_title . ' will be marked as deleted and hidden from you. Purging removes it permanently and cannot be undone.';
  }

  /**
   * Can the user purge objects?
   * @param OBJECT_IN_FOLDER $obj
   * @return boolean
   * @access private
   */
  protected function _is_purge_visible ($obj)
  {
    return $this->login->is_allowed ($this->_privilege_set, Privilege_purge, $obj);
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function on_click_purge (ctrl)
  {
    enable_item (ctrl.form.remove_resources, 0, is_selected (ctrl, 1));
  }
<?php
  }  

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    if ($this->show_object_as_link) 
      $obj_title = $this->_object->title_as_link (); 
    else 
      $obj_title = $this->_object->title_as_html ();
    $renderer->draw_text_row ('', 'Are you sure you want to delete ' . $obj_title . '?');

    if ($this->visible ('purge'))
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
   
      $check_props = $renderer->make_check_properties ('purge');
      $check_props->on_click_script = 'on_click_purge (this)';
      $props->add_item_object ($check_props);
      $props->add_item ('remove_resources', 0);
      $renderer->draw_check_boxes_row ('', $props);
    }
    
    $renderer->draw_separator ();

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }

  /**
   * @var string
   * @access private
   */
  protected $_privilege_set;
}

/**
 * Confirm and purge WebCore objects.
 * Deleting marks a database object as deleted, but does not remove it from the database.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 */
class DELETE_OBJECT_IN_FOLDER_FORM extends DELETE_OBJECT_FORM
{
  /**
   * @param FOLDER $folder Deleting content from this folder.
   * @param string $set_name
   */
  public function __construct ($folder, $set_name)
  {
    parent::__construct ($folder->app, $set_name);
    $this->_folder = $folder;
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}

?>