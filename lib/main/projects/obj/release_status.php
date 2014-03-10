<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.5.0
 * @since 1.8.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Describes the overall status for a {@link RELEASE}.
 * Includes two {@link RELEASE_DATE_STATUS} objects, describing the testing and shipping status.
 * @package projects
 * @subpackage obj
 * @version 3.5.0
 * @since 1.8.0
 * @access private
 */
class RELEASE_STATUS extends WEBCORE_OBJECT
{
  /**
   * Information about the testing status of a release.
   * @var RELEASE_DATE_STATUS
   */
  public $test;

  /**
   * Information about the shipping status of a release.
   * @var RELEASE_DATE_STATUS
   */
  public $ship;

  /**
   * @param RELEASE $release
   * @param boolean $text_only Omit all tags if True.
   */
  public function __construct ($release, $text_only)
  {
    parent::__construct ($release->app);

    $this->test = new RELEASE_DATE_STATUS ($release, $release->time_tested, $release->time_testing_scheduled, ! $release->planned ());
    $this->ship = new RELEASE_DATE_STATUS ($release, $release->time_shipped, $release->time_scheduled);
  }

  /**
   * Return whether this release is overdue in any way.
   * Use this function rather than checking the states of {@link $test} and {@link $ship}.
   * @return boolean
   */
  public function is_overdue ()
  {
    return $this->test->overdue || $this->ship->overdue;
  }

  /**
   * Return this status as HTML
   * @return string
   */
  public function as_html ()
  {
    if (! isset ($this->_html_text))
    {
      $this->_html_text = $this->_as_text (false);
    }
    return $this->_html_text;
  }

  /**
   * Return this status as plain text.
   * @return string
   */
  public function as_plain_text ()
  {
    if (! isset ($this->_plain_text))
    {
      $this->_plain_text = $this->_as_text (true);
    }
    return $this->_plain_text;
  }

  /**
   * Format into a concise text representation.
   * @param boolean $text_only Do not use tags when formatting.
   * @return string
   * @access private
   */
  protected function _as_text ($text_only)
  {
    if ($this->ship->occurred)
    {
      $Result = 'Released on ' . $this->ship->date_as_text ($text_only);
    }
    else
    {
      if ($this->test->scheduled)
      {
        if (! $this->test->occurred)
        {
          $label = 'testing';
          $stat = $this->test;
        }
        else if ($this->ship->scheduled)
        {
          $label = 'release';
          $stat = $this->ship;
        }
      }
      else if ($this->ship->scheduled)
      {
        $label = 'release';
        $stat = $this->ship;
      }
      else
      {
        $Result = 'Assigned to release';
      }
    }

    if (! isset ($Result))
    {
      $Result = '';
      $Result .= "Scheduled for $label " . $stat->date_as_text ($text_only);
      $Result .= ' (' . $stat->diff_as_text ($text_only) . ' ' . $stat->diff_label . ')';

      if ($text_only)
      {
        if ($stat->text)
        {
          $Result = '[' . $stat->text . '] ' . $Result;
        }
      }
      else
      {
        $Result = $this->app->get_text_with_icon($stat->icon_url, $Result, '16px');
      }
    }

    return $Result;
  }

  /**
   * @var string
   * @access private
   */
  protected $_html_text;

  /**
   * @var string
   * @access private
   */
  protected $_plain_text;
}

/**
 * Describes the status for a {@link RELEASE} date.
 * Releases have two dates: testing and shipping.
 * @package projects
 * @subpackage obj
 * @version 3.5.0
 * @since 1.8.0
 */
class RELEASE_DATE_STATUS extends WEBCORE_OBJECT
{
  /**
   * Either the time scheduled or the time occurred.
   * @var DATE_TIME
   */
  public $date;

  /**
   * The relevant time difference.
   * If {@link $occurred} is True, then this is the difference between scheduled
   * time and occurred time. If {@link $scheduled} is True, then this is the
   * difference between now and time scheduled.
   * @var TIME_INTERVAL
   */
  public $difference;

  /**
   * Either 'early' or 'late'.
   * @var string
   */
  public $diff_label;

  /**
   * True if scheduled time has passed and {@link $occurred} is False.
   * @var boolean
   */
  public $overdue;

  /**
   * True if the event occurred.
   * @var boolean
   */
  public $occurred;

  /**
   * True if the event is (or was) scheduled.
   * @var boolean
   */
  public $scheduled;

  /**
   * Url to the graphic indicator of the status condition.
   * Indicates whether an event has occurred, was skipped or is overdue.
   * @see $text
   * @var string
   */
  public $icon_url;

  /**
   * Text indicator of the status condition.
   * Indicates whether an event has occurred, was skipped or is overdue.
   * @see $icon
   * @var string
   */
  public $text;

  /**
   * Time the event was scheduled.
   * @var DATE_TIME
   */
  public $time_scheduled;

  /**
   * Time the event occurred.
   * @var DATE_TIME
   */
  public $time_occurred;

  /**
   * @param RELEASE $rel
   * @param DATE_TIME $occurred Time the event occurred. Need not be valid.
   * @param DATE_TIME $scheduled Time the event is scheduled. Need not be valid.
   * @param boolean $skip_condition Marks an event as skipped if this is true, and the event was scheduled, but has not occurred.
   * @internal param \RELEASE $release
   */
  public function __construct ($rel, $occurred, $scheduled, $skip_condition = false)
  {
    parent::__construct ($rel->context);

    $this->_release = $rel;
    $this->time_occurred = $occurred;
    $this->time_scheduled = $scheduled;
    $this->_skip_condition = $skip_condition;

    $this->refresh ();
  }

