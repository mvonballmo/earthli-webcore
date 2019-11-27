<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.6.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once('webcore/gui/basic_calendar.php');

/**
 * Renders {@link JOURNAL}s and {@link PICTURE}s into a calendar for an {@link ALBUM}.
 * @package albums
 * @subpackage gui
 * @version 3.6.0
 * @since 2.5.0
 */
class ALBUM_CALENDAR extends BASIC_CALENDAR
{
  /**
   * @var ALBUM
   * @access private
   */
  public $album;

  /**
   * @param ALBUM $album Show calendar for this album.
   */
  public function __construct($album)
  {
    parent::__construct($album->app);

    $this->album = $album;
    $this->set_ranges($album->first_day, $album->last_day);
    $this->_albums = $album->sub_folders();
  }

  /**
   * @param integer $month
   * @param integer $year
   * @return bool
   * @access private
   */
  public function month_has_content($month, $year)
  {
    $Result = false;
    $first_day = new DATE_TIME (mktime(0, 0, 0, $month, 1, $year));
    $last_day = new DATE_TIME (mktime(23, 59, 59, $month, $first_day->last_legal_day(), $year));

    $journal = current($this->_journals);
    if ($journal)
    {
      $Result = $journal->date->between($first_day, $last_day);
    }

    if (!$Result)
    {
      $picture = current($this->_pictures);
      if ($picture)
      {
        $Result = $picture->date->between($first_day, $last_day);
      }
    }

    if (!$Result)
    {
      foreach ($this->_albums as $album)
      {
        $Result = $album->first_day->between($first_day, $last_day, Date_time_date_part) || $album->last_day->between($first_day, $last_day, Date_time_date_part);

        if ($Result)
        {
          break;
        }

        $Result = $first_day->between($album->first_day, $album->last_day, Date_time_date_part) || $last_day->between($album->first_day, $album->last_day, Date_time_date_part);
        if ($Result)
        {
          break;
        }
      }
    }

    return $Result;
  }

  /**
   * @param integer $day
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  protected function _get_content_for_day($day, $week, $month, $year)
  {
    $first_day = new DATE_TIME (mktime(0, 0, 0, $month, $day, $year));
    $last_day = new DATE_TIME (mktime(23, 59, 59, $month, $day, $year));

    // find the number of pictures for this day

    /** @var PICTURE[] $pictures */
    $pictures = null;

    if (isset ($this->_pictures))
    {
      $picture = current($this->_pictures);

      if ($picture)
      {
        while ($picture && $picture->date->between($first_day, $last_day))
        {
          $pictures [] = $picture;
          $picture = next($this->_pictures);
        }
      }
    }

    // find all the journal entries for this day

    /** @var JOURNAL[] $journal_entries */
    $journal_entries = null;

    if (isset ($this->_journals))
    {
      $journal = current($this->_journals);

      if ($journal)
      {
        while ($journal && $journal->date->between($first_day, $last_day))
        {
          $journal_entries [] = $journal;
          $journal = next($this->_journals);
        }
      }
    }

    // find all of the albums for this day

    /** @var ALBUM[] $albums */
    $albums = null;

    foreach ($this->_albums as $album)
    {
      if ($first_day->between($album->first_day, $album->last_day, Date_time_date_part) || $last_day->between($album->first_day, $album->last_day, Date_time_date_part))
      {
        $albums [] = $album;
      }
    }

