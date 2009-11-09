<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.2.0
 * @since 2.2.1
 * @package webcore
 * @subpackage date-time
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/sys/system.php');

/**
 * Format the full date and time.
 */
define ('Date_time_format_date_and_time', 'date_and_time');
/**
 * Format only the date.
 */
define ('Date_time_format_date_only', 'date_only');
/**
 * Format the month (as text) and the year.
 */
define ('Date_time_format_month_and_year', 'month_and_year');
/**
 * Format the date in compact form.
 */
define ('Date_time_format_short_date', 'short_date');
/**
 * Format the date and time in compact form.
 */
define ('Date_time_format_short_date_and_time', 'short_date_and_time');

/**
 * Compare only the times.
 */
define ('Date_time_time_part', 1);
/**
 * Compare only the dates.
 */
define ('Date_time_date_part', 2);
/**
 * Compare date and time.
 */
define ('Date_time_both_parts', 3);

/**
 * Identifies a time that is in the PHP/Unix timestamp format.
 */
define ('Date_time_php', 'php');
/**
 * Identifies a time that is in ISO/db format (YYYY-MM-DD hh:mm:ss).
 */
define ('Date_time_iso', 'iso');

define ('Date_time_unassigned', -1);

/**
 * Provides formatting and conversion for {@link DATE_TIME}s.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class DATE_TIME_TOOLKIT
{
  /**
   * Formats date/times for string output.
   * @var DATE_TIME_FORMATTER
   */
  public $formatter;

  public function __construct ()
  {
    $this->register_converter (new US_DATE_TIME_CONVERTER ());
    $this->register_converter (new EURO_DATE_TIME_CONVERTER ());
    $this->register_converter (new ISO_DATE_TIME_CONVERTER ());
    $this->register_converter (new EXIF_DATE_TIME_CONVERTER ());

    $this->formatter = new US_DATE_TIME_FORMATTER ();
  }

  /**
   * Register a conversion format.
   * 
   * @param DATE_TIME_CONVERTER $converter
   */
  public function register_converter ($converter)
  {
    $this->_converters [] = $converter;
  }

  /**
   * Convert from a supported string representation.
   * @param string $t
   * @param integer $parts Which parts of the text should be converted?
   * @return integer
   */
  public function text_to_php ($t, $parts = Date_time_both_parts)
  {
    if (empty($t)) { return Date_time_unassigned; }
    
    foreach ($this->_converters as $converter)
    {
      $Result = $converter->text_to_php ($t, $parts);
      if ($Result != Date_time_unassigned)
      {
        return $Result;
      }
    }
    
    return Date_time_unassigned;
  }

  /**
   * List of registered input formats.
   * @var array[DATE_TIME_CONVERTER]
   * @see DATE_TIME_CONVERTER
   * @access private
   */
  protected $_converters;
}

