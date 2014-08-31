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

var Calendar_show_time = false;
var Calendar_show_year_selector = true;
var Calendar_show_month_selector = false;
var Calendar_show_now_selector = true;
var Calendar_first_day_of_week = 1;
var Calendar_page_name = 'date_picker.html';
var Calendar_close_on_select = true;

var Calendar_style_main = 'basic';
var Calendar_style_weekday = 'weekday';
var Calendar_style_today = 'calendar-today';
var Calendar_style_weekend_day = 'calendar-weekend-day';
var Calendar_style_month_day = 'calendar-month-day';
var Calendar_style_other_day = 'calendar-other-day';

var Date_time_field_output_format = Date_format_euro;

/** Global list of all date pickers in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a date was selected. */
var date_time_fields = [];

/**
 * Create a component to select a date-time.
 * Uses a popup window to display a calendar and transform the chosen date into
 * the desired format.
 * @return DATE_TIME_FIELD
 */
function DATE_TIME_FIELD()
{
  /** 2-digit years less than this considered 20th century. */
  this.century_break = Date_century_break;
  /** Show the time in the control? */
  this.show_time = Calendar_show_time;
  /** Show a month selector control? */
  this.show_month_selector = Calendar_show_month_selector;
  /** Name of the calendar page. */
  this.page_name = Calendar_page_name;
  /** How to format dates? */
  this.output_format = Date_time_field_output_format;

  this.title = 'Select a date';

  return this;
}

/**
 * Attach the given control to a {DATE_TIME_FIELD} with the given
 *
 * @param ctrl
 * @param {string} title
 */
DATE_TIME_FIELD.prototype.attach = function (ctrl, title)
{
  if (!ctrl || ctrl.value == null)
  {
    DATE_TIME_FIELD.prototype.report_error("Please specify a valid form control.");
  }

  if (title)
  {
    this.title = title;
  }

  this.control = ctrl;

  this.id = date_time_fields.length;
  date_time_fields [this.id] = this;

  this.refresh();
};

/**
 * Show an error message.
 *
 * @param {string} msg
 */
DATE_TIME_FIELD.prototype.report_error = function (msg)
{
  report_error('DATE_TIME_FIELD: ' + msg);
};

/**
 * Apply a new value to the form control.
 *
 * Generally called from the CALENDAR object in the page displayed with 'show_calendar'.
 *
 * @param {Date|int|string} dt
 */
DATE_TIME_FIELD.prototype.set_value = function (dt)
{
  this.control.value = this.as_string(this.as_js_date(dt));
};

/**
 * Re-apply current value to control.
 *
 * Use this to make sure the control reflects the current display properties.
 */
DATE_TIME_FIELD.prototype.refresh = function ()
{
  if (this.control.value)
    this.set_value();
};

/**
 * Converts this picker's date to a JavaScript Date object.
 *
 * Uses 'initial_date_time' if given. If that is empty, it uses the date/time displayed
 * in the target control. If that is empty, it uses the current date/time.
 *
 * @param {string} initial_date_time
 * @return {Date}
 */
DATE_TIME_FIELD.prototype.as_js_date = function (initial_date_time)
{
  var Result = null;

  var dt = initial_date_time;
  if (!dt)
  {
    dt = this.control.value;
  }

  if (dt)
  {
    if (dt instanceof Date)
    {
      Result = dt;
    }
    else if ((typeof (dt) == 'number') || Date_is_number.exec(dt))
    {
      // if it's a number, assume its milliseconds

      Result = new Date(dt);
    }
    else
    {
      Result = date_time_from_string(dt, this.century_break);
    }
  }
  else
  {
    Result = new Date();
  }

  return Result;
};

/** Return the given date as a string.
 * @param dt Date
 * @return string */
DATE_TIME_FIELD.prototype.as_string = function (dt)
{
  var fmt = this.output_format;
  if (this.show_time)
    fmt += ' H:i:s';

  return format_date_time(dt, fmt);
};

/** Constant used with 'offset_date()'. */
var Calendar_year = 1;
/** Constant used with 'offset_date()'. */
var Calendar_month = 2;

/** Global list of all calendars in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a date was selected. */
var calendars = [];

/**
 * A 'picker' that interacts with a DATE_TIME_FIELD.
 *
 * Provides abstract representation of a calendar.
 *
 * @see HTML_CALENDAR */
function CALENDAR()
{
  /** Show control to select the time? */
  this.show_time = Calendar_show_time;
  /** Show controls to change the year? */
  this.show_year_selector = Calendar_show_year_selector;
  /** Show a drop-down for selecting months? */
  this.show_month_selector = Calendar_show_month_selector;
  /** Show a button to select current date and time? */
  this.show_now_selector = Calendar_show_now_selector;
  /** Which day should start the week? */
  this.first_day_of_week = Calendar_first_day_of_week;
  /** Close the calendar when a selection is made? */
  this.close_on_select = Calendar_close_on_select;
}

/**
 *
 * @param {Date} initial_date_time
 */