    if (isset($pictures) || isset ($journal_entries) || isset ($albums))
    {
      if (isset ($journal_entries) && !empty($journal_entries))
      {
        $first_journal = $journal_entries [0];
        $journal_props = $first_journal->weather_icon_properties();
        ?>
        <div class="align-right">
          <?php
          echo $this->context->get_icon_with_text($journal_props->icon, Thirty_px, '');
          ?>
        </div>
      <?php
      }
      ?>
      <div class="day-number"><?php echo $day; ?></div>
      <div class="day-content">
        <?php

        if (isset ($albums))
        {
          foreach ($albums as $album)
          {
            $t = $album->title_formatter();
            $t->set_name('view_calendar.php');
            $t->text = "[$t->text]";
            echo $album->title_as_link($t);
          }
        }

        if (isset ($journal_entries))
        {
          if (count($journal_entries) > 1)
          {
            $iso_first_day = $first_day->as_iso();
            $iso_last_day = $last_day->as_iso();
            $num_journals = count($journal_entries);
            ?>
            <a href="view_journals.php?<?php echo "id={$this->album->id}&amp;first_day=$iso_first_day&amp;last_day=$iso_last_day&amp;calendar=1"; ?>"><?php echo "$num_journals journals"; ?></a>
          <?php
          }
          else
          {
            $journal = $journal_entries [0];
            $t = $journal->title_formatter();
            $t->add_argument('calendar', '1');
            $t->add_argument('journal', $journal->id);
            echo $journal->title_as_link($t);
          }
        }

        if (isset ($pictures))
        {
          if (count($pictures) > 1)
          {
            $iso_first_day = $first_day->as_iso();
            $iso_last_day = $last_day->as_iso();
            $num_pics = count($pictures);

            $key_photo_for_day = $pictures[0];

            foreach ($pictures as $p)
            {
              if ($p->is_key_photo_for_day)
              {
                $key_photo_for_day = $p;

                break;
              }
            }

            $url = $key_photo_for_day->thumbnail_location (Force_root_on);
            $is_local = $url->has_local_domain ();
            $metrics = $key_photo_for_day->thumbnail_metrics ($is_local);
            if ($is_local)
            {
              $metrics->resize_to_fit (100, 75);
            }
            else
            {
              $metrics->resize (100, 75);
            }

            $t = $key_photo_for_day->title_formatter();
            $t->add_argument('calendar', '1');
            $t->add_argument('first_day', $iso_first_day);
            $t->add_argument('last_day', $iso_last_day);

            echo '<a href="' . $t->as_url() . '">' . $metrics->as_html_without_link ($key_photo_for_day->title_as_plain_text ()) . '</a>';

            ?>
            <a href="view_pictures.php?<?php echo "id={$this->album->id}&amp;calendar=1&amp;first_day=$iso_first_day&amp;last_day=$iso_last_day"; ?>"><?php echo "$num_pics pictures"; ?></a>
          <?php
          }
          else
          {
            $picture = $pictures [0];

            $url = $picture->thumbnail_location (Force_root_on);
            $is_local = $url->has_local_domain ();
            $metrics = $picture->thumbnail_metrics ($is_local);
            if ($is_local)
            {
              $metrics->resize_to_fit (100, 75);
            }
            else
            {
              $metrics->resize (100, 75);
            }

            $t = $picture->title_formatter();
            $t->add_argument('calendar', '1');

            echo '<a href="' . $t->as_url() . '">' . $metrics->as_html_without_link ($picture->title_as_plain_text ()) . '</a>';

            echo $picture->title_as_link($t);
          }
        }
        ?>
      </div>
    <?php
    }
  }

  /**
   * Called when the page is set before rendering.
   *
   * This calendar caches the pictures and journals for the new page.
   *
   * @access private
   */
  protected function _page_changed()
  {
    $first_day = new DATE_TIME (mktime(0, 0, 0, $this->_curr_month, 1, $this->_curr_year));
    $last_day = new DATE_TIME (mktime(23, 59, 59, $this->_curr_month, $first_day->last_legal_day(), $this->_curr_year));

    $journal_query = $this->album->entry_query();
    $journal_query->set_type('journal');
    $journal_query->set_days($first_day->as_iso(), $last_day->as_iso());
    $this->_journals = $journal_query->objects();

    $picture_query = $this->album->entry_query();
    $picture_query->set_type('picture');
    $picture_query->set_days($first_day->as_iso(), $last_day->as_iso());
    $this->_pictures = $picture_query->objects();
  }

  /**
   * @var ALBUM[]
   * @access private
   */
  protected $_albums;

  /**
   * @var JOURNAL[]
   * @access private
   */
  protected $_journals;

  /**
   * @var PICTURE[]
   * @access private
   */
  protected $_pictures;
}

?>