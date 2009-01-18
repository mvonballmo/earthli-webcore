<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/forms/form.php');

/**
 * Base class for all forms that update information for {@link SUBSCRIBER}s.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 */
class SUBSCRIPTION_FORM extends FORM
{
  /**
   * @var string
   */
  var $button = 'Save';
  /**
   * @var string
   */
  var $button_icon = '{icons}buttons/save';
  /**
   * Name of the panel in which the form is displayed.
   * Since these types of forms are displayed within panels, the page has to
   * know which panel the form was submitted from. This value is stored in the
   * form so the correct form is processed.
   * @see SUBCRIPTION_PANEL::check ()
   * @var string
   * @access private
   */
  var $panel_name;

  /**
   * @param APPLICATION &$app Main application.
    * @param SUBSCRIBER &$subscriber Edit subscriptions for this user.
    */
  function SUBSCRIPTION_FORM (&$app)
  {
    FORM::FORM ($app);

    $field = new EMAIL_FIELD ();
    $field->id = 'email';
    $field->title = 'Email';
    $field->visible = FALSE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'panel';
    $field->title = 'Panel';
    $field->visible = FALSE;
    $this->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'ids';
    $field->title = 'Ids';
    $field->min_values = 0;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this user.
   * @param SUBSCRIBER &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('email', $obj->email);
    $this->set_value ('panel', $this->panel_name);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('email', read_var ('email'));
  }
}

/**
 * Provides support for subscribable {@link CONTENT_OBJECT}s.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.0
 */
class CONTENT_OBJECT_SUBSCRIPTION_FORM extends SUBSCRIPTION_FORM
{
  /**
   * Updates the user's entry subscriptions.
   * @param SUBSCRIBER &$obj
   * @access private
   */
  function commit (&$obj)
  {
    $obj->update_subscriptions_for ($this->_sub_type, $this->value_for ('ids'), $this->_type);
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $size = 0;
    
    if ($this->object_exists ())
    {
      $ids = $this->_object->subscribed_ids_for ($this->_sub_type, $this->_type);
      if (! empty ($ids))
      {
        $query = $this->_make_query ();
        $query->restrict_to_ids ($ids);
        $size = $query->size ();
      }
    }

    if ($size)
    {
      $grid = $this->_make_grid ();
      $grid->set_ranges ($size, 1);
      $grid->set_query ($query);
      $grid->items_are_selectable = TRUE;
      $grid->items_are_selected = TRUE;
      $grid->width = '';

      $ctrl_name = $this->js_name ('ids');

      $renderer->width = '';
      $renderer->start ();

      if ($size > 0)
      {
        $buttons [] = $renderer->javascript_button_as_HTML ('Select All', "select_all ($ctrl_name)", '{icons}buttons/select');
        $buttons [] = $renderer->javascript_button_as_HTML ('Clear All', "select_none ($ctrl_name)", '{icons}buttons/close');
        $buttons [] = $renderer->submit_button_as_HTML ();
        $renderer->draw_buttons_in_row ($buttons);

        $renderer->draw_separator ();
        $renderer->draw_error_row ('ids');
      }

      $renderer->start_row ();
      $grid->display ();
      $renderer->finish_row ();

      $renderer->draw_separator ();
      if ($size > 0)
        $renderer->draw_buttons_in_row ($buttons);

      $renderer->finish ();
    }
    else
      echo '<div class="error">You are not subscribed to any ' . strtolower ($this->_type_info->plural_title) . '.</div>';
  }

  /**
   * @return QUERY
   * @access private
   * @abstract
   */
  function _make_query ()
  {
    $this->raise_deferred ('_make_query', 'CONTENT_OBJECT_SUBSCRIPTION_FORM');
  }
  
  /**
   * @return SELECTABLE_GRID
   * @access private
   * @abstract
   */
  function _make_grid ()
  {
    $this->raise_deferred ('_make_grid', 'CONTENT_OBJECT_SUBSCRIPTION_FORM');
  }
  
  /**
   * Used to format type-specific text output.
   * @var TYPE_INFO
   * @access private
   */
  var $_type_info;
  /**
   * Used by {@link ENTRY} subscriptions.
   * @var string 
   * @access private
   */
  var $_type = '';
  /**
   * Type of subscriptions to manage.
   * @var string
   */
  var $_sub_type;
}

?>