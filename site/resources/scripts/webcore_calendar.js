/****************************************************************************

Copyright (c) 2002-2005 Marco Von Ballmoos

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
var Calendar_width = 200;
var Calendar_height = 200;
var Calendar_close_on_select = true;

var Calendar_style_main = 'calendar';
var Calendar_style_banner = 'calendar-banner';
var Calendar_style_title = 'calendar-title';
var Calendar_style_body = 'calendar-body';
var Calendar_style_footer = 'calendar-footer';
var Calendar_style_weekday_banner = 'calendar-weekday-banner';
var Calendar_style_weekday = 'calendar-weekday';
var Calendar_style_today = 'calendar-today';
var Calendar_style_weekend_day = 'calendar-weekend-day';
var Calendar_style_month_day = 'calendar-month-day';
var Calendar_style_other_day = 'calendar-other-day';

var Calendar_style_menu_control = 'menu-control';
var Calendar_style_text_control = 'text-control';

var Calendar_image_path = '';

var Calendar_image_previous_year = 'previous_year.png';
var Calendar_image_next_year = 'next_year.png';
var Calendar_image_previous_month = 'previous_month.png';
var Calendar_image_next_month = 'next_month.png';
var Calendar_image_now = 'now.png';

var Date_time_field_output_format = Date_format_euro;

/** Global list of all date pickers in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a date was selected. */
var date_time_fields = [];

/** Create a component to select a date-time.
 * Uses a popup window to display a calendar and transform the chosen date into
 * the desired format. */
function DATE_TIME_FIELD ()
{
  /** 2-digit years less than this considered 20th century. */
  this.century_break = Date_century_break;
  /** Show the time in the control? */
  this.show_time = Calendar_show_time;
  /** Show a month selector control? */
  this.show_month_selector = Calendar_show_month_selector;
  /** Name of the calendar page. */
  this.page_name = Calendar_page_name;
  /** How wide should the calendar window be? */
  this.width = Calendar_width;
  /** How high should the calendar window be? */
  this.height = Calendar_height;
  /** How to format dates? */
  this.output_format = Date_time_field_output_format;

  this.title = 'Select a date';
}

DATE_TIME_FIELD.prototype.attach = function (ctrl, title)
{
  if (! ctrl || ctrl.value == null)
	DATE_TIME_FIELD.prototype.report_error ("Please specify a valid form control.");

  if (title)
	this.title = title;

  this.control = ctrl;

  this.id = date_time_fields.length;
  date_time_fields [this.id] = this;

  this.refresh ();
}

/** Show an error message.
 * @param string msg */
DATE_TIME_FIELD.prototype.report_error = function (msg)
{
  report_error ('DATE_TIME_FIELD: ' + msg);
}

/** Show the calendar window positioned on the given date.
 * Uses 'initial_date_time' if given. If that is empty, it uses the date/time displayed
 * in the target control. If that is empty, it uses the current date/time.
 * @param string initial_date_time */
DATE_TIME_FIELD.prototype.show_calendar = function (initial_date_time)
{
  var dt = this.as_js_date (initial_date_time);

  var window_location = this.page_name + '?'
	                  + 'datetime' + this.id + '=' + dt.valueOf ()
					  + '&showtime' + this.id + '=' + this.show_time
					  + '&showmonthsel' + this.id + '=' + this.show_month_selector
				  	  + '&id=' + this.id;

  if (this.calendar && ! this.calendar.closed)
  {
	this.calendar.location = window_location;
  }
  else
  {
	h = this.height;
	if (this.show_time)
	  h += 20;

	var window_params = 'width=' + this.width
					  + ',height=' + h
					  + ',status=no,resizable=no,dependent=yes,alwaysRaised=yes';

	/* IE doesn't like this object property...so hack around it. */

	var window_title = ''; // + this.title;

	this.calendar = window.open (window_location, window_title, window_params);
	this.calendar.focus ();
  }
}

/** Apply a new value to the form control.
 * Generally called from the CALENDAR object in the page displayed with 'show_calendar'.
 * @param Date|integer|string */
DATE_TIME_FIELD.prototype.set_value = function (dt)
{
  this.control.value = this.as_string (this.as_js_date (dt));
}

/** Re-apply current value to control.
 * Use this to make sure the control reflects the current display properties. */
DATE_TIME_FIELD.prototype.refresh = function ()
{
  if (this.control.value)
	this.set_value ();
}

