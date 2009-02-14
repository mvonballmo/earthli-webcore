<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/gui/basic_calendar.php');

/**
 * Renders {@link JOURNAL}s and {@link PICTURE}s into a calendar for an {@link ALBUM}.
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 */
class ALBUM_CALENDAR extends BASIC_CALENDAR
{
  /**
   * @var ALBUM
    * @access private
    */
  var $album;
  /**
   * @var array[ALBUM]
    * @access private
    */
  var $_albums;
  /**
   * @var array[JOURNAL]
    * @access private
    */
  var $_journals;
  /**
   * @var array[DATE_TIME]
    * @access private
    */
  var $_pic_dates;

  /**
   * @param ALBUM &$folder Show calendar for this album.
   */
  function ALBUM_CALENDAR (&$album)
  {
    BASIC_CALENDAR::BASIC_CALENDAR ($album->app);

    $this->album =& $album;
    $this->set_ranges ($album->first_day, $album->last_day);
    $this->_albums = $album->sub_folders ();
  }

  /**
   * @param integer $month
    * @param integer $year
    * @access private
    */
  function month_has_content ($month, $year)
  {
    $Result = FALSE;
    $first_day = new DATE_TIME (mktime (0, 0, 0, $month, 1, $year));
    $php_first_day = $first_day->as_php ();
    $php_last_day = mktime (23, 59, 59, $month, $first_day->last_legal_day (), $year);

    if ($this->_journals)
    {
      $jrnl = current ($this->_journals);
      if ($jrnl)
      {
        $php_jrnl_date = $jrnl->date->as_php ();
        $Result = ($jrnl && ($php_first_day <= $php_jrnl_date) && ($php_jrnl_date <= $php_last_day));
      }
    }

    if (! $Result)
    {
      if (isset ($this->_pic_dates))
      {
        $pic_date = current ($this->_pic_dates);
        if ($pic_date)
        {
          $php_pic_date = $pic_date->as_php ();
          $Result = ($pic_date && ($php_first_day <= $php_pic_date) && ($php_pic_date <= $php_last_day));
        }
      }
    }

    if (! $Result)
    {
      $i = 0;
      $c = sizeof ($this->_albums);
      while ($i < $c)
      {
        $album =& $this->_albums [$i];
        if ((($php_first_day <= $album->first_day->as_php ()) && ($album->first_day->as_php () <= $php_last_day)) ||
            (($php_first_day <= $album->last_day->as_php ()) && ($album->last_day->as_php () <= $php_last_day)))
          $Result = TRUE;
        $i++;
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
  function _get_content_for_day ($day, $week, $month, $year)
  {
    $first_day = new DATE_TIME (mktime (0, 0, 0, $month, $day, $year));
    $last_day = new DATE_TIME (mktime (23, 59, 59, $month, $day, $year));
    $php_first_day = $first_day->as_php ();
    $php_last_day = $last_day->as_php ();

    // find the number of pictures for this day

    $num_pics = 0;

    if (isset ($this->_pic_dates))
    {
      $pic_date = current ($this->_pic_dates);

      if ($pic_date)
      {
        $php_pic_date = $pic_date->as_php ();

        while ($pic_date && ($php_first_day <= $php_pic_date) && ($php_pic_date <= $php_last_day))
        {
          $num_pics++;  // just count the number of pictures
          next ($this->_pic_dates);
          $pic_date = current ($this->_pic_dates);
          if ($pic_date)
            $php_pic_date = $pic_date->as_php ();
        }
      }
    }

    // find all the journal entries for this day

    if (isset ($this->_journals))
    {
      $jrnl = current ($this->_journals);

      if ($jrnl)
      {
        $php_jrnl_date = $jrnl->date->as_php ();

        while ($jrnl && ($php_first_day <= $php_jrnl_date) && ($php_jrnl_date <= $php_last_day))
        {
          $jrnls [] = $jrnl;
          next ($this->_journals);
          $jrnl = current ($this->_journals);
          if ($jrnl)
            $php_jrnl_date = $jrnl->date->as_php ();
        }
      }
    }

    // find all of the albums for this day

    $i = 0;
    $c = sizeof ($this->_albums);
    while ($i < $c)
    {
      $album = $this->_albums [$i];
      $d = $album->first_day->as_php ();
      $php_first_album_day = mktime (0, 0, 0, date ("n", $d), date ("j", $d), date ("Y", $d));
      $d = $album->last_day->as_php ();
      $php_last_album_day = mktime (23, 59, 59, date ("n", $d), date ("j", $d), date ("Y", $d));
      if (($php_first_album_day <= $php_first_day) && ($php_last_day <= $php_last_album_day))
        $albums [] = $album;
      $i++;
    }

    if ($num_pics || isset ($jrnls) || isset ($albums))
    {
?>
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="album-day-num"><?php echo $day; ?></td>
          <td style="text-align: right">
          <?php
            if (isset ($jrnls))
            {
              $c = sizeof ($jrnls);
              if ($c)
              {
                $first_jrnl = $jrnls [0];
                if ($first_jrnl)
                  echo $first_jrnl->weather_icon ();
              }
            }
          ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="height: 7px"></td>
        </tr>
        <tr>
          <td style="text-align: center; vertical-align: top" colspan="2">
          <?php

            // output all albums for today

            if (isset ($albums))
            {
              $i = 0;
              $c = sizeof ($albums);
              while ($i < $c)
              {
                $album = $albums [$i];
                $t = $album->title_formatter ();
                $t->set_name ('view_calendar.php');
                $t->text = "[$t->text]";
                echo $album->title_as_link ($t) . '<br>';
                $i++;
              }
            }

            // output all journal entries for today

            if (isset ($jrnls))
            {
              $i = 0;
              $c = sizeof ($jrnls);

              if ($c > 1)
                // too many journal entries to list
              {
                $iso_first_day = $first_day->as_iso ();
                $iso_last_day = $last_day->as_iso ();
          ?>
              <a href="view_journals.php?<?php echo "id={$this->album->id}&amp;first_day=$iso_first_day&amp;last_day=$iso_last_day&amp;calendar=1"; ?>"><?php echo "($c journal entries)"; ?></a>
          <?php
              }
              else
              {
                while ($i < $c)
                {
                  $jrnl = $jrnls [$i];
                  $t = $jrnl->title_formatter ();
                  $t->add_argument ('calendar', '1');
                  $t->add_argument ('journal', $jrnl->id);
                  echo $jrnl->title_as_link ($t);
                  echo "<br>\n";
                  $i++;
                }
              }
            }
          ?>
          </td>
        </tr>
        <?php
          if ($num_pics)
          {
            $iso_first_day = $first_day->as_iso ();
            $iso_last_day = $last_day->as_iso ();
        ?>
        <tr>
          <td colspan="2" style="height: 7px"></td>
        </tr>
        <tr>
          <td style="text-align: center" colspan="2">
            <a href="view_pictures.php?<?php echo "id={$this->album->id}&amp;calendar=1&amp;first_day=$iso_first_day&amp;last_day=$iso_last_day"; ?>"><?php echo "($num_pics pictures)"; ?></a>
          </td>
        </tr>
        <?php
          }
        ?>
      </table>
<?php
    }
  }

  /**
   * @access private
   */
  function _page_changed ()
  {
    $jrnl_query = $this->album->entry_query ();
    $jrnl_query->set_type ('journal');
    $jrnl_query->set_days ("$this->curr_year-$this->curr_month-01", "$this->last_year-$this->last_month-31");
    $this->_journals = $jrnl_query->objects ();

    // get only the dates for the pictures (using the 'raw_output' function)
    // and store all of the dates in 'pic_dates'. The number of pictures for
    // a day are calculated by working through the list as each day is rendered.

    $pic_query = $this->album->entry_query ();
    $pic_query->set_type ('picture');
    $pic_query->set_select ('entry.date');
    $pic_query->set_days ("$this->curr_year-$this->curr_month-01", "$this->last_year-$this->last_month-31");
    $pic_db =& $pic_query->raw_output ();
    if ($pic_db)
    {
      while ($pic_db->next_record ())
        $this->_pic_dates [] = new DATE_TIME ($pic_db->f ('date'), Date_time_iso);
    }
  }
}
?>