<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.4.0
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
 * Form that stores {@link AUDITABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.4.0
 * @abstract
 */
abstract class AUDITABLE_FORM extends RENDERABLE_FORM
{
  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'publication_state';
    $field->caption = 'Notifications';
    $field->add_value (History_item_default);
    $field->add_value (History_item_silent);
    $field->add_value (History_item_needs_send);
    $this->add_field ($field);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'history_item_title';
    $field->caption = 'Title';
    $field->description = 'This will be used as the notification title; if none is given, a default is selected based on the changes made.';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'history_item_description';
    $field->caption = 'Description';
    $field->description = 'This is a quick description of why the change was made.';
    $this->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = 'time_modified';
    $field->caption = 'Last modified';
    $field->visible = false;
    $this->add_field ($field);
    
    $field = new BOOLEAN_FIELD ();
    $field->id = 'update_modifier_on_change';
    $field->caption = 'Update Modifier';
    $field->description = 'Store currently logged-in user as last modifier; turn off to maintain the existing user as modifier.';
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Add this object to be previewed in the form.
   * Uses {@link FORM_PREVIEW_SETTINGS} to store these settings.
   * @param AUDITABLE $obj
   * @param string $title
   * @param boolean $visible
   */
  public function add_preview ($obj, $title, $visible = true)
  {
    if (! $obj->exists ())
    {
      $obj->modifier_id = $this->app->login->id;
      $obj->creator_id = $this->app->login->id;
    }
    parent::add_preview ($obj, $title, $visible);
  }

  /**
   * Try to apply the values in this form to 'obj'.
   * Since the object is auditable, make a copy of it here so that any changes
   * made during validation or the commit process do not affect the original.
   * @param AUDITABLE $obj Store the form values to this object.
   */
  public function attempt_action ($obj)
  {
    if (! $this->previewing ())
    {
      if ($this->cloning())
      {
        $obj->initialize_as_new();
      }
      
      $this->_history_item = $obj->new_history_item ();
    }
    
    parent::attempt_action ($obj);
  }

  /**
   * Set the internal object of the form.
   * If the object is being created or cloned, it is not set by default.
   * @param object $obj
   * @param string $load_action
   * @access private
   */
  protected function _set_object ($obj, $load_action)
  {
    parent::_set_object($obj, $load_action);

    $this->_history_item = $obj->new_history_item ();
  }

  /**
   * Called after fields are validated.
   * Check whether the object has changed since this form was loaded. If it has, do not update
   * the object with this form's contents (else it will overwrite newly-stored changes).
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    if ($obj->exists ())
    {
      $time_modified = $this->value_for ('time_modified');
      if (! $obj->time_modified->equals ($time_modified))
      {
        $type_info = $obj->type_info ();
        $msg =
          '<p>This ' . strtolower ($type_info->singular_title) . ' has been changed by another user; you cannot save your changes. ' .
          'Please cancel this form or reload the page and try applying the changes again.</p>';
        $this->allow_cancel_only = true;
        $this->record_error (Form_general_error_id,  $msg);
      }
    }

    parent::_post_validate ($obj);
  }

  /**
   * Configure the history item's properties.
   * Applies form values to the history item and adjusts the publication state 
   * depending on the state of the object.
   * 
   * @param AUDITABLE $obj The object to be stored.
   * @param HISTORY_ITEM $history_item The history item to be stored.
   * @access private
   */
  protected function _adjust_history_item ($obj, $history_item)
  {
    $history_item->title = $this->value_as_text ('history_item_title');
    $history_item->description = $this->value_as_text ('history_item_description');

    $pub_state = $this->value_for ('publication_state');
    switch ($pub_state)
    {
      case History_item_default:
        $history_item->publication_state = History_item_queued;
        break;
      default:
        $history_item->publication_state = $pub_state;
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('time_modified', new DATE_TIME ());
    $this->set_value ('publication_state', History_item_default);
    $this->set_value ('update_modifier_on_change', true);
  }

  /**
   * Load initial properties from this object.
   * @param AUDITABLE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('time_modified', $obj->time_modified);
    
    if (isset($this->_history_item))
    {
      $this->_history_item->record_differences ($obj);
      $this->_history_item->prepare_for_storage ();
      if (empty ($this->_history_item->system_description))
      {
        $this->_history_item->system_description = 'No changes.';
      }
      $this->add_preview ($this->_history_item, 'Modifications', $this->previewing());
    }
  }

  /**
   * Execute the form.
   * The form has been validated and can be executed.
   * @param AUDITABLE $obj
   * @access private
   */
  public function commit ($obj)
  {
    $this->_adjust_history_item ($obj, $this->_history_item);
    $obj->store_if_different ($this->_history_item);
  }

  /**
   * Store the form's values to this object.
   * @param AUDITABLE $obj
   * @access private
   * @abstract
   */
  protected function _store_to_object ($obj)
  {
    $obj->update_modifier_on_change = $this->value_for ('update_modifier_on_change');
  }

  /**
   * Draws controls for history item notification.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_history_item_controls ($renderer)
  {
    $description = 'Change history is stored automatically. %s history options.';
    $layer = $renderer->start_layer_row ('history', 'History', $description, false, 'full-screen-optional');
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
      $props->add_item ('Default', History_item_default, 'Let the system decide whether to send notifications for this change.');
      $props->add_item ('Publish', History_item_needs_send, 'Send notifications for this change.');
      $props->add_item ('Do not publish', History_item_silent, 'Do not send notifications for this change');
      $renderer->draw_radio_group_row ('publication_state', $props);

      $renderer->draw_text_line_row ('history_item_title');
      $renderer->draw_text_box_row ('history_item_description', 'short-medium');

      $renderer->draw_submit_button_row ();
    $renderer->finish_layer_row ($layer);
  }
  
  /**
   * Reference to the history item stored with the main object.
   *
   * @var HISTORY_ITEM
   */
  protected $_history_item;
}