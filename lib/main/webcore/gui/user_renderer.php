<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
 * @since 2.5.0
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link USER}s.
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
 * @since 2.5.0
 */
class USER_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param USER $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    if ($this->_options->show_as_summary)
    {
      if ($obj->picture_url)
      {
        $class_name = $this->app->final_class_name ('IMAGE_METRICS', 'webcore/util/image.php');
        /** @var $metrics IMAGE_METRICS */
        $metrics = new $class_name ();
        $metrics->set_url ($this->context->resolve_file ($obj->picture_url, Force_root_on));
        $metrics->resize_to_fit (200, 200);
        echo '<p>' . $metrics->as_html ('Picture', '') . '</p>';
      }
      else if ($obj->icon_url)
      {
        echo '<p>';
        echo $obj->icon_as_html ('50px');
        echo '</p>';
      }

      $this->_echo_properties_as_html ($obj);
    }
    else
    {
      if ($obj->picture_url)
      {
        ?>
        <img src="<?php echo $obj->full_picture_url (); ?>" alt="Picture">
      <?php
      }

      $this->_echo_properties_as_html ($obj);

      $this->_echo_html_user_information ($obj, 'info-box-bottom');
    }
  }
  
  /**
   * Show the main properties of a user.
   * @param USER $obj
   * @access private
   */
  protected function _echo_properties_as_html ($obj)
  {
?>
  <table class="basic columns left-labels">
    <tr>
      <th>Name</th>
      <td><?php echo $obj->real_name (); ?></td>
    </tr>
    <tr>
      <th>Member since</th>
      <td><?php echo $obj->time_created->format (); ?></td>
    </tr>
    <tr>
      <th>Email</th>
      <td><?php echo $obj->email_as_text (); ?></td>
    </tr>
    <tr>
      <th>Home page</th>
      <td>
        <?php
        if ($obj->home_page_url)
        {
          $t = $obj->title_formatter ();
          $t->text = $obj->home_page_url;
          $t->location = ensure_has_protocol($obj->home_page_url, "http");
          $t->CSS_class = '';
          echo $t->as_html_link ();
        }
        else
        {
          echo "(none)";
        }
        ?>
      </td>
    </tr>
    <tr>
      <th>Description</th>
      <td>
        <?php
        if ($obj->description)
        {
          echo $obj->description_as_html ();
        }
        else
        {
          echo "(none)";
        }
        ?>
      </td>
    </tr>
  </table>
<?php
  }

  /**
   * Shows the subscription status for this object.
   * @param USER $obj
   * @access private
   */
  protected function _echo_subscribe_status ($obj)
  {
    $this->_echo_html_subscribed_toggle ($obj, 'subscribe_to_user.php?name=' . $obj->title, Subscribe_user);
  }

  /**
   * Outputs the object as plain text.
   * @param USER $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    parent::display_as_plain_text ($obj);
  }
}

?>