/**
 * Handles formatting dates for an {@link APPLICATION}.
 * Times can be formatted as either local times (browser side, using JavaScript) or as GMT-based
 * absolute times. Maintains an array of format strings which can be customized to locale.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class DATE_TIME_FORMATTER extends RAISABLE
{
  /**
   * Formatting type to use (can be empty)
   * @var integer
   * @see Date_time_format_date_and_time
   * @see Date_time_format_date_only
   * @see Date_time_format_month_and_year
   * @see Date_time_format_short_date
   */
  public $type;

  /**
   * Show adjusted to local time? (uses JavaScript)
   * @var boolean
   */
  public $show_local_time = true;

  /**
   * Show the time zone in the result?
   * Will only be shown if {@link $show_local_time} is False.
   * @var boolean
   */
  public $show_time_zone = true;

  /**
   * Time-zone to show for non-local times.
   * If the time is shown without JavaScript, this time zone is appended to indicate the
   * locale of the server.
   * @var string
   */
  public $time_zone_id = 'GMT-5';

  /**
   * Wrap the formatted output in CSS.
   * This wraps a 'span' tag around the date with class = 'date-time'.
   * @var boolean
   */
  public $show_CSS = true;

  /**
   * Set the format string for a date time type.
   * @param integer $type
   * @param string $format_string
   */
  public function register_formatter ($type, $format_string)
  {
    $this->_format_strings [$type] = $format_string;
  }

  /**
   * Return the format string for a type.
   * @param string $type
   * @return string
   */
  public function format_string_for ($type)
  {
    return $this->_format_strings [$type];
  }

  /**
   * Set the default format type for this formatter (used when no type is assigned)
   * @param integer $type
   */
  public function set_default_formatter ($type)
  {
    $this->assert (! empty ($this->_format_strings [$type]), "[$type] is not a registered date time format.", 'set_default_formatter', 'DATE_TIME_FORMATTER');
    $this->_default_format_type = $type;
  }

  /**
   * Formats a PHP-style time.
   * If the {@link $show_local_time} setting is true, this will format a string of JavaScript which calculates
   * the date in local time on the client.
   * @param integer $php_time
   * @return string
   */
  public function date_time_to_text ($php_time)
  {
    if (isset ($this->type))
    {
      $type = $this->type;
    }
    else
    {
      $type = $this->_default_format_type;
    }

    $this->assert (! empty ($this->_format_strings [$type]), "[$type] is not a registered date time format.", 'date_time_to_text', 'DATE_TIME_FORMATTER');

    $fmt = $this->_format_strings [$type];

    if ($this->show_local_time)
    {
      // replace the escape character so it's properly escaped on Javascript as well.

      $fmt = str_replace ('\\', '\\\\', $fmt);
      $Result = "<script type=\"text/javascript\">document.write (local_time ($php_time, '$fmt'));</script>";
    }
    else
    {
      $Result = @date ($fmt, $php_time);
      if ($Result)
      {
        if (($type == Date_time_format_date_and_time) && ($this->show_time_zone))
        {
          $Result .= " ($this->time_zone_id)";
        }
      }
      else
      {
        $Result = "[none]";
      }
    }

    if ($this->show_CSS)
    {
      return "<span class=\"date-time\">$Result</span>";
    }

    return $Result;
  }

  /**
   * Sets the {@link $type} and clears all formatting flags.
   * Calls {@link clear_flags()} to remove all markup and format as plain text.
   * @param integer $type Can be any of the registered {@link $type}s.
   */
  public function set_type_and_clear_flags ($type)
  {
    $this->type = $type;
    $this->clear_flags ();
  }

  /**
   * Clears all formatting flags.
   * Useful for plain text and debugging output. Sets {@link $show_local_time},
   * {@link $show_CSS} and {@link $show_time_zone} to False.
   */
  public function clear_flags ()
  {
    $this->show_CSS = false;
    $this->show_local_time = false;
    $this->show_time_zone = false;
  }

  /**
   * The type if none is specified.
   * @var string
   * @access private
   */
  protected $_default_format_type;

  /**
   * List of registered output formats.
   * @var array[string]
   * @access private
   */
  protected $_format_strings;
}

/**
 * Standard US date/time formatting.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class US_DATE_TIME_FORMATTER extends DATE_TIME_FORMATTER
{
  public function __construct ()
  {
    $this->register_formatter (Date_time_format_date_and_time, 'M j, Y H:i:s');
    $this->register_formatter (Date_time_format_date_only, 'M j, Y');
    $this->register_formatter (Date_time_format_month_and_year, 'M Y');
    $this->register_formatter (Date_time_format_short_date, 'm/d/Y');
    $this->register_formatter (Date_time_format_short_date_and_time, 'm/d/Y H:i:s');
    $this->set_default_formatter (Date_time_format_date_and_time);
  }
}

/**
 * Standard European date/time formatting.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class EURO_DATE_TIME_FORMATTER extends DATE_TIME_FORMATTER
{
  public function __construct ()
  {
    $this->register_formatter (Date_time_format_date_and_time, 'j. M Y H:i:s');
    $this->register_formatter (Date_time_format_date_only, 'j. M Y');
    $this->register_formatter (Date_time_format_month_and_year, 'M Y');
    $this->register_formatter (Date_time_format_short_date, 'd.m.Y');
    $this->register_formatter (Date_time_format_short_date_and_time, 'd.m.Y H:i:s');
    $this->set_default_formatter (Date_time_format_date_and_time);
  }
}

/**
 * Standard ISO date/time formatting.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.7.0
 */
