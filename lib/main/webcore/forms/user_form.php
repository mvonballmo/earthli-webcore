<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
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
require_once ('webcore/forms/content_object_form.php');

/**
 * Standard WebCore {@link USER} options.
 * Includes name, signature, urls, email, etc.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */
class USER_FORM extends CONTENT_OBJECT_FORM
{
  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new TEXT_FIELD ();
    $field->id = 'name';
    $field->caption = 'Original name';
    $field->visible = false;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'password1';
    $field->caption = 'Password';
    $field->min_length = $this->app->user_options->minimum_password_length;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'password2';
    $field->caption = 'Confirm Password';
    $field->min_length = $this->app->user_options->minimum_password_length;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new EMAIL_FIELD ();
    $field->id = 'orig_email';
    $field->caption = 'Original email address';
    $field->visible = false;
    $this->add_field ($field);

    $field = new EMAIL_FIELD ();
    $field->id = 'email';
    $field->caption = 'Email address';
    $field->description = 'Allows you to sign up for subscriptions.';
    $this->add_field ($field);
    
    $field = new ENUMERATED_FIELD ();
    $field->add_value (User_email_hidden);
    $field->add_value (User_email_scrambled);
    $field->add_value (User_email_visible);
    $field->id = 'email_visibility';
    $field->caption = '';
    $this->add_field ($field);    

    $field = new TITLE_FIELD ();
    $field->id = 'real_first_name';
    $field->caption = 'First name';
    $this->add_field ($field);

    $field = new TITLE_FIELD ();
    $field->id = 'real_last_name';
    $field->caption = 'Last name';
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'home_page_url';
    $field->caption = 'Home page';
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'picture_url';
    $field->caption = 'Picture';
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'icon_url';
    $field->caption = 'Icon URL';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'signature';
    $field->caption = 'Signature';
    $this->add_field ($field);