CALENDAR.prototype.set_initial_date_time = function (initial_date_time)
{
  if (initial_date_time)
  {
    this.today = initial_date_time;
  }
  else
  {
    this.today = new Date();
  }

  this.now = new Date();

  this.previous_year = this.offset_date(Calendar_year, -1);
  this.next_year = this.offset_date(Calendar_year, 1);
  this.previous_month = this.offset_date(Calendar_month, -1);
  this.next_month = this.offset_date(Calendar_month, 1);

  this.first_day = new Date(this.today);
  this.first_day.setDate(1);
  this.first_day.setDate(1 - (7 + this.first_day.getDay() - this.first_day_of_week) % 7);

  this.show_time = parseBool(Query_string.item('showtime' + this.id));
  this.show_month_selector = parseBool(Query_string.item('showmonthsel' + this.id));
};

CALENDAR.prototype.attach = function (field)
{
  this.id = field.id;
  this.field = field;

  calendars [this.id] = this;
};

/**
 * Get a date based on the current date.
 *
 * Apply the specified offset for a year or month.
 *
 * @param {int} part Can be 'Calendar_year' or 'Calendar_month'
 * @param {int} offset
 *
 * @return Date
 */
CALENDAR.prototype.offset_date = function (part, offset)
{
  var current = this.today;
  var Result = new Date(current.getFullYear(), current.getMonth(), current.getDay(), current.getHours(), current.getMinutes(), current.getSeconds(), current.getMilliseconds());

  switch (part)
  {
    case Calendar_year:
      Result.setFullYear(current.getFullYear() + offset);
      break;
    case Calendar_month:
      Result.setMonth(current.getMonth() + offset);
      break;
  }

  return Result;
};

/**
 * Close the calendar and update the date picker that launched it.
 *
 * Do not pass a Javascript Date here, use Date.valueOf() instead.
 *
 * @param {string} utc_formatted_time
 */
CALENDAR.prototype.select_date = function (utc_formatted_time)
{
  var date = new Date(Date.parse(utc_formatted_time));

  this.field.set_value(date);

  closeMenus(document);
};

/**
 * Update the time and close the calendar.
 *
 * @param {string} ts
 */
CALENDAR.prototype.select_time = function (ts)
{
  var ds = format_date_time(this.today, Date_format_iso);
  var d = date_time_from_string(ds + ' ' + ts);

  this.select_date(d.valueOf());
};

/**
 * Refocus the calendar on the given date.
 *
 * Do not pass a Javascript Date here, use Date.valueOf() instead.
 *
 * @param {string} utc_formatted_time
 */
CALENDAR.prototype.change_date = function (utc_formatted_time)
{
  var dom_element = $q('#' + this.field.control.id + '_field').first();

  var date = new Date(Date.parse(utc_formatted_time));

  this.set_initial_date_time(date);

  dom_element.innerHTML = this.getText();
};

/**
 * Show an error message.
 *
 * @param {string} msg
 */
CALENDAR.prototype.report_error = function (msg)
{
  report_error('CALENDAR: ' + msg);
};

/**
 * Does this date match the selected date?
 *
 * @param {Date} d
 * @return boolean */
CALENDAR.prototype.is_today = function (d)
{
  return (d.getDate() == this.today.getDate()) && (d.getMonth() == this.today.getMonth());
};

/**
 * Is this date a weekend day?
 * @param {Date} d
 * @return {boolean}
 */
CALENDAR.prototype.is_weekend_day = function (d)
{
  return (d.getDay() == 0) || (d.getDay() == 6);
};

/**
 * Is this date in the same month as the selected date?
 * @param {Date} d
 * @returns {boolean}
 */
CALENDAR.prototype.is_same_month = function (d)
{
  return d.getMonth() == this.today.getMonth();
};

/**
 * Output a string to the destination document.
 * @param {string} s
 */
CALENDAR.prototype.echo = function (s)
{
  document.write(s + "\n");
};

/** Create a CALENDAR that displays as HTML.
 * Call 'display' to show the calendar in a page. */
function HTML_CALENDAR()
{
  CALENDAR.call();

  this.main_style = Calendar_style_main;
  this.weekday_style = Calendar_style_weekday;
  this.today_style = Calendar_style_today;
  this.weekend_day_style = Calendar_style_weekend_day;
  this.month_day_style = Calendar_style_month_day;
  this.other_day_style = Calendar_style_other_day;
}

HTML_CALENDAR.prototype = new CALENDAR;

/**
 * Return JavaScript to select this date.
 *
 * @param {int} dt
 * @param {string} func_name
 * @return {string}
 */
HTML_CALENDAR.prototype.make_javascript_selector = function (dt, func_name)
{
  return 'calendars [' + this.id + '].' + func_name + ' (\'' + dt + '\')';
};

/**
 * Return a link to call the given calendar function.
 *
 * @param {Date} d
 * @param {string} text
 * @param {string} func_name
 * @param {string} css_class
 * @return {string}
 */