class ISO_DATE_TIME_FORMATTER extends DATE_TIME_FORMATTER
{
  public function __construct ()
  {
    $this->register_formatter (Date_time_format_date_and_time, 'M j, Y H:i:s');
    $this->register_formatter (Date_time_format_date_only, 'M j, Y');
    $this->register_formatter (Date_time_format_month_and_year, 'M Y');
    $this->register_formatter (Date_time_format_short_date, 'Y-m-d');
    $this->register_formatter (Date_time_format_short_date_and_time, 'Y-m-d H:i:s');
    $this->set_default_formatter (Date_time_format_date_and_time);
  }
}

/**
 * Converts strings to times usable by {@link DATE_TIME}s.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
abstract class DATE_TIME_CONVERTER extends RAISABLE
{
  /**
   * Matched against candidate strings in {@link text_to_php()} using {@link PHP_MANUAL#preg_match}.
   * @var string
   */
  public $match_expression;

  /**
   * @var integer
   */
  public $month_pos;

  /**
   * @var integer
   */
  public $day_pos;

  /**
   * @var integer
   */
  public $year_pos;

  /**
   * Convert a string to a php time.
   * @param string $t
   * @param integer $parts Which parts of the text should be converted?
   * @return integer
   * @abstract
   */
  public function text_to_php ($value, $parts = Date_time_both_parts)
  {
    $arr = null; // Compiler warning
    if (preg_match ($this->match_expression, $value, $arr))
    {
      switch ($parts)
      {
      case Date_time_both_parts:
        return mktime ($arr [6], $arr [7], $arr [8], $arr [$this->month_pos], $arr [$this->day_pos], $arr [$this->year_pos]);
      case Date_time_time_part:
        return mktime ($arr [6], $arr [7], $arr [8]);
      case Date_time_date_part:
        return mktime (0, 0, 0, $arr [$this->month_pos], $arr [$this->day_pos], $arr [$this->year_pos]);
      }
    }

    return Date_time_unassigned;
  }
}

/**
 * Converts a US date (mm/dd/yyyy hh:mm:ss).
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class US_DATE_TIME_CONVERTER extends DATE_TIME_CONVERTER
{
  /**
   * Matched against candidate strings in {@link text_to_php()} using {@link PHP_MANUAL#preg_match}.
   * @var string
   */
  public $match_expression = '&^(([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}))?[ ]?(([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$&';

  /**
   * @var integer
   */
  public $month_pos = 2;

  /**
   * @var integer
   */
  public $day_pos = 3;

  /**
   * @var integer
   */
  public $year_pos = 4;
}

/**
 * Converts a European date (dd.mm.yyyy hh:mm:ss).
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class EURO_DATE_TIME_CONVERTER extends DATE_TIME_CONVERTER
{
  /**
   * Matched against candidate strings in {@link text_to_php()} using {@link PHP_MANUAL#preg_match}.
   * @var string
   */
  public $match_expression = '/^(([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}))?[ ]?(([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$/';

  /**
   * @var integer
   */
  public $month_pos = 3;

  /**
   * @var integer
   */
  public $day_pos = 2;

  /**
   * @var integer
   */
  public $year_pos = 4;
}

/**
 * Converts an ISO date (yyyy-mm-dd hh:mm:ss).
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.5.0
 */
class ISO_DATE_TIME_CONVERTER extends DATE_TIME_CONVERTER
{
  /**
   * Matched against candidate strings in {@link text_to_php()} using {@link PHP_MANUAL#preg_match}.
   * @var string
   */
  public $match_expression = '/^(([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}))?[ ]?(([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$/';

  /**
   * @var integer
   */
  public $month_pos = 3;

  /**
   * @var integer
   */
  public $day_pos = 4;

