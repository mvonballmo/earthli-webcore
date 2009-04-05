<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/gui/panel.php');

/**
 * Base class for subscription panels.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 * @abstract
 */
class SUBSCRIPTION_PANEL extends PANEL
{
  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param SUBSCRIBER $subscriber Show subscriptions for this user.
   */
  public function SUBSCRIPTION_PANEL ($manager, $subscriber)
  {
    FORM_PANEL::FORM_PANEL ($manager);
    $this->_subscriber = $subscriber;
  }

  /**
   * All the panel to perform processing.
   * Form-based panels will process their forms and possibly redirect.
   */
  public function check () 
  {
  }

  /**
   * @var SUBSCRIBER
   * @access private
   */
  protected $_subscriber;
}

/**
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 * @abstract
 */
class FORM_BASED_SUBSCRIPTION_PANEL extends SUBSCRIPTION_PANEL
{
  /**
   * Process the form for this panel.
   */
  public function check ()
  {
    $form = $this->form ();
    $form->process_existing ($this->_subscriber);
    if ($form->committed ())
    {
      $this->env->redirect_local ($this->_subscriber->home_page ());
    }
  }

  /**
   * Renders this panel.
   */
  public function display ()
  {
    $form = $this->form ();
    $form->display ();
  }

  /**
   * Creates/returns a form for this panel.
   * @access private
   */
  public function form ()
  {
    if (! isset ($this->_form))
    {
      $this->_form = $this->_make_form ();
      $this->_form->panel_name = $this->id;
    }
    return $this->_form;
  }

  /**
   * @return FORM
   * @access private
   * @abstract
   */
  protected function _make_form () 
  { 
    $this->raise_deferred ('_make_form', 'SUBSCRIPTION_PANEL'); 
  }

  /**
   * @var FORM
   * @access private
   */
  protected $_form;
}

/**
 * Displays {@link FOLDER}s to which one is subscribed.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 */
class FOLDER_SUBSCRIPTION_PANEL extends FORM_BASED_SUBSCRIPTION_PANEL
{
  /**
   * @var string
   */
  public $id = 'folders';

  /**
   * @var string
   */
  public $title = 'Folders';

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param SUBSCRIBER $subscriber Show subscriptions for this user.
   */
  public function FOLDER_SUBSCRIPTION_PANEL ($manager, $subscriber)
  {
    FORM_BASED_SUBSCRIPTION_PANEL::FORM_BASED_SUBSCRIPTION_PANEL ($manager, $subscriber);
    $type_info = $this->app->type_info_for ('FOLDER', 'webcore/obj/folder.php');
    $this->id = $type_info->id;
    $this->title = $type_info->plural_title;
  }

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  /**
   * @return integer
   */
  public function num_objects ()
  {
    return sizeof ($this->_subscriber->subscribed_ids_for (Subscribe_folder));
  }

  /**
   * Renders this panel.
   */
  public function display ()
  {
?>
  <p>You will receive notifications for all activity within subscribed folders. Notifications are sent when a new item is created
    or when an existing item is modified.</p>
<?php
    parent::display ();
  }

  /**
   * @return FOLDER_SUBSCRIPTION_FORM
   * @access private
   */
  protected function _make_form ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_SUBSCRIPTION_FORM', 'webcore/forms/folder_subscription_form.php');
    $Result = new $class_name ($this->app);
    $Result->panel_name = $this->id;
    return $Result;
  }
}

/**
 * Displays {@link ENTRY}s to which one is subscribed.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 */
class ENTRY_SUBSCRIPTION_PANEL extends FORM_BASED_SUBSCRIPTION_PANEL
{
  /**
   * @var string
   */
  public $id = 'entries';

  /**
   * @var string
   */
  public $title = 'Entries';

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param SUBSCRIBER $subscriber Show subscriptions for this user.
   * @param TYPE_INFO $type_info
   */
  public function ENTRY_SUBSCRIPTION_PANEL ($manager, $subscriber, $type_info)
  {
    FORM_BASED_SUBSCRIPTION_PANEL::FORM_BASED_SUBSCRIPTION_PANEL ($manager, $subscriber);
    $this->_type_info = $type_info;
    $this->id = $type_info->id;
    $this->title = $type_info->plural_title;
  }

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  /**
   * @return integer
   */
  public function num_objects ()
  {
    return sizeof ($this->_subscriber->subscribed_ids_for (Subscribe_entry, $this->id));
  }

  /**
   * Renders this panel.
   */
  public function display ()
  {
?>
  <p>You will receive notifications for all activity on subscribed items. Notifications are sent when an item is changed, or when
    a new item is attached (like a comment).</p>
  <p class="notes">You cannot subscribe to items here; subscribe to items from their home pages.</p>
<?php
    parent::display ();
  }

