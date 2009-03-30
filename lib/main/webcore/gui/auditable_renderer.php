<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/gui/object_renderer.php');

/**
 * Render details for {@link AUDITABLE} objects.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */
class AUDITABLE_RENDERER extends OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
  * @param AUDITABLE $obj
  * @access private
  */
  protected function _display_as_html ($obj)
  {
    $this->_echo_html_user_information ($obj);
  }

  /**
   * Shows creator/modifier as a box in HTML.
   * Uses {@link _echo_html_users()} to format its contents.
   * @param AUDITABLE $obj
   * @param string $CSS_class
   * @access private
   */
  protected function _echo_html_user_information ($obj, $CSS_class = 'info-box-top')
  {
    if ($this->_options->show_users)
    {
      echo "<div class=\"$CSS_class\">\n";
      $this->_echo_html_users ($obj);
      echo "</div>\n";
    }
  }

  /**
   * Show created/updated information in HTML.
   * Uses {@link _echo_html_user()} to format users.
   * @param AUDITABLE $obj
   * @access private
   */
  protected function _echo_html_users ($obj)
  {
    $this->_echo_html_user ('Created', $obj->creator (), $obj->time_created);
    if ($obj->modified ())
    {
      $this->_echo_html_user ('Updated', $obj->modifier (), $obj->time_modified);
    }
  }

  /**
   * Display a user and date in HTML.
   * Formatted as: 'caption' by 'user' on 'time'
   * @param string $caption
   * @param USER $user

   *    * @param DATE_TIME $time
   * @access private
   */
  protected function _echo_html_user ($caption, $user, $time)
  {
    echo "<div>\n";
    echo $caption . ' by ' . $user->title_as_link () . ' on ' . $time->format () . "\n";
    echo "</div>\n";
  }

  /**
   * Show a button to toggle subscribe status.
   * @param AUDITABLE $obj
   * @param string $page_name Location of "toggle" url.
   * @param string $kind Can be any of the {@link Subscribe_constants}.
   * @access private
   */
  protected function _echo_html_subscribed_toggle ($obj, $page_name, $kind)
  {
    if ($this->_options->show_interactive)
    {
      $subscriber = $this->login->subscriber ();
      if ($subscriber->email)
      {
        $kinds = $subscriber->receives_notifications_through ($obj);
        
        $obj_type_info = $obj->type_info ();
        $obj_title = strtolower ($obj_type_info->singular_title);
        $directly_subscribed = in_array ($kind, $kinds);
        
        $url = new URL ($page_name);
        $url->add_arguments ('email=' . $subscriber->email . '&subscribed=' . ! $directly_subscribed);
  
        $icon = $this->context->resolve_icon_as_html ('{icons}indicators/subscribed', 'Subscribed', '16px', '');
        if (sizeof ($kinds) > 0)
        {
          if ($directly_subscribed)
          {
            $text = 'You are subscribed to this ' . $obj_title . '. (<a href="' . $url->as_html () . '">Unsubscribe</a>)';
          }
          else
          {
            if (in_array (Subscribe_user, $kinds))
            {
              $creator = $obj->creator ();
              $text = 'You are subscribed to this ' . $obj_title . ' through the user, ' . $creator->title_as_link () . '.';
            }
            else
            {
              $folder = $obj->parent_folder ();
              $folder_type_info = $folder->type_info ();
              $folder_title = strtolower ($folder_type_info->singular_title);
              $text = 'You are subscribed to this ' . $obj_title . ' through its ' . $folder_title . ', ' . $folder->title_as_link () . '.';
            }
          }
        }
        else
        {
          $text = 'You are <strong>not</strong> subscribed to this ' . $obj_title . '. (<a href="' . $url->as_html () . '">Subscribe</a>)';
        }

        echo '<div class="status-indicator"><div style="float: left">' . $icon . '</div><div style="margin-left: 20px">' . $text . '</div></div>';
      }
    }
  }

  /**
   * Outputs the object as plain text.
  * @param AUDITABLE $obj
  * @access private
  */
  protected function _display_as_plain_text ($obj)
  {
    $this->_echo_plain_text_user_information ($obj);
  }

  /**
   * Format created/updated information into a box.
   * Uses {@link _echo_plain_text_users()} to format its contents.
   * @param AUDITABLE $obj
   * @param boolean $top_aligned Shown before the description?
   * @access private
   */
  protected function _echo_plain_text_user_information ($obj, $top_aligned = true)
  {
    if ($this->_options->show_users)
    {
      if (! $top_aligned)
      {
        echo $this->_sep ();
      }

      $this->_echo_plain_text_users ($obj);

      if ($top_aligned)
      {
        echo $this->_line ($this->_sep ());
      }
    }
  }

  /**
   * Show created/updated information in plain text.
   * Uses {@link _echo_plain_text_user()} to format users.
   * @param AUDITABLE $obj
   * @access private
   */
  protected function _echo_plain_text_users ($obj)
  {
    $this->_echo_plain_text_user ('Created', $obj->creator (), $obj->time_created);
    if ($obj->modified ())
    {
      $this->_echo_plain_text_user ('Updated', $obj->modifier (), $obj->time_modified);
    }
  }

  /**
   * Display a user and date in plain text.
   * Formatted as: 'caption' by 'user' on 'time'
   * @param string $caption
   * @param USER $user
   * @param DATE_TIME $time
   * @access private
   */
  protected function _echo_plain_text_user ($caption, $user, $time)
  {
    echo $this->_line ($caption . ' by ' . $user->title_as_plain_text () . ' on ' . $this->_time ($time));
  }
}

?>