  /**
   * @var integer
   */
  public $year_pos = 2;
}

/**
 * Converts an EXIF date (yyyy:mm:dd hh:mm:ss).
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.7.0
 */
class EXIF_DATE_TIME_CONVERTER extends DATE_TIME_CONVERTER
{
  /**
   * Matched against candidate strings in {@link text_to_php()} using {@link PHP_MANUAL#preg_match}.
   * @var string
   */
  public $match_expression = '/^(([0-9]{4}):([0-9]{1,2}):([0-9]{1,2}))?[ ]?(([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$/';

  /**
   * @var integer
   */
  public $month_pos = 3;

  /**
   * @var integer
   */
  public $day_pos = 4;

  /**
   * @var integer
   */
  public $year_pos = 2;
}

/**
 * Represents a date/time.
 * Maintains an internal date representation that can be returned {@link
 * as_php()} or {@link as_iso()} or formatted with {@link format()}. The time
 * can be set with {@link set_from_php()}, {@link set_from_iso()} or {@link
 * set_from_text()} and cleared with {@link clear()}. Use {@link is_valid()} to
 * check the date and {@link equals()}, {@link less_than()} and {@link
 * less_than_equal()} to compare times and {@link diff()} to retrieve the
 * difference as a {@link TIME_INTERVAL}.
 * @see TIME_INTERVAL
 * @see DATE_TIME_FORMATTER
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.2.1
 */
class DATE_TIME extends RAISABLE
{
  /**
   * @param object $time Can be either a PHP or ISO time.
   * @param string $type Can be either {@link Date_time_php} or {@link
   * Date_time_iso}.
   */
  public function __construct ($time = 0, $type = null)
  {
    if (! $time)
    {
      $time = time ();
      $type = Date_time_php;
    }

    if (! isset ($type))
    {
      if (is_int ($time))
      {
        $type = Date_time_php;
      }
      else
      {
        $type = Date_time_iso;
      }
    }

    switch ($type)
    {
    case Date_time_php:
      $this->set_from_php ($time);
      break;
    case Date_time_iso:
      $this->set_from_iso ($time);
      break;
    }
  }

  /**
   * @return boolean
   */
  public function is_valid ()
  {
    return $this->as_php () != Date_time_unassigned;
  }

  /**
   * Returns this object as a PHP timestamp.
   * @return integer
   */
  public function as_php ()
  {
    if (($this->_php_time == Date_time_unassigned) && ($this->_iso_time != Date_time_unassigned))
    {
      $parts = null; // Compiler warning
      preg_match ('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})( ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))/', $this->_iso_time, $parts);
      if (sizeof ($parts))
      {
        $this->_php_time = mktime ($parts [5], $parts [6], $parts [7], $parts [2], $parts [3], $parts [1]);
      }
      else
      {
        $this->_iso_time = Date_time_unassigned;
      }
    }

    return $this->_php_time;
  }

  /**
   * Returns the ISO representation.
   * Use this format with {@link DATABASE}s.
   * Example: 2000-12-21 16:01:07
   * @return string
   */
  public function as_iso ()
  {
    if (($this->_iso_time == Date_time_unassigned) && ($this->_php_time != Date_time_unassigned))
    {
      $this->_iso_time = date ("Y-m-d H:i:s", $this->_php_time);
      if (! isset($this->_iso_time))
      {
        $this->_iso_time = Date_time_unassigned;
      }
    }

    return $this->_iso_time;
  }

  /**
   * Returns the RFC 2822 representation.
   * Use this format with the {@link RSS_RENDERER}.
   * Example: Thu, 21 Dec 2000 16:01:07 +0200
   * @return string
   */
  public function as_RFC_2822 ()
  {
    return date (DATE_RFC2822, $this->as_php ());
  }

  /**
   * Returns the RFC 3339 representation.
   * Use this format with the {@link RSS_RENDERER}.
   * Example: 2000-12-21T16:01:07+02:00
   * @return string
   */
  public function as_RFC_3339 ()
  {
    return date(DATE_ATOM, $this->as_php ());
  }

