<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/gui/entry_renderer.php');
require_once ('webcore/gui/location_renderer.php');

/**
 * Render details for a {@link PICTURE}.
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.7.0
 */
class PICTURE_RENDERER extends ENTRY_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param PICTURE $entry
   * @access private
   */
  protected function _display_as_html ($entry)
  {
    $this->_echo_subscribe_status ($entry);
    $this->_echo_picture_as_html ($entry);
  }

  /**
   * Outputs the object as HTML.
   * @param PICTURE $entry
   * @access private
   */
  protected function _display_as_plain_text ($entry)
  {
    $folder = $entry->parent_folder ();
    $f = $entry->date->formatter ();
    $f->clear_flags ();
    echo $this->line ($folder->format_date ($entry->date, $f));
    echo $this->line ('<' . $entry->full_file_name () . '>');
    parent::_display_as_plain_text ($entry);
  }

  /**
   * Outputs the object for print-preview.
   * @param PICTURE $entry
   * @access private
   */
  protected function _display_as_printable ($entry)
  {
    $this->_echo_picture_as_html ($entry);
  }

  /**
   * Format the picture as HTML for printing or display.
   * @param PICTURE $entry
   * @access private
   */
  protected function _echo_picture_as_html ($entry)
  {
    $folder = $entry->parent_folder ();
    $metrics = $entry->metrics ();
    if ($metrics->loaded ())
    {
  ?>
  <div style="width: <?php echo $metrics->width (); ?>px">
    <p>
      <?php echo $folder->format_date ($entry->date); ?>
    </p>
    <div>
      <?php $this->_echo_html_description ($entry); ?>
    </div>
    <div>
    <?php
      if ($this->_options->show_interactive)
      {
        echo $metrics->as_html ($entry->title_as_plain_text (), '');
      }
      else
      {
        echo $metrics->as_html_without_link ($entry->title_as_plain_text ());
      }
    ?>
    </div>
    <?php
      if ($this->_options->show_interactive && $metrics->was_resized)
      {
    ?>
    <div class="subdued">
      Resized from
      <?php echo $metrics->original_width; ?> x <?php echo $metrics->original_height; ?> to
      <?php echo $metrics->constrained_width; ?> x <?php echo $metrics->constrained_height; ?>.
      Click to show full size in a separate window.
    </div>
    <?php
      }
      $this->_echo_html_user_information ($entry, 'info-box-bottom');
    ?>
  </div>
  <?php
    }
    else
    {
  ?>
    <div style="text-align: right">
      <?php echo $folder->format_date ($entry->date); ?>
    </div>
  <?php
      $this->_echo_html_description ($entry);
      echo "<div class=\"error\">[$metrics->url] could not be displayed.</div>";
      $this->_echo_html_user_information ($entry, 'info-box-bottom');
    }
  }
}

/**
 * Renders a location for a {@link PROJECT_ENTRY} into a {@link PAGE}.
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 1.9.1
 */
class PICTURE_LOCATION_RENDERER extends OBJECT_IN_FOLDER_LOCATION_RENDERER
{
  /**
   * Render any parent objects to the title and location.
   * @param PAGE $page
   * @param RENDERABLE $obj
   * @access private
   */
  protected function _add_context ($page, $obj)
  {
    parent::_add_context ($page, $obj);

    $calendar = read_var ('calendar');
    $journal = read_var ('journal');
    $first_day = read_var ('first_day');
    $folder = $obj->parent_folder ();

    if ($calendar)
    {
      $this->page->location->append ('Calendar', "view_calendar.php?id=$folder->id");
    }

    if ($journal)
    {
      $jrnl_query = $folder->entry_query ();
      $jrnl = $jrnl_query->object_at_id ($journal);
      if (isset ($jrnl))
      {
        if ($calendar)
        {
          $args = 'calendar=1';
        }
        else
        {
          $args = '';
        }
        $this->page->location->add_object_link ($jrnl, $args);
      }
    }

    if (! $journal && $first_day)
    {
      $day = $this->app->make_date_time ($first_day);
      $url = new URL ($this->env->url (Url_part_no_host_path));
      $url->replace_argument ('id', $folder->id);
      $url->replace_name_and_extension ('view_pictures.php');
      $this->page->location->append ($folder->format_date ($day), $url->as_text ());
    }
  }
}

?>