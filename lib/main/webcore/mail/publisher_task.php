<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/sys/task.php');

/**
 * Connects the publiser to a page-wide task.
 * Use this class to execute the publisher in a way that channels output to
 * a web page.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class PUBLISHER_TASK extends TASK
{
  /**
   * @var string
   */
  public $page_title = 'Publish Queued Content';

  /**
   * Log all messages in this channel.
   * @var string
   */
  public $log_channel = Msg_channel_publisher;

  /**
   * Shows generated mails on the fly if set.
   * @var boolean
   */
  public $preview = false;

  /**
   * Publish only the given entry history item type, if set.
   * Can contain any of the {@link History_item_state_constants}.
   * @var array[string]
   */
  public $entry_filter;

  /**
   * Publish only the given entry history item type, if set.
   * Can contain any of the {@link History_item_state_constants}.
   * @var array[string]
   */
  public $comment_filter;

  /**
   * Icon to show in the title bar when executing.
   * @var string
   */
  public $icon = '{icons}indicators/published';

  /**
   * Return a formatted title for this task.
   * @return string
   */
  public function title_as_text ()
  {
    return 'Publish Notifications for ' . $this->app->title;
  }

  /**
   * Return a form to display options and execute this task.
   * @return FORM
   */
  public function form ()
  {
    $class_name = $this->context->final_class_name ('EXECUTE_PUBLISHER_TASK_FORM', 'webcore/forms/execute_publisher_task_form.php');
    return new $class_name ($this->context);
  }

  /**
   * Run the task.
   * Log in as the publishing user, then call {@link _publish()}.
   * @access private
   */
  protected function _execute ()
  {
    /* Log in as a user that can see all objects that need sending
       If the impersonation fails, it throws an exception. */

    $opts = $this->app->mail_options;
    $this->app->impersonate ($opts->publisher_user_name, $opts->publisher_user_password);
    $publisher = $this->app->make_publisher ();
    $publisher->testing = $this->testing;
    $publisher->preview = $this->preview;
    $publisher->default_channel = $this->log_channel;
    $logger = $publisher->logs->logger;
    if (isset ($logger))
    {
      $this->env->logs->add_logger ($logger);
    }
    $publisher->logs->set_logger ($this->env->logs->logger);
    $this->_publish ($publisher);
  }

  /**
   * Overridable for descendents.
   * @param PUBLISHER $publisher
   * @access private
   */
  protected function _publish ($publisher)
  {
    $history_item_query = $this->login->all_history_item_query ();
    if ($this->entry_filter || $this->comment_filter)
    {
      $filters = array ();

/*
      $entry_types = array ();
      $entry_type_infos = $this->app->entry_type_infos ();
      if (sizeof ($entry_type_infos) > 1)
      {
        foreach ($entry_type_infos as $type_info)
        {
          $entry_types [] = $type_info->id;
        }
      }
      else
      {
        $entry_types [] = History_item_entry;
      }
*/
      /* The code above would work if the individual history items actually used
       * different types for their history items, which it seems they do not.
       * The line below works for all applications.
       */
      $entry_types [] = History_item_entry;

      $entry_types = "'" . implode ("', '", $entry_types) . "'";
      if (! empty ($this->entry_filter))
      {
        $kinds = "'" . implode ("', '", $this->entry_filter) . "'";
        $filters [] = "act.object_type IN ($entry_types) AND act.kind IN ($kinds)";
      }
      else
      {
        $filters [] = "act.object_type IN ($entry_types)";
      }

      if (! empty ($this->comment_filter))
      {
        $kinds = "'" . implode ("', '", $this->comment_filter) . "'";
        $filters [] = "act.object_type = '" . History_item_comment . "' AND act.kind IN ($kinds)";
      }
      else
      {
        $filters [] = "act.object_type = '" . History_item_comment . "'";
      }

      $filters [] = "act.object_type IN ('" . History_item_folder . "') AND act.kind IN ('Created')";

      $history_item_query->restrict_to_one_of ($filters);
    }
    $publisher->publish_history_items ($history_item_query);
  }
}

?>