  /**
   * Sets the date and time from an ISO-formatted string.
   * @param string $t
   */
  public function set_from_iso ($t)
  {
    $this->_iso_time = (isset($t) && ($t != '0000-00-00 00:00:00')) ? $t : Date_time_unassigned;
    $this->_php_time = Date_time_unassigned;
  }

  /**
   * Sets the date and time from a PHP timestamp.
   * @param string $t
   */
  public function set_from_php ($t)
  {
    $this->_php_time = $t;
    $this->_iso_time = Date_time_unassigned;
  }

  /**
   * Sets the time from an ISO-formatted string.
   * @param string $t
   */
  public function set_time_from_iso ($t)
  {
    if ($this->_iso_time == Date_time_unassigned)
    {
      $this->set_from_iso ($this->as_iso ());
    }

    $parts = split (' ', $this->_iso_time);
    $this->_iso_time = $parts [0] . " $t";
  }

  /**
   * Set the time from a user input.
   * Supported formats are determined by the {@link DATE_TIME_CONVERTER}s
   * registered with the {@link toolkit()}.
   * @param string $t
   * @param integer $parts Can be {@link Date_time_time_part}, {@link
   * Date_time_date_part} or {@link Date_time_both_parts}.
   */
  public function set_from_text ($t, $parts = Date_time_both_parts)
  {
    $toolkit = $this->toolkit ();
    $this->set_from_php ($toolkit->text_to_php ($t, $parts));
  }

  public function set_now ()
  {
    $this->set_from_php (time ());
  }

  /**
   * Clear the time.
   * This ensures that {@link is_valid()} returns false.
   */
  public function clear ()
  {
    $this->_iso_time = Date_time_unassigned;
    $this->_php_time = Date_time_unassigned;
  }

  /**
   * Format the date.
   * You can pass in a formatter, which is commonly obtained by calling 'formatter'. From
   * that object, change the defaults, then call format with it. If you don't pass a formatter,
   * the unmodified default is used.
   * @param DATE_TIME_FORMATTER $formatter Pass in the formatter to use.
   * @return string
   */
  public function format ($formatter = 0)
  {
    if (! $formatter)
    {
      $formatter = $this->formatter ();
    }
    return $formatter->date_time_to_text ($this->as_php ());
  }

  /**
   * Format the date without any highlighting.
   * @return string
   */
  public function format_plain ()
  {
    $formatter = $this->formatter ();
    $formatter->clear_flags ();
    return $this->format ($formatter);
  }

  /**
   * Is this date equal to 'other'?
   * Compares on the requested portions of the two dates.
   * @param DATE_TIME $other
   * @param int $parts Can be {@link Date_time_date_part}, {@link Date_time_time_part} or {@link Date_time_both_parts}.
   * @return boolean
   */
  public function equals ($other, $parts = Date_time_both_parts)
  {
    return $this->_compare_to ($other, Operator_equal, $parts);
  }

  /**
   * Is this date less than or equal to 'other'?
   * Compares on the requested portions of the two dates.
   * @param DATE_TIME $other
   * @param int $parts Can be {@link Date_time_date_part}, {@link Date_time_time_part} or {@link Date_time_both_parts}.
   * @return boolean
   */
  public function less_than_equal ($other, $parts = Date_time_both_parts)
  {
    return $this->_compare_to ($other, Operator_less_than_equal, $parts);
  }

  /**
   * Is this date less than 'other'?
   * Compares on the requested portions of the two dates.
   * @param DATE_TIME $other
   * @param int $parts Can be {@link Date_time_date_part}, {@link Date_time_time_part} or {@link Date_time_both_parts}.
   * @return boolean
   */
  public function less_than ($other, $parts = Date_time_both_parts)
  {
    return $this->_compare_to ($other, Operator_less_than, $parts);
  }