  /**
   * @return ENTRY_SUBSCRIPTION_FORM
   * @access private
   */
  protected function _make_form ()
  {
    $class_name = $this->app->final_class_name ('ENTRY_SUBSCRIPTION_FORM', 'webcore/forms/entry_subscription_form.php', $this->id);
    return new $class_name ($this->app, $this->_type_info);
  }
  
  /**
   * @var TYPE_INFO
   * @access private
   */
  protected $_type_info;
}

/**
 * Displays {@link USER}s to which one is subscribed.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.7.0
 */
class USER_SUBSCRIPTION_PANEL extends FORM_BASED_SUBSCRIPTION_PANEL
{
  /**
   * @var string
   */
  public $id = 'users';

  /**
   * @var string
   */
  public $title = 'Users';

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  /**
   * @return integer
   */
  public function num_objects ()
  {
    return sizeof ($this->_subscriber->subscribed_ids_for (Subscribe_user));
  }

  /**
   * Renders this panel.
   */
  public function display ()
  {
?>
  <p>You will receive notifications for items created by subscribed users. Notifications are 
    sent when an item is changed, or when a new item is attached (like a comment).</p>
  <p class="notes">You cannot subscribe to users here; subscribe to users from their home pages.</p>
<?php
    parent::display ();
  }

  /**
   * @return USER_SUBSCRIPTION_FORM
   * @access private
   */
  protected function _make_form ()
  {
    $class_name = $this->app->final_class_name ('USER_SUBSCRIPTION_FORM', 'webcore/forms/user_subscription_form.php');
    return new $class_name ($this->app);
  }
}

/**
 * Displays subscription options.
 * The {@link SUBSCRIBER} can choose HTML/Plain text email or various other settings.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.4.0
 */
class USER_SUBSCRIPTION_OPTIONS_PANEL extends FORM_BASED_SUBSCRIPTION_PANEL
{
  /**
   * @var string
   */
  public $id = 'prefs';

  /**
   * @var string
   */
  public $title = 'Preferences';

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  /**
   * @return USER_SUBSCRIPTION_OPTIONS_FORM 
   * @access private
   */
  protected function _make_form ()
  {
    $class_name = $this->app->final_class_name ('USER_SUBSCRIPTION_OPTIONS_FORM', 'webcore/forms/user_subscription_options_form.php');
    return new $class_name ($this->app);
  }
}

/**
 * Displays a summary of all subscription information.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.4.0
 */
class SUBSCRIPTION_SUMMARY_PANEL extends SUBSCRIPTION_PANEL
{
  /**
   * @var string
   */
  public $id = 'summary';

