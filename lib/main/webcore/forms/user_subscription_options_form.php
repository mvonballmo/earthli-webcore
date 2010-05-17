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
require_once ('webcore/forms/subscription_form.php');

/**
 * Presents the standard WebCore subscription options.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class USER_SUBSCRIPTION_OPTIONS_FORM extends SUBSCRIPTION_FORM
{
  /**
   * @var string
   */
  public $button = 'Save';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/save';
  
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new EMAIL_FIELD ();
    $field->id = 'new_email';
    $field->title = 'Email address';
    $field->required = true;
    $field->min_length = 5;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'send_as_html';
    $field->title = 'Format';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'max_individual_messages';
    $field->title = 'Grouping';
    $field->min_value = 1;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'max_items_per_message';
    $field->title = 'Size';
    $field->min_value = 2;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'min_hours_to_wait';
    $field->title = 'Schedule';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'preferred_text_length';
    $field->title = 'Preferred text length';
    $field->min_value = 0;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'text_options';
    $field->title = 'Text';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'group_objects';
    $field->title = 'Grouping';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'split_objects';
    $field->title = 'Size';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'group_history_items';
    $field->title = '&nbsp;';
    $field->description = 'Group history details for an object into one message.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_history_items';
    $field->title = '';
    $field->description = 'Show history details.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_history_item_as_subject';
    $field->title = '';
    $field->description = 'Show history details in the message subject.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'send_own_changes';
    $field->title = 'Content';
    $field->description = 'Send messages triggered by this subscriber.';
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this user.
   * @param SUBSCRIBER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('new_email', $obj->email);
    $this->set_value ('send_as_html', $obj->send_as_html);

    $this->set_value ('max_individual_messages', $obj->max_individual_messages);
    $this->set_value ('max_items_per_message', $obj->max_items_per_message);

    $this->set_value ('min_hours_to_wait', $obj->min_hours_to_wait);

    $this->set_value ('group_objects', $obj->max_individual_messages > 0);
    $this->set_value ('split_objects', $obj->max_items_per_message > 0);

    $this->set_value ('group_history_items', $obj->group_history_items);
    $this->set_value ('show_history_items', $obj->show_history_items);
    $this->set_value ('show_history_item_as_subject', $obj->show_history_item_as_subject);
    $this->set_value ('send_own_changes', $obj->send_own_changes);

    $this->set_value ('text_options', $obj->preferred_text_length > 0);
    $this->set_value ('preferred_text_length', $obj->preferred_text_length);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('new_email', $_REQUEST ['email']);
    $this->set_value ('send_as_html', 1);

    $this->set_value ('max_individual_messages', 3);
    $this->set_value ('max_items_per_message', 25);
    $this->set_value ('min_hours_to_wait', 24);

    $this->set_value ('group_objects', 1);
    $this->set_value ('split_objects', 1);
    $this->set_value ('text_options', 0);
    $this->set_value ('preferred_text_length', 0);

    $this->set_value ('show_history_items', 0);
    $this->set_value ('group_history_items', 1);
    $this->set_value ('show_history_item_as_subject', 0);
    $this->set_value ('send_own_changes', 1);
  }

  /**
   * Called after fields are loaded with data.
   * @param SUBSCRIBER $obj Object from which data was loaded. May be null.
   * @access private
   */
  protected function _post_load_data ($obj) 
  {
    parent::_post_load_data ($obj);
    $enabled = $obj->enabled ();

    $this->set_enabled ('max_individual_messages', ($obj->max_individual_messages > 0) && $enabled);
    $this->set_enabled ('max_items_per_message', ($obj->max_items_per_message > 0) && ($obj->max_individual_messages > 0) && $enabled);
    $this->set_enabled ('text_options', $enabled);
    $this->set_enabled ('group_objects', $enabled);
    $this->set_enabled ('split_objects', $enabled && ($obj->max_individual_messages > 0));
    $this->set_enabled ('send_as_html', $enabled);
    $this->set_enabled ('group_history_items', $enabled && $obj->show_history_items);
    $this->set_enabled ('show_history_items', $enabled);
    $this->set_enabled ('show_history_item_as_subject', $enabled);
    $this->set_enabled ('send_own_changes', $enabled);
    $this->set_enabled ('preferred_text_length', $enabled && ($obj->preferred_text_length > 0));
  }

  /**
   * Called before fields are validated.
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _pre_validate ($obj)
  {
    parent::_pre_validate ($obj);

    if (! $this->value_for ('group_objects'))
    {
      $field = $this->field_at ('max_individual_messages');
      $field->required = false;
      $field->set_value (0);
      $field->min_value = 0;
    }
    $this->set_required ('max_items_per_message', $this->value_for ('split_objects') && $this->value_for ('group_objects'));
  }

  /**
   * Store the form's values for this user's subscription options.
   * @param SUBSCRIBER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $orig_email = $this->value_for ('email');
    $new_email = $this->value_for ('new_email');

    if (! $new_email)
    {
      $obj->email = $orig_email;
      $obj->purge ();
    }
    else
    {
      if ($orig_email)
      {
        /* If the subscriber already exists, make sure to load the full record (so that the existing
           record is updated and no new record is created). */

        $obj->email = $orig_email;
        $obj->synchronize ();
      }

      $obj->email = $new_email;
      $obj->send_as_html = $this->value_for ('send_as_html');

      if ($this->value_for ('group_objects'))
      {
        $obj->max_individual_messages = $this->value_for ('max_individual_messages');
      }
      else
      {
        $obj->max_individual_messages = 0;
      }

      if ($this->value_for ('split_objects'))
      {
        $obj->max_items_per_message = $this->value_for ('max_items_per_message');
      }
      else
      {
        $obj->max_items_per_message = 0;
      }

      $obj->min_hours_to_wait = $this->value_for ('min_hours_to_wait');

      $obj->preferred_text_length = $this->value_for ('preferred_text_length');
      $obj->show_history_items = $this->value_for ('show_history_items');
      $obj->show_history_item_as_subject = $this->value_for ('show_history_item_as_subject');
      $obj->group_history_items = $this->value_for ('group_history_items');
      $obj->send_own_changes = $this->value_for ('send_own_changes');
      $obj->store ();

      if ($orig_email)
      {
        // The subscriber already existed, so see if there is a matching user for this subscriber. If so
        // synchronize the email address in the user record with the new subscriber address.

        $user_query = $this->app->user_query ();
        $user = $user_query->object_at_email ($orig_email);

        if (isset ($user))
        {
          // matching user found for this subscriber

          if ($orig_email != $new_email)
          {
            $user->email = $new_email;
            $user->store ();
          }
        }
      }
    }
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function on_show_history_items (ctrl)
  {
    ctrl.form.group_history_items.disabled = ! ctrl.checked;
  }

  function on_group_objects (ctrl)
  {
    ctrl.form.max_individual_messages.disabled = is_selected (ctrl, 0);
    enable_items (ctrl.form.split_objects, ! is_selected (ctrl, 0));
    ctrl.form.max_items_per_message.disabled = is_selected (ctrl, 0) || is_selected (ctrl.form.split_objects, 0);
  }

  function on_split_objects (ctrl)
  {
    ctrl.form.max_items_per_message.disabled = is_selected (ctrl, 0);
  }

  function on_min_hours_to_wait (ctrl)
  {
    var enabled = (ctrl.value != <?php echo Subscriptions_disabled; ?>);

    enable_items (ctrl.form.send_as_html, enabled);
    enable_items (ctrl.form.text_options, enabled);
    enable_items (ctrl.form.group_objects, enabled);
    enable_items (ctrl.form.split_objects, enabled);

    ctrl.form.max_individual_messages.disabled = ! enabled || is_selected (ctrl.form.group_objects, 0);
    ctrl.form.max_items_per_message.disabled = ! enabled || is_selected (ctrl.form.split_objects, 0);
    ctrl.form.preferred_text_length.disabled = ! enabled || is_selected (ctrl.form.text_options, 0);

    ctrl.form.send_own_changes.disabled = ! enabled;
    ctrl.form.show_history_items.disabled = ! enabled;
    ctrl.form.group_history_items.disabled = ! enabled;
    ctrl.form.show_history_item_as_subject.disabled = ! enabled;
  }

  function on_change_text_option (ctrl)
  {
    ctrl.form.preferred_text_length.disabled = is_selected (ctrl, 0);
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $user_query = $this->app->user_query ();
    $user = $user_query->object_at_email ($this->value_for ('email'));
    if (isset ($user))
    {
      $field = $this->field_at ('new_email');
      $field->description = 'Linked to the email address for ' . $user->title_as_link ();
    }

    $renderer->start ();

    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '20em';
    
    $renderer->draw_text_line_row ('new_email', $options);
    $renderer->draw_separator ();

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'on_min_hours_to_wait (this)';
    $props->add_item ('Never', Subscriptions_disabled);
    $props->add_item ('Immediately', 0);
    $props->add_item ('Once an hour', 1);
    $props->add_item ('Twice per day', 12);
    $props->add_item ('Once per day', 24);
    $props->add_item ('Every 2 days', 48);
    $props->add_item ('Every 3 days', 72);
    $props->add_item ('Once per week', 168);
    $props->add_item ('Once per month', 720);
    $renderer->draw_drop_down_row ('min_hours_to_wait', $props);

    $renderer->draw_separator ();

    $props = $renderer->make_list_properties ();
    $props->add_item ('HTML', 1);
    $props->add_item ('Plain text', 0);
    $renderer->draw_radio_group_row ('send_as_html', $props);

    $renderer->draw_separator ();
    $layer = $renderer->start_layer_row ('advanced', 'Advanced', '%s more options.');

    $options->width = '3em';

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'on_change_text_option (this)';
    $props->smart_wrapping = true;
    $props->add_item ('Send all available text.', 0);
    $props->add_item ('Send at most ', 1, '', true, $renderer->text_line_as_HTML ('preferred_text_length', $options) . ' characters.');
    $renderer->draw_radio_group_row ('text_options', $props);
    $renderer->draw_error_row ('preferred_text_length');

    $renderer->draw_separator ();

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'on_group_objects (this)';
    $props->smart_wrapping = true;
    $props->add_item ('One item per message.', 0);
    $props->add_item ('Group items if there are more than ', 1, '', true, $renderer->text_line_as_HTML ('max_individual_messages', $options) . ' at once.');
    $renderer->draw_radio_group_row ('group_objects', $props);
    $renderer->draw_error_row ('max_individual_messages');

    $renderer->draw_separator ();

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'on_split_objects (this)';
    $props->smart_wrapping = true;
    $props->add_item ('Send only one message.', 0);
    $props->add_item ('Send at most ', 1, '', true, $renderer->text_line_as_HTML ('max_items_per_message', $options) . ' items per message.');
    $renderer->draw_radio_group_row ('split_objects', $props);
    $renderer->draw_error_row ('max_items_per_message');

    $renderer->draw_separator ();

    $renderer->draw_check_box_row ('send_own_changes');
    $check_props = $renderer->make_check_properties ();
    $check_props->on_click_script = 'on_show_history_items (this)';
    $renderer->draw_check_box_row ('show_history_items', $check_props);
    $renderer->start_row (' ');
      $renderer->start_block ();
      $renderer->draw_check_box_row ('group_history_items');
      $renderer->finish_block ();
    $renderer->finish_row ();
    $renderer->draw_check_box_row ('show_history_item_as_subject');

    $renderer->finish_layer_row ($layer);

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->finish ();
  }
}

?>