/** Converts this picker's date to a JavaScript Date object.
 * Uses 'initial_date_time' if given. If that is empty, it uses the date/time displayed
 * in the target control. If that is empty, it uses the current date/time.
 * @param string initial_date_time
 * @return Date */
DATE_TIME_FIELD.prototype.as_js_date = function (initial_date_time)
{
  var Result = null;

  var dt = initial_date_time;
  if (! dt)
	dt = this.control.value;

  if (dt)
  {
	// if its a number, assume its milliseconds
	if ((typeof (dt) == 'number') || Date_is_number.exec (dt))
	  Result = new Date (dt);
	else
	  Result = date_time_from_string (dt, this.century_break);
  }
  else
	Result = new Date ();

  return Result;
}

/** Return the given date as a string.
 * @param Date dt
 * @return string */
DATE_TIME_FIELD.prototype.as_string = function (dt)
{
  fmt = this.output_format;
  if (this.show_time)
	fmt += ' H:i:s';

  return format_date_time (dt, fmt);
}

/** Constant used with 'offset_date()'. */
var Calendar_year = 1;
/** Constant used with 'offset_date()'. */
var Calendar_month = 2;

/** Global list of all calendars in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a date was selected. */
var calendars = [];

/** A 'picker' that interacts with a DATE_TIME_FIELD.
 * Provides abstract representation of a calendar.
 * @see HTML_CALENDAR */
function CALENDAR ()
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

/** Registers a calendar with a DATE_TIME_FIELD 'id'.
 * Pass in an id, if the calendar should interact with a field and an optional date/time.
 * @param integer id
 * @param integer initial_date_time Should be the result of calling valueOf() on a Date object. */
CALENDAR.prototype.register = function (id, initial_date_time)
{
  if (! initial_date_time)
	initial_date_time = parseInt (Query_string.item ('datetime' + id));

  this.id = id;
  this.now = new Date ();

  this.show_time = parseBool (Query_string.item ('showtime' + id));
  this.show_month_selector = parseBool (Query_string.item ('showmonthsel' + id));

  if (initial_date_time)
	this.today = new Date (initial_date_time);
  else
	this.today = new Date ();

  if (window.opener && window.opener.date_time_fields)
	this.date_time_field = window.opener.date_time_fields [id];
  if (! this.date_time_field)
	CALENDAR.prototype.report_error ("Date picker not found.");

  this.previous_year = this.offset_date (Calendar_year, -1);
  this.next_year = this.offset_date (Calendar_year, 1);
  this.previous_month = this.offset_date (Calendar_month, -1);
  this.next_month = this.offset_date (Calendar_month, 1);

  this.first_day = new Date (this.today);
  this.first_day.setDate (1);
  this.first_day.setDate (1 - (7 + this.first_day.getDay () - this.first_day_of_week) % 7);

  calendars [this.id] = this;
}

/** Get a date based on the current date.
 * Apply the specified offset for a year or month.
 * @param integer part Can be 'Calendar_year' or 'Calendar_month'
 * @param integer offset
 * @return Date */
CALENDAR.prototype.offset_date = function (part, offset)
{
  Result = new Date (this.today);

  switch (part)
  {
  case Calendar_year:
	Result.setFullYear (this.today.getFullYear () + offset);
	break;
  case Calendar_month:
	Result.setMonth (this.today.getMonth () + offset);
	break;
  }

  if (Result.getDate () != this.today.getDate ())
	Result.setDate (0);

  return Result;
}


/** Close the calendar and update the date picker that launched it.
 * Do not pass a Javascript Date here, use Date.valueOf() instead.
 * @param integer dt */
CALENDAR.prototype.select_date = function (dt)
{
  this.date_time_field.set_value (parseInt (dt));
  if (this.close_on_select)
	window.close ();
  else
	this.change_date (dt);
}

/** Update the time and close the calendar.
 * @param string ts */
CALENDAR.prototype.select_time = function (ts)
{
  ds = format_date_time (this.today, Date_format_iso);
  d = date_time_from_string (ds + ' ' + ts);

  this.select_date (d.valueOf ());
}

/** Refocus the calendar on the given date.
 * Do not pass a Javascript Date here, use Date.valueOf() instead.
 * @param integer dt */
CALENDAR.prototype.change_date = function (dt)
{
  this.date_time_field.show_calendar (parseInt (dt));
}

/** Show an error message.
 * @param string msg */
CALENDAR.prototype.report_error = function (msg)
{
  report_error ('CALENDAR: ' + msg);
}

/** Does this date match the selected date?
 * @param Date d
 * @return boolean */