  /**
   * @var string
   */
  public $title = 'Summary';

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  public function display ()
  {
?>
  <p>These are settings for <?php echo $this->_subscriber->title_as_html (); ?>.</p>
  <ul>
    <?php
      if (! $this->_subscriber->enabled ())
      {
    ?>
      <li>Messages are <span class="field">disabled</span>.</li>
    <?php
      }
      else
      {
    ?>
    <li>Send messages <span class="field"><?php
      switch ($this->_subscriber->min_hours_to_wait)
      {
      case Subscriptions_disabled:
        echo 'immediately'; break;
      case 0:
        echo 'immediately'; break;
      case 1:
        echo 'once an hour'; break;
      case 12:
        echo 'twice per day'; break;
      case 24:
        echo 'once per day'; break;
      case 48:
        echo 'every 2 days'; break;
      case 72:
        echo 'every 3 days'; break;
      case 168:
        echo 'once per week'; break;
      case 720:
        echo 'once per month'; break;
      default:
        echo 'every ' . $this->_subscriber->min_hours_to_wait . ' hours';
      }
    ?></span> in <span class="field">
    <?php
      if ($this->_subscriber->send_as_html)
      {
        echo 'HTML';
      }
      else
      {
        echo 'Plain text';
      }
    ?></span> format.</li>
    <li>Send <span class="field"><?php
      if (! $this->_subscriber->preferred_text_length)
      {
        echo 'all text';
      }
      else
      {
        echo 'a ' . $this->_subscriber->preferred_text_length . ' character excerpt';
      }
    ?></span> from each message.</li>
    <li>
    <?php
      if ($this->_subscriber->max_individual_messages)
      {
        if ($this->_subscriber->max_items_per_message)
        {
    ?>
      If there are more than
      <span class="field"><?php echo $this->_subscriber->max_individual_messages; ?></span>
      messages, send them in groups of
      <span class="field"><?php echo $this->_subscriber->max_items_per_message; ?></span>.
    <?php
        }
        else
        {
    ?>
      If there are more than
      <span class="field"><?php echo $this->_subscriber->max_individual_messages; ?></span>
      messages, send them <span class="field">in one message</span>.
    <?php
        }
      }
      else
      {
    ?>
      Send each message <span class="field">individually</span>.
    <?php
      }
    ?></li>
    <li><span class="field"><?php
      if ($this->_subscriber->send_own_changes)
      {
        echo 'Send';
      }
      else
      {
        echo 'Do not send';
      }
    ?></span> messages triggered by this subscriber.</li>
    <li><span class="field"><?php
      if ($this->_subscriber->show_history_item_as_subject)
      {
        echo 'Include';
      }
      else
      {
        echo 'Do not include';
      }
    ?></span> history details in message subject.</li>
    <li>
    <?php
      if ($this->_subscriber->show_history_items)
      {
    ?>
      <span class="field">Show</span> history details and
      <?php
        if ($this->_subscriber->group_history_items)
        {
      ?>
      <span class="field">group</span> them with their object.
      <?php
        }
        else
        {
      ?>
          <span class="field">repeat</span> object details for each.
      <?php
        }
      }
      else
      {
    ?>
      <span class="field">Do not show</span> history details.
    <?php
      }
    ?></li>
  </ul>
  <?php
      }
    $url = new URL ($this->env->url (Url_part_all));
    $url->replace_argument ('panel', 'prefs');

    $renderer = $this->context->make_controls_renderer ();
    $button = $renderer->button_as_html ('Change...', $url->as_text (), '{icons}buttons/edit');
    $this->_echo_button_with_description ('', $button);

	$panels = $this->_panel_manager->ordered_panels (Panel_location);
    foreach ($panels as $panel)
    {
      if ($this->_is_summarizable ($panel))
      {
        $url->replace_argument ('panel', $panel->id);
        $desc = 'Subscribed to <span class="field">' . $panel->num_objects () . '</span> ' . $panel->raw_title ();
        $button = $renderer->button_as_html ('Change...', $url->as_text (), '{icons}buttons/edit');
        $this->_echo_button_with_description ($desc, $button);
      }
    }
  ?>
<?php
  }
  
  /**
   * Show a piece of text with a button.
   * @param string $desc
   * @param string $button
   * @access private
   */
  protected function _echo_button_with_description ($desc, $button)
  {
  ?>
  <div>
    <div style="float: right">
  <?php
    echo $button;
  ?>
    </div>
  <?php
    echo $desc
  ?>
  </div>
  <p class="horizontal-separator" style="clear: both">&nbsp;</p>
  <?php
  }

  /**
   * Display a summary of this panel?
   * @var PANEL $panel
   */
  protected function _is_summarizable ($panel)
  {
    return ($panel->id != 'prefs') && ($panel->id != 'summary') && ($panel->id != Empty_panel_id);
  }
}

/**
 * Displays the panels for subscription information.
 * Shows subscribed objects, folders and options panels.
 * @package webcore
 * @subpackage panels
 * @version 3.1.0
 * @since 2.2.1
 */
class SUBSCRIPTION_PANEL_MANAGER extends PANEL_MANAGER
{
  /**
   * @var string
   */
  public $default_panel_id = 'summary';

  /**
   * @param SUBSCRIBER $subscriber Show settings for this subscriber.
   */
  public function SUBSCRIPTION_PANEL_MANAGER ($subscriber)
  {
    $this->_subscriber = $subscriber;
    PANEL_MANAGER::PANEL_MANAGER ($subscriber->app, false);
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    $class_name = $this->app->final_class_name ('SUBSCRIPTION_SUMMARY_PANEL');
    $this->add_panel (new $class_name ($this, $this->_subscriber));
    $class_name = $this->app->final_class_name ('USER_SUBSCRIPTION_OPTIONS_PANEL');
    $this->add_panel (new $class_name ($this, $this->_subscriber));

    $class_name = $this->app->final_class_name ('FOLDER_SUBSCRIPTION_PANEL');
    $this->add_panel (new $class_name ($this, $this->_subscriber));

    $class_name = $this->app->final_class_name ('ENTRY_SUBSCRIPTION_PANEL');
    $type_infos = $this->app->entry_type_infos ();
    foreach ($type_infos as $type_info)
    {
      $this->add_panel (new $class_name ($this, $this->_subscriber, $type_info));
    }

    $class_name = $this->app->final_class_name ('USER_SUBSCRIPTION_PANEL');
    $this->add_panel (new $class_name ($this, $this->_subscriber));
  }

  /**
   * @var SUBSCRIBER
   * @access private
   */
  protected $_subscriber;
}

?>