  /**
   * The last valid day of any month.
   * This is useful for building calendars. Handles leap years.
   * @return integer
   */
  public function last_legal_day ()
  {
    $t = $this->as_php ();
    $month = date ('n', $t);
    switch ($month)
    {
    case 1:
    case 3:
    case 5:
    case 7:
    case 8:
    case 10:
    case 12:
      return 31;
    case 4:
    case 6:
    case 9:
    case 11:
      return 30;
    case 2:
      if (checkdate ($month, 29, date ('Y', $t)))    // check if the year is a leap year
        return 29;

      return 28;
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($month);
    }
  }

  /**
   * The difference between the two times.
   * @param DATE_TIME $other
   * @return TIME_INTERVAL
   */
  public function diff ($other)
  {
    return new TIME_INTERVAL ($this, $other);
  }

  /**
   * Returns the formatter for this date.
   * @see toolkit()
   * @return DATE_TIME_FORMATTER
   */
  public function formatter ()
  {
    $toolkit = $this->toolkit ();
    return $toolkit->formatter;
  }

  /**
   * Returns the toolki for this date/time.
   * This basic date/time class uses the {@link global_date_time_toolkit()},
   * which is fine for most applications. Applications which need specialized
   * toolkits can define a descendent and redefine this function.
   * @see formatter()
   * @see use_toolkit()
   * @return DATE_TIME_TOOLKIT
   */
  public function toolkit ()
  {
    if (isset ($this->_toolkit))
    {
      $Result = $this->_toolkit;
    }
    else
    {
      $Result = global_date_time_toolkit ();
    }
    return $Result;
  }

  /**
   * Use the given toolkit for conversions and formatting.
   * @param DATE_TIME_TOOLKIT $toolkit
   * @see toolkit()
   */
  public function use_toolkit ($toolkit)
  {
    $this->_toolkit = $toolkit;
  }

  /**
   * Compare against 'other' using 'operator'.
   * @param DATE_TIME $other
   * @param int $operator Can be {@link Operator_equal}, {@link Operator_less_than_equal}.
   * @param int $parts Can be {@link Date_time_date_part}, {@link Date_time_time_part} or {@link Date_time_both_parts}.
   * @return boolean
   */
  protected function _compare_to ($other, $operator, $parts)
  {
    switch ($parts)
    {
    case Date_time_both_parts:
      if ($this->_php_time != Date_time_unassigned)
      {
        $left = $this->_php_time;
        $right = $other->as_php ();
      }
      else
      {
        $left = $this->_iso_time;
        $right = $other->as_iso ();
      }
      break;

    case Date_time_date_part:
      $left = @date ('Ymd', $this->as_php ());
      $right = @date ('Ymd', $other->as_php ());
//      $left = floor (($this->as_php () + 7200) / 86400);
//      $right = floor (($other->as_php () + 7200) / 86400);
      break;

    case Date_time_time_part:
      $left = $this->as_php () % 86400;
      $right = $other->as_php () % 86400;
      break;
    }

    switch ($operator)
    {
      case Operator_equal:
        return $left == $right;
      case Operator_less_than_equal:
        return $left <= $right;
      case Operator_less_than:
        return $left < $right;
      default:
        $this->raise ("[$operator] is not supported.", '_compare_to', 'DATE_TIME');
    }
    
    return false; // Compiler warning; the call to "raise" should abort execution 
  }

  /**
   * @var integer
   *  @access private
   */
  protected $_php_time = Date_time_unassigned;

  /**
   * @var string
   *  @access private
   */
  protected $_iso_time = Date_time_unassigned;

  /**
   * Context-specific toolkit.
   * May be empty.
   * @see toolkit()
   * @see use_toolkit()
   * @var DATE_TIME_TOOLKIT
   * @access private
   */
  protected $_toolkit;
}

/**
 * Represents a time interval.
 * Uses 30 days per month when calculating number of months.
 * @package webcore
 * @subpackage date-time
 * @version 3.2.0
 * @since 2.2.1
 */
class TIME_INTERVAL
{
  /**
   * @param DATE_TIME $t1
   * @param DATE_TIME $t2
   */
  public function __construct ($t1, $t2)
  {
    $php_t1 = $t1->as_php ();
    $php_t2 = $t2->as_php ();

    $this->_total_seconds = $php_t1 - $php_t2;
  }