CALENDAR.prototype.is_today = function (d)
{
  return (d.getDate () == this.today.getDate ()) && (d.getMonth () == this.today.getMonth ());
}

/** Is this date a weekend day?
 * @param Date d
 * @return boolean */
CALENDAR.prototype.is_weekend_day = function (d)
{
  return (d.getDay () == 0) || (d.getDay () == 6);
}

/** Is this date in the same month as the selected date?
 * @param Date d
 * @return boolean */
CALENDAR.prototype.is_same_month = function (d)
{
  return d.getMonth () == this.today.getMonth ();
}

/** Output a string to the destination document.
 * @param string s */
CALENDAR.prototype.echo = function (s)
{
  document.write (s + "\n");
}

/** Create a CALENDAR that displays as HTML.
 * Call 'display' to show the calendar in a page. */
function HTML_CALENDAR ()
{
  this.main_style = Calendar_style_main;
  this.banner_style = Calendar_style_banner;
  this.title_style = Calendar_style_title;
  this.body_style = Calendar_style_body;
  this.footer_style = Calendar_style_footer;
  this.weekday_banner_style = Calendar_style_weekday_banner;
  this.weekday_style = Calendar_style_weekday;
  this.today_style = Calendar_style_today;
  this.weekend_day_style = Calendar_style_weekend_day;
  this.month_day_style = Calendar_style_month_day;
  this.other_day_style = Calendar_style_other_day;

  this.menu_control_style = Calendar_style_menu_control;
  this.text_control_style = Calendar_style_text_control;

  this.image_path = Calendar_image_path;
  this.previous_year_image = Calendar_image_previous_year;
  this.next_year_image = Calendar_image_next_year;
  this.previous_month_image = Calendar_image_previous_month;
  this.next_month_image = Calendar_image_next_month;
  this.today_now = Calendar_image_now;
}
HTML_CALENDAR.prototype = new CALENDAR;

/** Adjust the image file name, if necessary.
 * @param string file_name
 * @return string */
HTML_CALENDAR.prototype.expand_image_file_name = function (file_name)
{
  return this.image_path + file_name;
}

/** Return JavaScript to select this date.
 * @param integer dt
 * @param string func_name
 * @return string */
HTML_CALENDAR.prototype.make_javascript_selector = function (dt, func_name)
{
  return 'calendars [' + this.id + '].' + func_name + ' (' + dt + ')';
}

/** Return an icon as HTML.
 * Resolves the icon to the propert path and uses the default extension for the chosen theme.
 * @param string file_name
 * @param string title
 * @return string */
HTML_CALENDAR.prototype.icon_as_html = function (file_name, title)
{
  return '<img src="' + this.expand_image_file_name (file_name) + '" title="' + title + '" alt="' + title + '" style="vertical-align: middle">';
}

/** Return a link to call the given calendar function.
 * @param Date d
 * @param string text
 * @param string func_name
 * @return string */
HTML_CALENDAR.prototype.make_link = function (d, text, func_name)
{
  return '<a href="javascript:' + this.make_javascript_selector (d.valueOf (), func_name) + '">' + text + '</a>';
}

/** Return a link to select the given date.
 * @param Date d
 * @param string text
 * @return string */
HTML_CALENDAR.prototype.make_selector_link = function (d, text)
{
  return this.make_link (d, text, 'select_date');
}

/** Return a link to change to the given date.
 * @param Date d
 * @param string text
 * @return string */
HTML_CALENDAR.prototype.make_changer_link = function (d, text)
{
  return this.make_link (d, text, 'change_date');
}

HTML_CALENDAR.prototype.draw_month_selector = function ()
{
  this.echo ('<select class="' + this.menu_control_style + '" onChange="' + this.make_javascript_selector ('this.value', 'change_date') + '">');
  month_day = new Date (this.today);
  for (idxMonth = 0; idxMonth < 12; idxMonth++)
  {
	month_day.setMonth (idxMonth);
	if (idxMonth == this.today.getMonth ())
	  selText = ' selected';
	else
	  selText = '';
	this.echo ('<option value="' + month_day.valueOf () + '"' + selText + '>' + Month_short_names [idxMonth] + '</option>');
  }
  this.echo ('</select>');
  this.echo (this.today.getFullYear ());
}

