<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.4.0
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
   * Outputs the subscription options for the given {@link $obj}.
   * @param AUDITABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $subscriber = $this->login->subscriber ();
    if ($subscriber->email)
    {
      echo '<table class="basic columns left-labels">';

      $kinds = $subscriber->receives_notifications_through ($obj);

      $this->_display($obj, $subscriber, $kinds, $options);

      echo '</table>';
    }
  }

  /**
   * @param SUBSCRIBER $subscriber
   * @param NAMED_OBJECT $obj
   * @param string $page_name
   * @param bool $subscribed
   * @param bool $include_return_url
   * @param string $caption
   */
  protected function show_subscription($subscriber, $obj, $page_name, $subscribed, $include_return_url, $caption)
  {
    if (!$caption)
    {
      $type_info = $obj->type_info ();
      $caption = strtolower ($type_info->singular_title);
    }

    $url = new URL ($obj->home_page());
    $url->replace_name_and_extension($page_name);
    $url->add_argument ('subscribed', !$subscribed);
    $url->add_argument('email', $subscriber->email);

    if ($include_return_url)
    {
      $url->add_argument ('return_url', urlencode($this->env->url(Url_part_no_host)));
    }

    echo '<tr>';
    echo '<th>' . $caption . '</th>';
    echo '<td>' . $obj->title_as_link() . '</td>';
    echo '<td>' . ($subscribed ? 'subscribed' : '<strong>not</strong> subscribed') . '</td>';
    echo '<td><a class="button" href="' . $url->as_html() . '">' . ($subscribed ? 'Unsubscribe' : 'Subscribe') . '</a></td>';
    echo '</tr>';
  }

  /**
   * @param SUBSCRIBER $subscriber
   * @param NAMED_OBJECT $obj
   * @param array[integer] $kinds
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  protected abstract function _display($obj, $subscriber, $kinds, $options);
}

class COMMENT_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * @param SUBSCRIBER $subscriber
   * @param COMMENT $obj
   * @param array[integer] $kinds
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  protected function _display($obj, $subscriber, $kinds, $options)
  {
    $this->show_subscription($subscriber, $obj->parent_folder(), 'subscribe_to_folder.php', in_array(Subscribe_folder, $kinds), true, '');
    $this->show_subscription($subscriber, $obj->entry(), 'subscribe_to_entry.php', in_array(Subscribe_entry, $kinds), true, '');
    $this->show_subscription($subscriber, $obj, 'subscribe_to_comment.php', in_array(Subscribe_comment, $kinds), false, '');
    $this->show_subscription($subscriber, $obj->creator(), 'subscribe_to_user.php', in_array(Subscribe_user, $kinds), true, 'creator');
  }
}

class FOLDER_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * @param SUBSCRIBER $subscriber
   * @param FOLDER $obj
   * @param array[integer] $kinds
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  protected function _display($obj, $subscriber, $kinds, $options)
  {
    $this->show_subscription($subscriber, $obj, 'subscribe_to_folder.php', in_array(Subscribe_folder, $kinds), false, '');
    $this->show_subscription($subscriber, $obj->creator(), 'subscribe_to_user.php', in_array(Subscribe_user, $kinds), true, 'creator');
  }
}

class USER_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * @param SUBSCRIBER $subscriber
   * @param USER $obj
   * @param array[integer] $kinds
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  protected function _display($obj, $subscriber, $kinds, $options)
  {
    $this->show_subscription($subscriber, $obj, 'subscribe_to_user.php', in_array(Subscribe_user, $kinds), false, '');
  }
}

class ENTRY_SUBSCRIPTION_RENDERER extends SUBSCRIPTION_RENDERER
{
  /**
   * @param SUBSCRIBER $subscriber
   * @param ENTRY $obj
   * @param array[integer] $kinds
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  protected function _display($obj, $subscriber, $kinds, $options)
  {
    $this->show_subscription($subscriber, $obj->parent_folder(), 'subscribe_to_folder.php', in_array(Subscribe_folder, $kinds), true, '');
    $this->show_subscription($subscriber, $obj, 'subscribe_to_entry.php', in_array(Subscribe_entry, $kinds), false, '');
    $this->show_subscription($subscriber, $obj->creator(), 'subscribe_to_user.php', in_array(Subscribe_user, $kinds), true, 'creator');
  }
}