HTML_CALENDAR.prototype.make_link = function (d, text, func_name, css_class)
{
  var Result = '<a ';

  if (css_class)
  {
    Result += 'class="' + css_class + '" ';
  }

  Result += 'href="javascript:' + this.make_javascript_selector(d.toUTCString(), func_name) + '">' + text + '</a>';

  return Result;
};

/**
 * Return a link to select the given date.
 *
 * @param {Date} d
 * @param {string} text
 * @return {string}
 */
HTML_CALENDAR.prototype.make_selector_link = function (d, text)
{
  return this.make_link(d, text, 'select_date');
};

/**
 * Return a link to change to the given date.
 *
 * @param {Date} d
 * @param {string} text
 * @param {string} css_class
 * @return {string}
 */
HTML_CALENDAR.prototype.make_changer_link = function (d, text, css_class)
{
  return this.make_link(d, text, 'change_date', css_class);
};

HTML_CALENDAR.prototype.get_month_selector = function ()
{
  var result = '';
  result += '<select class="tiny-small" onChange="' + this.make_javascript_selector('this.value', 'change_date') + '">';
  var month_day = new Date(this.today);
  for (var idxMonth = 0; idxMonth < 12; idxMonth++)
  {
    var selText;
    month_day.setMonth(idxMonth);
    if (idxMonth == this.today.getMonth())
    {
      selText = ' selected';
    }
    else
    {
      selText = '';
    }

    result += '<option value="' + month_day.valueOf() + '"' + selText + '>' + Month_short_names [idxMonth] + '</option>';
  }
  result += '</select>';
  result += this.today.getFullYear();

  return result;
};

HTML_CALENDAR.prototype.getText = function ()
{
  var result = '<div class="button-content">';

  result += '<ul class="menu-items buttons">';

  if (this.show_year_selector)
  {
    result += '<li>' + this.make_changer_link(this.previous_year, '<<') + '</li>';
  }

  result += '<li>' + this.make_changer_link(this.previous_month, '<') + '</li>';
  result += '<li>' + this.make_changer_link(this.next_month, '>') + '</li>';

  if (this.show_year_selector)
  {
    result += '<li>' + this.make_changer_link(this.next_year, '>>') + '</li>';
  }

  if (this.show_month_selector)
  {
    result += '<li>' + this.get_month_selector() + '</li>';
  }
  else
  {
    result += '<li>' + Month_names [this.today.getMonth()] + ' ' + this.today.getFullYear() + '</li>';
  }

  if (this.show_time || this.show_now_selector)
  {
    if (this.show_time)
    {
      result += '<li>' + '<input id="time' + this.id + '" type="text" value="' + format_date_time(this.today, 'H:i:s') + '" maxlength="8" size="8">' + '</li>';
    }
    if (this.show_now_selector)
    {
      result += '<li>' + this.make_changer_link(this.now, 'Now') + '</li>';
    }
  }

  result += '</ul></div>';

  result += '<table class="' + this.main_style + ' mini">';

  result += '<tr>';
  for (var idxDow = 0; idxDow < 7; idxDow++)
  {
    result += '<td class="' + this.weekday_style + '">' + Weekday_short_names [(this.first_day_of_week + idxDow) % 7] + '</td>';
  }
  result += '</tr>';

  var current = new Date(this.first_day);
  while ((current.getMonth() == this.today.getMonth())
    || current.getMonth() == this.first_day.getMonth())
  {
    result += '<tr>';
    for (var idxDow = 0; idxDow < 7; idxDow++)
    {
      var css_class;
      if (this.is_today(current))
      {
        css_class = this.today_style;
      }
      else if (!this.is_same_month(current))
      {
        css_class = this.other_day_style;
      }
      else if (this.is_weekend_day(current))
      {
        css_class = this.weekend_day_style;
      }
      else
      {
        css_class = this.month_day_style;
      }

      result += '<td class="' + css_class + '">' + this.make_selector_link(current, current.getDate()) + '</td>';

      current.setDate(current.getDate() + 1);
    }
    result += '</tr>';
  }
  result += '</table>';

  return result;
}

/** Draw an HTML-table-based calendar with JavaScript. */
HTML_CALENDAR.prototype.display = function ()
{
  this.echo(this.getText());
};

/**
 * HTML Calendar that uses WebCore styles and images.
 */
function WEBCORE_HTML_CALENDAR()
{
  HTML_CALENDAR.call();

  this.today_style = 'cell-selected';
  this.weekend_day_style = 'cell-highlight';
  this.month_day_style = 'cell-non-empty';
  this.other_day_style = 'cell-empty';
}

WEBCORE_HTML_CALENDAR.prototype = new HTML_CALENDAR;

/**
 * {DATE_TIME_FIELD} that uses a WebCore calendar.
 */
function WEBCORE_DATE_TIME_FIELD()
{
  this.page_name = 'date_picker.php';
}

WEBCORE_DATE_TIME_FIELD.prototype = new DATE_TIME_FIELD;