  /**
   * Is this interval empty (0 seconds)?
   * @return boolean
   */
  public function is_empty ()
  {
    return $this->_total_seconds == 0;
  }

  /**
   * Are the time intervals the same?
   * @param TIME_INTERVAL $other
   * @return boolean
   */
  public function equals ($other)
  {
    return $this->_total_seconds == $other->_total_seconds;
  }

  /**
   * Format the time interval.
   * Returns a string containing the time in English units. e.g. 7 years 4 months 6 days etc.
   * @param integer $num_significant_units Shows only this many units.
   * @param boolean $long_names Uses the full English name rather than abbreviations
   */
  public function format ($num_significant_units = 2, $long_names = true, $decimal_places = 0)
  {
    /* Build some constants to use for conversions. */

    $unit_sizes_in_seconds = array (31104000, 2592000, 604800, 86400, 3600, 60 );
    $unit_sizes = array (1000000, 12, 4, 7, 24, 60, 60);
    if ($long_names)
    {
      $names = array ('year', 'month', 'week', 'day', 'hour', 'minute', 'second');
    }
    else
    {
      $names = array ('y', 'm', 'w', 'd', 'h', 'm', 's');
    }

    /* Break the total number of seconds down into individual units, storing
     * years, months, weeks, etc. in an array from left to right. At the same
     * time, find the location in the array of the least significant unit (LSU).
     * If the array looks like this:
     *
     * 0, 5, 3, 2, 5, 0, 52
     *
     * And "num_significant_units" is 2, then the position of the LSU is at
     * index 2 (index 0 is empty, index 1 is most significant and index 2 is the
     * least).
     */

    $idx_unit = 0;
    $idx_least_significant = sizeof ($unit_sizes) - 1;
    $num_significant_units_found = 0;
    $s = max ($this->_total_seconds, 0);

    foreach ($unit_sizes_in_seconds as $unit_size)
    {
      $unit = intval ($s / $unit_size);
      $units [] = $unit;
      if ($unit)
      {
        if ($num_significant_units_found < $num_significant_units)
        {
          $num_significant_units_found += 1;
          if ($num_significant_units_found == $num_significant_units)
          {
            $idx_least_significant = $idx_unit;
          }
        }
      }
      $s -= $unit * $unit_size;
      $idx_unit += 1;
    }

    /* Add seconds to the end of the array in the same way as described above. */

    $unit = $s;
    $units [] = $s;
    if ($unit)
    {
      if ($num_significant_units_found < $num_significant_units)
      {
        $num_significant_units_found += 1;
        if ($num_significant_units_found == $num_significant_units)
        {
          $idx_least_significant = sizeof($units) - 1;
        }
      }
    }

    /* Now apply rounding rules.
     *
     * If "decimal_places" is non-zero, then check the unit to the right of the
     * LSU and calculate what fraction of a full unit it is. The units array
     * from above is:
     *
     * 0, 5, 3, 2, 5, 0, 52
     *
     * Adding fractions at all non-significant levels yields (with
     * decimal_places = 1):
     *
     * 0, 5, 3.3, 2.2, 5, .9, 52
     *
     * With 2 significant units, this returns "5 months, 3.3 weeks". With one
     * significant unit, it would round up to 6 months.
     *
     * If "decimal_places" is false, start with the LSU and check the unit to
     * the right of it; if it rounds up, add one to the current unit and move
     * one unit to the left until no more rounding up is needed. If the current
     * unit is equal to the max units (was rounded by the unit to its right),
     * then zero it and add one to the unit to the left.
     *
     * Given the following units:
     *
     * 0, 5, 3, 6, 15, 0, 52
     *
     * Naively, this returns 5 months, 3 weeks. With proper rounding, 15 hours
     * are rounded to a day (adding 1 day to the 6 days); the resulting 7 days
     * are rounded to 1 week and the resulting 4 weeks are rounded to 1 month.
     * The successive stages of the array are as follows:
     *
     * 0, 5, 3, 6, 15, 0, 52
     * 0, 5, 3, 6, 15, 1, 0
     * 0, 5, 3, 6, 15, 1, 0
     * 0, 5, 3, 7, 0, 1, 0
     * 0, 5, 4, 0, 0, 1, 0
     * 0, 6, 0, 0, 0, 1, 0
     *
     * With 2 significant units, this returns "6 months" (because 1 minute is
     * not significant relative to 1 month)
     *
     */

    if ($decimal_places > 0)
    {
      $idx_unit = sizeof ($units) - 2;
      while ($idx_unit > $idx_least_significant)
      {
        $unit = $units [$idx_unit + 1];
        $max_unit = $unit_sizes [$idx_unit + 1];
        $fraction = round ($unit / $max_unit, 1);
        $units [$idx_unit] += $fraction;
        $idx_unit--;
      }

      if ($idx_least_significant < sizeof ($units) - 1)
      {
        $frac_unit = $units [$idx_least_significant + 1];
        $fraction = round ($frac_unit / $unit_sizes [$idx_least_significant + 1], $decimal_places);
        $units [$idx_least_significant] += $fraction;
      }
    }
    else
    {
      $idx_unit = sizeof ($units) - 1;
      while ($idx_unit >= $idx_least_significant)
      {
        $unit = $units [$idx_unit];
        $max_unit = $unit_sizes [$idx_unit];
        if ($idx_unit == $idx_least_significant)
        {
          if ($unit == $max_unit)
          {
            $units [$idx_unit] = 0;
            $units [$idx_unit - 1] += 1;
          }
        }
        else
        {
          $fraction = ($unit / $max_unit);
          if ($fraction >= 0.5)
          {
            $units [$idx_unit] = 0;
            $units [$idx_unit - 1] += 1;
          }
        }
        $idx_unit--;
      }
    }

    /* At this point, the units in the array are correct, so format the
     * required significant units and return them. If some units have already
     * been formatted and a zero is encountered, then skip all remaining units
     * as they are not significant. From the exampe above, we had:
     *
     * 0, 6, 0, 0, 0, 1, 0
     *
     * This yields "6 months" regardless of how many units are requested because
     * there are no other significant units available (it doesn't make sense to
     * return 6 months, 1 minute in any case).
     *
     */

    $num_units = sizeof ($units);
    $formatted_units = array ();
    $idx_unit = 0;
    while ((sizeof ($formatted_units) < $num_significant_units) && ($idx_unit < $num_units))
    {
      $unit = $units [$idx_unit];

      if ($unit)
      {
        $unit_name = $names [$idx_unit];
        if ($long_names)
        {
          if ($unit != 1)
          {
            $formatted_units [] = "$unit {$unit_name}s";
          }
          else
          {
            $formatted_units [] = "$unit $unit_name";
          }
        }
        else
        {
          $formatted_units [] = $unit . $unit_name;
        }
      }
      else
      {
        if (sizeof ($formatted_units))
        {
          $num_significant_units = 0;
        }
      }

      $idx_unit += 1;
    }

    $Result = implode (' ', $formatted_units);

    if (! $Result)
    {
      if ($long_names)
      {
        $Result = '0 seconds';
      }
      else
      {
        $Result = '0s';
      }
    }

    return trim ($Result);
  }

  /**
   * @var integer
   * @access private
   */
  protected $_total_seconds;
}

/**
 * Returns the global date/time toolkit.
 * Used by {@link DATE_TIME} if no other toolkit is assigned.
 * @return DATE_TIME_TOOLKIT
 * @access private
 */
function global_date_time_toolkit ()
{
  global $_g_date_time_toolkit;
  if (! isset ($_g_date_time_toolkit))
  {
    $_g_date_time_toolkit = new DATE_TIME_TOOLKIT ();
  }
  return $_g_date_time_toolkit;
}

/**
 * Cached copy of file options.
 * Accessed using {@link global_file_options()}.
 * @global FILE_OPTIONS
 * @access private
 */
$_g_date_time_toolkit = null;

?>
