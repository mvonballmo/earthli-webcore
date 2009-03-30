<?php

/**
 * @copyright Copyright (c) 2002-2007 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 2.8.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2007 Marco Von Ballmoos

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
 * @version 2.8.0
 * @since 2.4.0
 * @abstract
 */
class AUDITABLE_FORM extends RENDERABLE_FORM
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function AUDITABLE_FORM ($app)
  {
    RENDERABLE_FORM::RENDERABLE_FORM ($app);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'publication_state';
    $field->title = 'Notifications';
    $field->add_value (History_item_silent);
    $field->add_value (History_item_needs_send);
    $this->add_field ($field);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'history_item_title';
    $field->title = 'Title';
    $field->description = 'This will be used as the notification title; if none is given, a default is selected based on the changes made.';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'history_item_description';
    $field->title = 'Description';
    $field->description = 'This is a quick description of why the change was made.';
    $this->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = 'time_modified';
    $field->title = 'Last modified';
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
      $this->_history_item = $obj->new_history_item ();
      $this->_adjust_history_item ($this->_history_item);
    }
    parent::attempt_action ($obj);
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
        $renderer = $this->context->make_controls_renderer ();
        $button = $renderer->button_as_html ('Revert', $this->env->url (Url_part_no_args) . '?id=' . $obj->id, '{icons}/buttons/restore');
        $type_info = $obj->type_info ();
        $msg = '<div style="float: right">' . $button . '</div>' .
            '<div style="margin-right: 75px">This ' . strtolower ($type_info->singular_title) . ' has been changed by another user; you cannot save your changes. ' .
            'Reverting <strong>discards</strong> your changes and loads the version saved by the other user.</div>';
        $this->record_error (Form_general_error_id,  $msg);
      }
    }

    parent::_post_validate ($obj);
  }

  /**
   * Configure the history item's properties.
   * Applies form values to the history item.
   * @param HISTORY_ITEM $history_item
   * @access private
   */
  protected function _adjust_history_item ($history_item)
  {
    $history_item->title = $this->value_as_text ('history_item_title');
    $history_item->description = $this->value_as_text ('history_item_description');

    $pub_state = $this->value_for ('publication_state');
    if ($pub_state)
    {
      $history_item->publication_state = $pub_state;
    }
  }

  /**
   * Load initial properties from this object.
   * @param AUDITABLE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('time_modified', $obj->time_modified);
    $this->set_value ('publication_state', History_item_needs_send);
  }

  /**
   * Load initial properties from the object, but store as a new object.
   * @param AUDITABLE $obj
   */
  public function load_from_clone ($obj)
  {
    parent::load_from_clone ($obj);
    $this->set_value ('publication_state', History_item_needs_send);
  }

  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('time_modified', new DATE_TIME ());
    $this->set_value ('publication_state', History_item_needs_send);
  }

  /**
   * Execute the form.
   * The form has been validated and can be executed.
   * @param AUDITABLE $obj
   * @access private
   */
  public function commit ($obj)
  {
    if ($this->cloning ())
    {
      parent::commit ($obj);
    }
    else
    {
      $obj->store_if_different ($this->_history_item);
    }
  }

  /**
   * Draws controls for history item notification.
   *  @access private
   */
  protected function _draw_history_item_controls ($renderer, $show_initially)
  {
    $renderer->draw_separator ();
    $description = 'Change history is stored automatically. %s history options.';
    $layer = $renderer->start_layer_row ('history', 'History', $description);
      $renderer->set_width ('25em');
      $props = $renderer->make_list_properties ();
      $props->add_item ('Publish', History_item_needs_send);
      $props->add_item ('Do not publish', History_item_silent);
      $renderer->draw_radio_group_row ('publication_state', $props);

      $renderer->draw_separator ();
      $renderer->draw_text_line_row ('history_item_title');

      $renderer->draw_separator ();
      $renderer->draw_text_box_row ('history_item_description');

      $renderer->draw_separator ();
      $renderer->draw_submit_button_row ();
      $renderer->restore_width ();
    $renderer->finish_layer_row ($layer);
  }
}

?>