    $field = $this->_fields ['title'];
    $field->caption = 'Name';
  }

  /**
   * Load initial properties from this user.
   * @param USER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('name', $obj->title);
    $this->set_value ('orig_email', $obj->email);
    $this->set_value ('email', $obj->email);
    $this->set_value ('real_first_name', $obj->real_first_name);
    $this->set_value ('real_last_name', $obj->real_last_name);
    $this->set_value ('home_page_url', $obj->home_page_url);
    $this->set_value ('picture_url', $obj->picture_url);
    $this->set_value ('icon_url', $obj->icon_url);
    $this->set_value ('signature', $obj->signature);
    $this->set_value ('publication_state', History_item_silent);
    $this->set_value ('email_visibility', $obj->email_visibility);
    
    $this->set_visible ('title', $this->app->user_options->users_can_change_name);
    $this->set_visible ('password1', false);
    $this->set_visible ('password2', false);

    $icon_url = read_var ('icon_url');
    if ($icon_url)
    {
      $this->set_value ('icon_url', $icon_url);
    }
    else
    {
      $this->set_value ('icon_url', $obj->icon_url);
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('publication_state', History_item_silent);
    $this->set_value ('email_visibility', User_email_scrambled);

    $this->set_required ('password1', true);
    $this->set_required ('password2', true);

    $icon_url = read_var ('icon_url');
    if ($icon_url)
    {
      $this->set_value ('icon_url', $icon_url);
    }
  }

  /**
   * Return true to use integrated captcha verification.fu
   * @return boolean
   */
  protected function _captcha_enabled ()
  {
    return $this->login->is_anonymous ();
  }

  /**
   * Called after fields are validated.
   * @param USER $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (sizeof ($this->_errors) == 0)
    {
      if (! $this->object_exists () || $this->app->user_options->users_can_change_name)
      {
        $name = $this->value_for ('title');
        if (empty ($obj->title) || (strcasecmp ($obj->title, $name) != 0))
        {
          // new user or name has been changed

          $user_query = $this->app->user_query ();
          $existing_user = $user_query->object_at_name ($name);

          // see if there is a user with that name

          if ($existing_user)
          {
            $this->record_error ('title', "Someone is already using that name.");
          }
        }
      }

      if (! $obj->exists ())
      {
        $password1 = $this->value_for ('password1');
        $password2 = $this->value_for ('password2');
        if (strcasecmp ($password1, $password2))
        {
          $this->record_error ('password2', "Please make sure the passwords are the same.");
        }
      }
    }
  }

  /**
   * Execute the form.
   * The form has been validated and can be executed.
   * @param USER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $new_user = ! $obj->exists ();

    parent::commit ($obj);

    $orig_email = $this->value_for ('orig_email');
    $new_email = $this->value_for ('email');

    if (! $new_user && ($orig_email != $new_email))
    {
      /* mail has changed, update subscription information. If the mail is now empty,
         remove all subscription information. If the mail has changed, update the
         subscriber record. */

      $class_name = $this->app->final_class_name ('SUBSCRIBER', 'webcore/obj/subscriber.php');
      /** @var SUBSCRIBER $subscriber */
      $subscriber = new $class_name ($this->app);
      $subscriber->email = $orig_email;

      if (! $new_email)
      {
        $subscriber->purge ();
      }
      else
      {
        if ($subscriber->exists ())
        {
          $subscriber->email = $new_email;
          $subscriber->store ();
        }
      }
    }

    // If current user is anonymous, then log in as the newly created user

    if ($new_user && ($this->login->is_anonymous () || $this->login->ad_hoc_login))
    {
      $this->app->log_in ($obj, false);
    }
  }

  /**
   * Store the form's values to this user.
   * @param USER $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    if (! $obj->exists ())
    {
      $obj->set_password ($this->value_as_text ('password1'));
    }

    if ($this->visible ('title'))
    {
      $obj->title = $this->value_as_text ('title');
    }

    $obj->real_first_name = $this->value_as_text ('real_first_name');
    $obj->real_last_name = $this->value_as_text ('real_last_name');
    $obj->email = $this->value_as_text ('email');
    $obj->home_page_url = $this->value_as_text ('home_page_url');
    $obj->picture_url = $this->value_as_text ('picture_url');
    $obj->icon_url = $this->value_as_text ('icon_url');
    $obj->description = $this->value_as_text ('description');
    $obj->signature = $this->value_as_text ('signature');
    $obj->email_visibility = $this->value_for ('email_visibility');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $renderer->draw_text_line_row ('title');
    $renderer->draw_password_row ('password1');
    $renderer->draw_password_row ('password2');

    $renderer->draw_text_line_row ('real_first_name');
    $renderer->draw_text_line_row ('real_last_name');
    $renderer->draw_text_line_row ('email');    

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item ('Keep private', User_email_hidden, 'Do not display this email under any circumstances. Used only for sending subscriptions.');
    $props->add_item ('Show scrambled', User_email_scrambled, 'Email is displayed, but scrambled (e.g. bob [at] network [dot] com)');
    $props->add_item ('Show normally', User_email_visible, 'Email is displayed normally (open to screen-scraping; not recommended)');
    
    $renderer->draw_radio_group_row ('email_visibility', $props);

    if ($this->_captcha_enabled ())
    {
      $this->_draw_captcha_controls ($renderer);
    }

    $renderer->draw_submit_button_row ();

    $layer = $renderer->start_layer_row ('other_options', 'More Options', '%s more user account options');
      $renderer->draw_text_line_row ('home_page_url');
      $renderer->draw_text_line_row ('picture_url');
      $renderer->draw_icon_browser_row ('icon_url');
      $renderer->draw_text_box_row ('signature', 'short-medium');
      $renderer->draw_text_box_row ('description', 'short-medium');
    $renderer->finish_layer_row ($layer);

    $this->_draw_history_item_controls ($renderer);

    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_user;
}