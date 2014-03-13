<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/gui/calendar.php');

/**
 * Implements the calendar interface with HTML tables.
 * @package webcore
 * @subpackage gui
 * @version 3.5.0
 * @since 2.2.1
 */
class BASIC_CALENDAR extends CALENDAR
{
  /**
   * Render a day of the month.
   * @param integer $day
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function make_day ($day, $week, $month, $year)
  {
    $noon = new DATE_TIME (mktime (12, 0, 0, $month, $day, $year));

    if (($this->first_day->less_than_equal ($noon)) && ($noon->less_than_equal ($this->last_day)))
    {
      // this day is part of this calendar
      
      ob_start ();
        $this->_get_content_for_day ($day, $week, $month, $year);
        $content = ob_get_contents ();
      ob_end_clean ();

      if (isset ($content) && strlen ($content) > 0)
      {
?>
      <td class="cell-highlight">
        <?php echo $content; ?>
      </td>
<?php
      }
      else
      {
?>
      <td class="cell-selected">
        <?php echo $day; ?>
      </td>
<?php
      }
    }
    else
    {
?>
      <td class="cell-non-empty">
        <?php echo $day; ?>
      </td>
<?php
    }
  }

  /**
   * A new week is beginning.
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function start_week ($week, $month, $year)
  {
    echo "<tr>\n";
  }

  /**
   * A week has just completed.
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function finish_week ($week, $month, $year)
  {
    echo "</tr>\n";
  }

  /**
   * A new month is beginning.
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function start_month ($month, $year)
  {
    $month = date ("F", mktime (0, 0, 0, $month, 1, $year));
    echo "<tr><td colspan=\"" . Days_in_week . "\">";
    if ($this->_year_pending)
    {
      echo "<h2 class=\"year\">$year</h2>";
      $this->_year_pending = false;
    }
    echo "<h3 class=\"month\">$month</h3>";
    echo "</td></tr>\n";

    echo "<tr>\n";
    $t = mktime (0, 0, 0, 9, 10, 2000);
      // a Sunday
    for ($i = 0; $i < Days_in_week; $i++)
    {
      $d = date ("D", $t);
      echo "<td class=\"weekday\">$d</td>\n";
      $t = mktime (0, 0, 0, 9, $i + 4, 2000);
    }
    echo "</tr>\n";
  }

  /**
   * A month has just completed.
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function finish_month ($month, $year)
  {
  }

  /**
   * A new year is beginning.
   * @param integer $year
   * @access private
   */
  public function start_year ($year)
  {
    $this->_year_pending = true;
  }

  /**
   * A year has just completed.
   * @param integer $year
   * @access private
   */
  public function finish_year ($year)
  {
  }

  /**
   * Calendar is being rendered.
   * @access private
   */
  public function start_calendar ()
  {
    echo "<table class=\"calendar\">\n";
  }

  /**
   * Calendar is finished rendering.
   * @access private
   */
  public function finish_calendar ()
  {
    echo "</table>\n";
  }

  /**
   * A day of the week with no date for that month.
   * @access private
   */
  public function make_blank ()
  {
    echo "<td class=\"cell-empty\">&nbsp;</td>\n";
  }

  /**
   * @access private
   */
  public function make_empty_months ()
  {
    $first_month = date ("F", mktime (0, 0, 0, $this->_first_empty_month, 1, $this->_first_empty_year));
    $last_month = date ("F", mktime (0, 0, 0, $this->_last_empty_month, 1, $this->_last_empty_year));
    if ($this->_first_empty_month != $this->_last_empty_month)
    {
      $month_text = "$first_month &ndash; $last_month";
    }
    else
    {
      $month_text = $first_month;
    }
    echo "<tr><td class=\"month\" colspan=\"" . Days_in_week . "\"><h3>$month_text</h3> <p class=\"notes\">No content</p></td></tr>\n";

    // clears the months as rendered

    parent::make_empty_months ();
  }

  /**
   * Render the actual content.
   * Table cell for the day is already created.
   * @param integer $day
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  protected function _get_content_for_day ($day, $week, $month, $year)
  {
    // return nothing by default
  }

  /**
   * @access private
   */
  protected function _draw_pager ()
  {
?>
    <p><?php $this->pager->display (); ?></p>
<?php
  }

  /** @var bool */
  private $_year_pending;
}
?>