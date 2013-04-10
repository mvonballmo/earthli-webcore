<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.3.0
 * @since 2.7.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders the subscription status and options for a {@link RENDERABLE} into a {@link PAGE}.
 * @package webcore
 * @subpackage gui
 * @version 3.4.0
 * @since 3.4.0
 * @abstract
 */
abstract class SUBSCRIPTION_RENDERER extends HANDLER_RENDERER
{
  /**
   * Returns a list of commands for this renderer.
   * @return \COMMANDS
   */
  public function make_commands ()
  {
    $Result = new COMMANDS($this->context);

//    if ($this->_comment_query->size () > 1)
//    {
//      $command = $Result->make_command();
//      switch ($this->comment_mode)
//      {
//        case Comment_render_flat:
//          $command->caption = 'Show Threaded';
//          $command->link = $this->_obj->home_page () . "&comment_mode=threaded#comments";
//          break;
//        case Comment_render_threaded:
//          $command->caption = 'Show Flat';
//          $command->link = $this->_obj->home_page () . "&comment_mode=flat#comments";
//          break;
//      }
//    }

    return $Result;
  }
}

class COMMENT_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * Outputs the subscription options for the given {@link $obj}.
   * @param COMMENT $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $subscriber = $this->login->subscriber ();
    if ($subscriber->email)
    {
      $kinds = $subscriber->receives_notifications_through ($obj);

      $obj_type_info = $obj->type_info ();
      $obj_title = strtolower ($obj_type_info->singular_title);

      $directly_subscribed = in_array (Subscribe_comment, $kinds);
      $url = new URL ('subscribe_to_comment.php');
      $url->add_argument('id', $obj->id);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed directly to this ' . $obj_title . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> directly subscribed to this ' . $obj_title . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';

      $creator = $obj->creator ();
      $directly_subscribed = in_array (Subscribe_user, $kinds);
      $url = new URL ('subscribe_to_user.php');
      $url->add_argument('name', $creator->title);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to the creator of this ' . $obj_title . ', ' . $creator->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to the creator of this ' . $obj_title . ', ' . $creator->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';

      $entry = $obj->entry ();
      $entry_type_info = $entry->type_info ();
      $entry_title = strtolower ($entry_type_info->singular_title);
      $directly_subscribed = in_array (Subscribe_entry, $kinds);
      $url = new URL ('subscribe_to_entry.php');
      $url->add_argument('id', $entry->id);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to this ' . $obj_title . ' through its ' . $entry_title . ', ' . $entry->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to this ' . $obj_title . ' through its ' . $entry_title . ', ' . $entry->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';

      $folder = $obj->parent_folder ();
      $folder_type_info = $folder->type_info ();
      $folder_title = strtolower ($folder_type_info->singular_title);
      $directly_subscribed = in_array (Subscribe_folder, $kinds);
      $url = new URL ('subscribe_to_folder.php');
      $url->add_argument('id', $folder->id);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to this ' . $obj_title . ' through its ' . $folder_title . ', ' . $folder->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to this ' . $obj_title . ' through its ' . $folder_title . ', ' . $folder->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';
    }
  }
}

class FOLDER_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * Outputs the subscription options for the given {@link $obj}.
   * @param COMMENT $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $subscriber = $this->login->subscriber ();
    if ($subscriber->email)
    {
      $page_name = 'subscribe_to_folder.php?id=' . $obj->id;
      $kind = Subscribe_folder;
      $kinds = $subscriber->receives_notifications_through ($obj);

      $directly_subscribed = in_array ($kind, $kinds);

      $url = new URL ($page_name);
      $url->add_arguments ('email=' . $subscriber->email . '&subscribed=' . ! $directly_subscribed);

      $folder_type_info = $obj->type_info ();
      $folder_title = strtolower ($folder_type_info->singular_title);

      if ($directly_subscribed)
      {
        $text = 'You are directly subscribed to this ' . $folder_title . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> directly subscribed to this ' . $folder_title . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';

      $creator = $obj->creator ();
      $directly_subscribed = in_array (Subscribe_user, $kinds);
      $url = new URL ('subscribe_to_user.php');
      $url->add_argument('name', $creator->title);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to the creator of this ' . $folder_title . ', ' . $creator->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to the creator of this ' . $folder_title . ', ' . $creator->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';
    }
  }
}

class USER_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * Outputs the subscription options for the given {@link $obj}.
   * @param COMMENT $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $subscriber = $this->login->subscriber ();
    if ($subscriber->email)
    {
      $page_name = 'subscribe_to_user.php?name=' . $obj->title;
      $kind = Subscribe_user;
      $kinds = $subscriber->receives_notifications_through ($obj);

      $directly_subscribed = in_array ($kind, $kinds);

      $url = new URL ($page_name);
      $url->add_arguments ('email=' . $subscriber->email . '&subscribed=' . ! $directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to this user.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to this user.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';
    }
  }
}

class ENTRY_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * Outputs the subscription options for the given {@link $obj}.
   * @param ENTRY $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $subscriber = $this->login->subscriber ();
    if ($subscriber->email)
    {
      $kind = Subscribe_entry;
      $kinds = $subscriber->receives_notifications_through ($obj);

      $obj_type_info = $obj->type_info ();
      $obj_title = strtolower ($obj_type_info->singular_title);

      $directly_subscribed = in_array ($kind, $kinds);
      $url = new URL ('subscribe_to_entry.php');
      $url->add_argument('id', $obj->id);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed directly to this ' . $obj_title . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> directly subscribed to this ' . $obj_title . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';

      $creator = $obj->creator ();
      $directly_subscribed = in_array (Subscribe_user, $kinds);
      $url = new URL ('subscribe_to_user.php');
      $url->add_argument('name', $creator->title);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);
      $href = $url->as_html();

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to the creator of this ' . $obj_title . ', ' . $creator->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to the creator of this ' . $obj_title . ', ' . $creator->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $href . '">' . $caption . '</a></p>';

      $folder = $obj->parent_folder ();
      $folder_type_info = $folder->type_info ();
      $folder_title = strtolower ($folder_type_info->singular_title);
      $directly_subscribed = in_array (Subscribe_folder, $kinds);
      $url = new URL ('subscribe_to_folder.php');
      $url->add_argument('id', $folder->id);
      $url->add_argument('email', $subscriber->email);
      $url->add_argument ('subscribed', !$directly_subscribed);

      if ($directly_subscribed)
      {
        $text = 'You are subscribed to this ' . $obj_title . ' through its ' . $folder_title . ', ' . $folder->title_as_link () . '.';
      }
      else
      {
        $text = 'You are <strong>not</strong> subscribed to this ' . $obj_title . ' through its ' . $folder_title . ', ' . $folder->title_as_link () . '.';
      }

      $caption = $directly_subscribed ? 'Unsubscribe' : 'Subscribe';

      echo '<p>' . $text . ' <a class="button" href="' . $url->as_html() . '">' . $caption . '</a></p>';
    }
  }
}