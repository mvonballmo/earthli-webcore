<?php

/**
 * @copyright Copyright (c) 2002-2007 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 2.8.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2007 Marco Von Ballmoos

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
define ('Seconds_in_day', 86400);
define ('Days_in_week', 7);
define ('Months_in_year', 12);

require_once ('webcore/obj/webcore_object.php');

/**
 * Abstract calendar functionality.
 * Provides drawing hooks for days, weeks, months, years so descendants can decide how to draw.
 * @package webcore
 * @subpackage gui
 * @version 2.8.0
 * @since 2.2.1
 * @abstract
 */
abstract class CALENDAR extends WEBCORE_OBJECT
{
  /**
   * First day to render.
   * Treat as a read-only property; set only with {@link set_ranges()}.
   * @var DATE_TIME
   */
  public $first_day;

  /**
   * Last day to render.
   * Treat as a read-only property; set only with {@link set_ranges()}.
   * @var DATE_TIME
   */
  public $last_day;

  /**
   * @param APPLICATION $app Main application.
   */
  public function CALENDAR ($app)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($app);

    include_once ('webcore/gui/page_navigator.php');
    $this->paginator = new PAGE_NAVIGATOR ($app);
    $this->paginator->pages_to_show = 5;
  }

  /**
   * Set the first and last days to render.
   * Must be called before calling {@link display()}.
   * @param DATE_TIME $first_day
   * @param DATE_TIME $last_day
   */
  public function set_ranges ($first_day, $last_day)
  {
    $first_day->set_time_from_iso ('00:00:00');
    $last_day->set_time_from_iso ('23:59:59');

    $this->first_day = $first_day;
    $this->last_day = $last_day;

    $php_first_day = $first_day->as_php ();
    $php_last_day = $last_day->as_php ();

    $first_month = date ("n", $php_first_day);
    $first_year = date ("Y", $php_first_day);

    $last_month = date ("n", $php_last_day);
    $last_year = date ("Y", $php_last_day);

    $diff_months = (($last_year * Months_in_year) + $last_month) - (($first_year * Months_in_year) + $first_month);

    if ($diff_months > Months_in_year)
    {
      $this->num_years = $last_year - $first_year + 1;
    }
    else
    {
      $this->num_years = 1;
    }

    $this->paginator->set_ranges ($this->num_years, 1);
    $this->paginator->page_offset = date ("Y", $php_first_day) - 1;

    $curr_month = $first_month;
    $curr_year = $first_year;

    $diff_months = (($last_year * 12) + $last_month) - (($curr_year * 12) + $curr_month);

    if ($diff_months > 12)
    {
      // the span is more than 12 months, so split into pages
      
      if ($this->paginator->page_number == 1)
      {
        $last_month = 12;
        $last_year = $curr_year;
      }
      else
      {
        $curr_month = 1;
        $curr_year = $curr_year + $this->paginator->page_number - 1;
        if ($curr_year != $last_year)
        {
          $last_month = 12;
          $last_year = $curr_year;
        }
      }
    }

    $this->curr_month = $curr_month;
    $this->curr_year = $curr_year;
    $this->last_month = $last_month;
    $this->last_year = $last_year;

    $this->_page_changed ();
  }

  /**
   * Render all the days speficied in the range.
   * Set the range with 'set_ranges'. If the calendar spans more than a year, then use the
   * page number to navigate between years.
   * @see CALENDAR::set_ranges()
   */
  public function display ()
  {
    // get the first day of the month that 'first_day' falls on
    // and the last day of the month that 'last_day' falls on
    // to make sure the calendar displays whole months only

    $this->_draw_paginator ();
    $this->start_calendar ();

    $this->start_year ($this->curr_year);

    $month_displayed = false;

    while ((($this->curr_year * 12) + $this->curr_month) <= (($this->last_year * 12) + $this->last_month))
    {
      $current_month_has_content = $this->month_has_content ($this->curr_month, $this->curr_year);
      $month_displayed = $month_displayed || $current_month_has_content;

      if ($current_month_has_content)
      {
        $this->build_month ($this->curr_month, $this->curr_year);
      }
      else
      {
        if (! isset ($this->_first_empty_month))
        {
          $this->_first_empty_month = $this->curr_month;
          $this->_first_empty_year = $this->curr_year;
        }
        $this->_last_empty_month = $this->curr_month;
        $this->_last_empty_year = $this->curr_year;
      }

      if ($this->curr_month < Months_in_year)
      {
        $this->curr_month++;
      }
      else
      {
        $this->finish_year ($this->curr_year);
        $this->curr_month = 1;
        $this->curr_year++;

        if ($this->curr_year <= $this->last_year)
        {
          $this->start_year ($this->curr_year);
        }
      }
    }

    if (isset ($this->_first_empty_month))
    {
      // purge out any empty months that were skipped

      $this->make_empty_months ();
    }

    $this->finish_calendar ();
    if ($month_displayed)
    {
      $this->_draw_paginator ();
    }
  }

  /**
   * Renders the specified month.
   * Months are 1-based.
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function build_month ($month, $year)
  {
    if (isset ($this->_first_empty_month))
    {
      // purge out any empty months that were skipped
      $this->make_empty_months ();
    }

    $first_day = mktime (0, 0, 0, $month, 1, $year);

    $week = 1;

    $this->start_month ($month, $year);
    $this->start_week ($week, $month, $year);

    $day_of_week = date ("w", $first_day);

    for ($i = 0; $i < $day_of_week; $i++)
      $this->make_blank ();

    $day_num = date ("j", $first_day);

    $this->make_day ($day_num, $week, $month, $year);

    if ($day_of_week % Days_in_week == Days_in_week - 1)
    {
      $this->finish_week ($week, $month, $year);
      $week++;
    }

    $day_of_week++;
    $index = 1;
    $day_num = date ("j", $first_day + ($index * Seconds_in_day));

    while ($day_num != 1)
    {
      if ($day_of_week % Days_in_week == 0)
      {
        $this->start_week ($week, $month, $year);
      }

      $this->make_day ($day_num, $week, $month, $year);

      if ($day_of_week % Days_in_week == Days_in_week - 1)
      {
        $this->finish_week ($week, $month, $year);
        $week++;
      }

      $day_of_week++;
      $index++;
      $day_num = date ("j", $first_day + ($index * Seconds_in_day));
    }

    if ($day_of_week % Days_in_week != 0)
    {
      for ($i = $day_of_week; $i % Days_in_week != 0; $i++)
        $this->make_blank ();

      $this->finish_week ($week, $month, $year);
    }

    $this->finish_month ($month, $year);
  }

  /**
   * Does the specified month contain anything?
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function month_has_content ($month, $year)
  {
    return true;
  }
  /**
   * Render the given months as completely empty.
   * This is only called if 'month_has_content' has returned false for one or more
   * months in a row. This mechnism avoids showing the full calendar for uninteresting months.
   * Most representations will just show the span of missing months as text without a grid.
   * Descendants should call this method in order to clear the internal variables.
   * @access private
   */
  public function make_empty_months ()
  {
    unset ($this->_first_empty_month,
           $this->_last_empty_month,
           $this->_first_empty_year,
           $this->_last_empty_year);
  }
  
	/**
   * A day of the week with no date for that month.
   * @access private
   * @abstract
   */
  public abstract function make_blank ();
  
  /**
   * Render a day of the month.
   * @param integer $day
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   * @abstract
   */
  public abstract function make_day ($day, $week, $month, $year);
  
  /**
   * Render the page navigator.
   * @access private
   * @abstract
   */
  protected abstract function _draw_paginator ();

  /**
   * A new week is beginning.
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function start_week ($week, $month, $year) {}
  
	/**
   * A week has just completed.
   * @param integer $week
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function finish_week ($week, $month, $year) {}

  /**
   * A new month is beginning.
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function start_month ($month, $year) {}
  
	/**
   * A month has just completed.
   * @param integer $month
   * @param integer $year
   * @access private
   */
  public function finish_month ($month, $year) {}

  /**
   * A new year is beginning.
   * @param integer $year
   * @access private
   */
  public function start_year ($year) {}

  /**
   * A year has just completed.
   * @param integer $year
   * @access private
   */
  public function finish_year ($year) {}

  /**
   * Calendar is being rendered.
   * @access private
   */
  public function start_calendar () {}
  
	/**
   * Calendar is finished rendering.
   * @access private
   */
  public function finish_calendar () {}

  /**
   * Called when the page is set before rendering.
   * Hook this event to perform any necessary processing before the calendar is rendered.
   * @access private
   */
  protected function _page_changed () {}

  /**
   * First month of current data-free range in calendar.
   * @see CALENDAR::make_empty_months()
   * @var integer
   * @access private
   */
  protected $_first_empty_month;
  
  /**
   * Last month of current data-free range in calendar.
   * @see CALENDAR::make_empty_months()
   * @var integer
   * @access private
   */
  protected $_last_empty_month;
  
  /**
   * First year of current data-free range in calendar.
   * @see CALENDAR::make_empty_months()
   * @var integer
   * @access private
   */
  protected $_first_empty_year;
  
  /**
   * Last year of current data-free range in calendar.
   * @see CALENDAR::make_empty_months()
   * @var integer
   * @access private
   */
  protected $_last_empty_year;
}

?>