HTML_CALENDAR.prototype.draw_time_selector = function ()
{
  this.echo ('<div style="text-align: center">');
  if (this.show_time)
  {
	this.echo ('<form onSubmit="javascript:' + this.make_javascript_selector ('this.time' + this.id + '.value', 'select_time') + '">');
	this.echo ('<input id="time' + this.id + '" class="' + this.text_control_style + '" type="text" value="' + format_date_time (this.today, 'H:i:s') + '" maxlength="8" size="8">');
	this.echo ('</form>');
  }
  if (this.show_now_selector)
	this.echo (this.make_changer_link (this.now, this.icon_as_html (this.now_image, 'Move to current date and time')));
  this.echo ('</div>');
}

/** Draw an HTML-table-based calendar with JavaScript. */
HTML_CALENDAR.prototype.display = function ()
{
  this.echo ('<table class="' + this.main_style + '">');
	this.echo ('<tr><td class="' + this.banner_style + '">');
	  this.echo ('<table>');
		this.echo ('<tr><td style="white-space: nowrap">');
  		  if (this.show_year_selector)
			this.echo (this.make_changer_link (this.previous_year, this.icon_as_html (this.previous_year_image, 'Move to previous year')));
		  this.echo (this.make_changer_link (this.previous_month, this.icon_as_html (this.previous_month_image, 'Move to previous month')));
		this.echo ('</td><td class="' + this.title_style + '" style="width: 100%">');
  		  if (this.show_month_selector)
			this.draw_month_selector ();
		  else
			this.echo (Month_names [this.today.getMonth ()] + ' ' + this.today.getFullYear ());
		this.echo ('</td><td style="white-space: nowrap">');
		  this.echo (this.make_changer_link (this.next_month, this.icon_as_html (this.next_month_image, 'Move to next month')));
		  if (this.show_year_selector)
			this.echo (this.make_changer_link (this.next_year, this.icon_as_html (this.next_year_image, 'Move to next year')));
		this.echo ('</td></tr>');
	  this.echo ('</table>');
	this.echo ('</td></tr>');
	this.echo ('<tr><td>');
	  this.echo ('<table class="' + this.body_style + '" cellspacing="0" style="width: 100%">');
		
		this.echo ('<tr class="' + this.weekday_banner_style + '">');
		  for (var idxDow = 0; idxDow < 7; idxDow++)
			this.echo ('<td class="' + this.weekday_style + '">' + Weekday_short_names [(this.first_day_of_week + idxDow) % 7] + '</td>');
		this.echo ('</tr>');
		
		current = new Date (this.first_day);
		while ((current.getMonth () == this.today.getMonth ())
			   || current.getMonth () == this.first_day.getMonth ())
		{
		  this.echo ('<tr>');
		  for (var idxDow = 0; idxDow < 7; idxDow++)
		  {
			if (this.is_today (current))
			  CSS_class = this.today_style;
			else if (! this.is_same_month (current))
			  CSS_class = this.other_day_style;
			else if (this.is_weekend_day (current))
			  CSS_class = this.weekend_day_style;
			else
			  CSS_class = this.month_day_style;

			this.echo ('<td class="' + CSS_class + '">' + this.make_selector_link (current, current.getDate ()) + '</td>');

			current.setDate (current.getDate () + 1);
		  }
		  this.echo ('</tr>');
		}
	  this.echo ('</table>');
	this.echo ('</td></tr>');
	if (this.show_time || this.show_now_selector)
	{
	  this.echo ('<tr><td class="' + this.footer_style + '">');
		this.draw_time_selector ();
	  this.echo ('</td></tr>');
	}
  this.echo ('</table>');
}

/** HTML Calendar that uses WebCore styles and images. */
function WEBCORE_HTML_CALENDAR ()
{
  this.today_style = 'cell-selected';
  this.weekend_day_style = 'cell-highlight';
  this.month_day_style = 'cell-non-empty';
  this.other_day_style = 'cell-empty';

  this.previous_year_image = 'go_to_first';
  this.next_year_image = 'go_to_last';
  this.previous_month_image = 'go_to_previous';
  this.next_month_image = 'go_to_next';
  this.now_image = 'now';
}
WEBCORE_HTML_CALENDAR.prototype = new HTML_CALENDAR;

/** Reroute image requests to the icons folder.
 * Also applies the default icon extension for the theme.
 * @param string file_name */
WEBCORE_HTML_CALENDAR.prototype.expand_image_file_name = function (file_name)
{
  return image_path + 'buttons/' + file_name + '_16px.' + image_extension;
}

/** DATE_TIME_FIELD that uses a WebCore calendar. */
function WEBCORE_DATE_TIME_FIELD ()
{
  this.page_name = 'date_picker.php';
}
WEBCORE_DATE_TIME_FIELD.prototype = new DATE_TIME_FIELD;

