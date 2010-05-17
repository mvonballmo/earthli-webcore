<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.3.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * @version 3.3.0
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
    $this->_echo_subscribe_status ($obj);
    $main_style = '';
    if ($obj->icon_url)
    {
      $main_style .= ' margin-left: 60px';
      echo '<div style="float: left; margin-right: 10px">';
      echo $obj->icon_as_html ('50px');
      echo '</div>';
    }
    
    if ($obj->picture_url && ! $this->_options->show_as_summary)
    {
      $class_name = $this->app->final_class_name ('IMAGE_METRICS', 'webcore/util/image.php');
      $metrics = new $class_name ();
      $metrics->set_url ($this->context->resolve_file ($obj->picture_url, Force_root_on));
      $metrics->resize_to_fit (200, 150);
?>
     <div style="float: right"><?php echo $metrics->as_html ('Picture'); ?></div>
<?php 
    }
    
    if ($main_style)
    {
      echo '<div style="' . $main_style . '">';
    }
      
    $this->_echo_properties_as_html ($obj);
    
    if ($obj->description)
    {
      echo $obj->description_as_html ();
    }

    if ($obj->signature)
    {
      echo $obj->signature_as_html ();
    }

    if ($main_style)
    {
      echo '</div>';
    }

    $this->_echo_html_user_information ($obj, 'info-box-bottom');
    
    if ($obj->picture_url && $this->_options->show_as_summary)
    {
?>
    <img class="frame" src="<?php echo $obj->full_picture_url (); ?>" alt="Picture">
<?php 
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
  <dl>
    <dt class="field">
      Name
    </dt>
    <dd>
      <?php echo $obj->real_name (); ?>
    </dd>
    <dt class="field">
      Registered On
    </dt>
    <dd>
      <?php echo $obj->time_created->format (); ?>
    </dd>
    <dt class="field">
      Email
    </dt>
    <dd>
      <?php
        echo $obj->email_as_text ();
      ?>
    </dd>
    <dt class="field">
      Home Page
    </dt>
    <dd>
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
    </dd>
    <?php
      if ($this->_options->show_as_summary)
      {
    ?>
    <dt class="field">
      Description
    </dt>
    <dd>
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
    </dd>
    <dt class="field">
      Signature
    </dt>
    <dd>
      <?php
        if ($obj->signature)
        {
          echo $obj->signature_as_html ();
        }
        else
        {
          echo "(none)";
        }
      ?>
    </dd>
    <?php
      }
    ?>
  </dl>
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