  /**
   * Return this status as HTML
   * @return string
   */
  public function as_html ()
  {
    if (! isset ($this->_html_text))
    {
      $this->_html_text = $this->_as_text (false);
      $this->_html_text = $this->app->get_text_with_icon($this->icon_url, $this->_html_text, '16px');
      $this->_html_text = '<span style="white-space: nowrap">' . $this->_html_text . '</span>';
    }
    return $this->_html_text;
  }

  /**
   * Return this status as plain text.
   * @return string
   */
  public function as_plain_text ()
  {
    if (! isset ($this->_plain_text))
    {
       $this->_plain_text = $this->_as_text (true);
      if ($this->text)
      {
        $this->_plain_text = $this->text . ' ' . $this->_plain_text;
      }
    }
    return $this->_plain_text;
  }

  /**
   * Format the date into a string.
   * @param boolean $text_only Do not use tags when formatting.
   * @return string
   */
  public function date_as_text ($text_only)
  {
    if (isset ($this->date))
    {
      return $this->_date ($this->date, $text_only);
    }
    
    return '';
  }

  /**
   * Format the time difference into a string.
   * @param boolean $text_only Do not use tags when formatting.
   * @return string
   */
  public function diff_as_text ($text_only)
  {
    if (isset ($this->difference) && ! $this->difference->is_empty ())
    {
      return $this->difference->format (1);
    }

    return '';
  }

  /**
   * Recalculates the event's status.
   * Call this if you reassign the dates.
   */
  public function refresh ()
  {
    $this->scheduled = $this->time_scheduled->is_valid ();
    $this->occurred = $this->time_occurred->is_valid ();
    $this->overdue = false;
    $this->icon_url = '';
    $this->text = '';
    $this->diff_label = '';
    $this->_html_text = null;
    $this->_plain_text = null;

    if ($this->occurred)
    {
      $this->date = $this->time_occurred;

      $this->icon_url = '{icons}indicators/released';

      if ($this->time_scheduled->is_valid ())
      {
        if ($this->time_occurred->less_than_equal ($this->time_scheduled))
        {
          $this->difference = $this->time_scheduled->diff ($this->time_occurred);
          $this->diff_label = 'early';
        }
        else
        {
          $this->difference = $this->time_occurred->diff ($this->time_scheduled);
          $this->diff_label = 'late';
        }
      }
    }
    else
    {
      if ($this->scheduled)
      {
        $this->date = $this->time_scheduled;
        if ($this->_skip_condition)
        {
          $this->icon_url = '{icons}indicators/warning';
          $this->text = 'Skipped';
        }
        else
        {
          $this->icon_url = '{icons}buttons/calendar';
          $now = new DATE_TIME ();

          if ($now->less_than_equal ($this->time_scheduled))
          {
            $this->difference = $this->time_scheduled->diff ($now);
            $this->diff_label = 'left';

            /* Determine whether there should be a warning for the approaching deadline. */

            $warn_time = $this->_release->warning_time ($this->time_scheduled);
            if ($warn_time->less_than ($now))
            {
              $this->icon_url = '{icons}indicators/warning';
              $this->text = 'Due soon';
            }
          }
          else
          {
            $this->overdue = true;
            $this->icon_url = '{icons}indicators/error';
            $this->text = 'Overdue';
            $this->difference = $now->diff ($this->time_scheduled);
            $this->diff_label = 'late';
          }
        }
      }
    }
  }

  /**
   * Format as generic text.
   * @param boolean $text_only Do not use tags when formatting.
   * @return string
   * @access private
   */
  protected function _as_text ($text_only)
  {
    $date_text = $this->date_as_text ($text_only);
    $diff_text = $this->diff_as_text ($text_only);

    if ($this->overdue)
    {
      $Result = "$date_text ($diff_text $this->diff_label)";
    }
    else if ($this->occurred)
    {
      if ($diff_text)
      {
        $Result = "$date_text ($diff_text $this->diff_label)";
      }
      else
      {
        $Result = $date_text;
      }
    }
    else if ($this->scheduled)
    {
      if ($diff_text)
      {
        $Result = "$diff_text $this->diff_label ($date_text)";
      }
      else
      {
        $Result = $date_text;
      }
    }
    else
    {
      $Result = 'Not scheduled';
    }
      
    return $Result;
  }

  /**
   * Format a date for display.
   * @param DATE_TIME $date
   * @param boolean $text_only Do not use tags when formatting.
   * @return string
   * @access private
   */
  protected function _date ($date, $text_only)
  {
    $Result = '';

    if (isset ($date) && $date->is_valid ())
    {
      $f = $date->formatter ();
      $f->type = Date_time_format_short_date;
      $f->show_local_time = ! $text_only && $this->context->local_times_allowed ();
      $f->show_CSS = ! $text_only;

      $Result = $date->format ($f);
      if (! $text_only)
      {
        $Result = '<span class="visible" style="white-space: nowrap">' . $Result . '</span>';
      }
    }

    return $Result;
  }

  /**
   * Attached to this release.
   * @var RELEASE
   * @access private
   */
  protected $_release;

  /**
   * @var boolean
   * @access private
   */
  protected $_skip_condition;

  /**
   * @var string
   * @access private
   */
  protected $_html_text;

  /**
   * @var string
   * @access private
   */
  protected $_plain_text;
}

?>