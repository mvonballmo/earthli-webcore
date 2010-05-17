<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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

/**
 * Render details for a {@link JOURNAL}.
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.7.0
 */
class JOURNAL_RENDERER extends ENTRY_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param JOURNAL $entry
   * @access private
   */
  protected function _display_as_html ($entry)
  {
    $this->_echo_subscribe_status ($entry);

    $folder = $entry->parent_folder ();
?>
<div class="info-box-top">
  <div style="float: left; padding-right: 1em">
    <?php echo $entry->weather_icon (); ?>
  </div>
  <div style="float: right; text-align: right">
    <?php echo $folder->format_date ($entry->date); ?>
  </div>
  <div class="field">
    <?php echo $entry->temperature_as_html (); ?>
  </div>
  <div>
    <?php echo $entry->weather_as_html () ?>
  </div>
  <div style="clear:both"></div>
</div>
<?php echo $entry->description_as_html (); ?>
<?php
    $this->_echo_html_user_information ($entry, 'info-box-bottom');
  }

  /**
   * Outputs the object as plain text.
   * @param JOURNAL $entry
   * @access private
   */
  protected function _display_as_plain_text ($entry)
  {
    $props = $entry->weather_icon_properties ();
    echo $this->line ($props->title);
    echo $this->line ($entry->temperature_as_text ());
    echo $this->line ($entry->weather_as_plain_text ());

    $this->_echo_plain_text_description ($entry);

    echo $this->sep ();
    $this->_echo_plain_text_user_information ($entry, 'info-box-bottom');
  }

  /**
   * Outputs the object for print preview.
   * @param JOURNAL $entry
   * @access private
   */
  protected function _display_as_printable ($entry)
  {
    parent::_display_as_printable ($entry);

    if ($this->_options->show_pictures)
    {
      $curr_date = date ('Y-m-d', $entry->date->as_php ());
      if (! isset ($this->last_date) || ! $entry->date->equals ($this->last_date, Date_time_date_part))
      {
        // only show pictures with the first journal of a new day
        
        $this->last_date = $curr_date;
        $pic_query = $entry->picture_query ();
        $num_pics = $pic_query->size ();

        if ($num_pics > 0)
        {
          $class_name = $this->app->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
          $grid = new $class_name ($this->app);
          $grid->description_length = 0;  // no truncation
          $grid->show_controls = false;
          $grid->show_date = false;
          $grid->show_page_breaks = true;
          $grid->set_ranges (100, 3);
          $grid->set_query ($pic_query);
          $grid->display ();
        }
      }
    }
  }
}

/**
 * Render {@link PICTURE}s for a {@link JOURNAL}.
 * Also renders other data with {@link ENTRY_ASSOCIATED_DATA_RENDERER}.
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.9.0
 */
class JOURNAL_ASSOCIATED_DATA_RENDERER extends ENTRY_ASSOCIATED_DATA_RENDERER
{
  /**
   * Draws the list of {@link PICTURE}s.
   * @param JOURNAL $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $pic_query = $obj->picture_query ();
    $num_pics = $pic_query->size ();

    if ($num_pics)
    {
?>
<div class="box-title">
  <?php echo $num_pics; ?> Pictures
</div>
<div class="box-body">
<?php
      $class_name = $this->app->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
      $grid = new $class_name ($this->app);
      $grid->set_ranges (2, 3);
      $grid->set_query ($pic_query);
      $grid->display ();
?>
</div>
<?php
    }
    
    parent::display ($obj, $options);
  }
}

?>