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

function toggle_selected (control)
{
	if (control.checked)
		select_none (control)
	else
		select_all (control)
}

function select_all (control)
{
	select_items (control, true)
}

function select_none (control)
{
	select_items (control, false)
}

function select_items (control, b)
{
	if (control)
	{
		control.checked = b
		for (var i = 0; i < control.length; i++)
		{
			control [i].checked = b
		}
	}
}

function select_item (control, value, b)
{
	if (control)
	{
		if (control.length)
		{
			for (var i = 0; i < control.length; i++)
			{
				if (control [i].value == value)
					control [i].checked = b;
			}
		}
		else
		{
			if (control.value == value)
				control.checked = b;
		}
	}
}

function select_first_available (control)
{
  if (control)
  {
	if (control.length)
	{
	  for (var i = 0; i < control.length; i++)
	  {
		  if (! control [i].disabled)
			  control [i].checked = true;
	  }
	}
	else
	{
		if (! control [i].disabled)
			control.checked = true;
	}
  }
}

function enable_items (control, b)
{
	if (control)
	{
		control.disabled = ! b
		for (var i = 0; i < control.length; i++)
		{
			control [i].disabled = ! b
		}
	}
}

function enable_item (control, value, b)
{
	if (control)
	{
		if (control.length)
		{
			for (var i = 0; i < control.length; i++)
			{
				if (control [i].value == value)
					control [i].disabled = ! b;
			}
		}
		else
		{
			if (control.value == value)
				control.disabled = ! b;
		}
	}
}

function has_selected (control)
{
	var Result = false

	if (control)
	{
		if (control.length)
		{
			for (var i = 0; i < control.length; i++)
			{
				if ((control [i].checked) && (! control [i].disabled))
					break;
			}

			Result = (i < control.length);
		}
		else
			Result = control.checked && ! control.disabled;
	}

	return Result;
}

function is_selected (control, value)
{
	var Result = false;

	if (control)
	{
		if (control.length)
		{
			for (var i = 0; i < control.length; i++)
			{
				if (control [i].value == value)
					return control [i].checked && ! control [i].disabled;
			}

			return false;
		}
		else
			Result = (control.value == value) && control.checked && ! control.disabled;
	}

	return Result;
}

function ensure_not_selected (control, value)
{
	if (control)
	{
		if (is_selected (control, value))
		{
			if (control.length)
			{
				for (var i = 0; i < control.length; i++)
				{
					if (control [i].value != value)
					{
						control [i].checked = true;
						break;
					}
				}
			}
			else
			{
				if (control.value == value)
					control.checked = false;
			}
		}
	}
}

function ensure_has_selected (control, value)
{
	if (control)
	{
		if (! has_selected (control))
		{
			/* If there are only selected items that are disabled, then 'has_selected' also returns
			   false. Since we will be selecting an item, make sure that all disabled/selected items
			   are de-selected and that the selected 'value' is the only selected one. If the selected
			   'value' is not selectable, then just select the first available item. */

			select_none (control);
			select_item (control, value, 1);
			if (! has_selected (control))
			  select_first_available (control);
		}
	}
}

function first_selected (control)
{
	Result = false;
	var i = 0;
	while ((i < control.length) && (! Result))
	{
		Result = control [i].checked;
		i++;
	}

	if (Result)
		return control [i - 1].value;
	else
		return 0;
}

function enable_all_controls (f)
{
	for (var idx = 0; idx < f.length; idx++)
		f [idx].disabled = false;
}

function toggle_all_controls_of_type (form, type)
{
  for (var idx = 0; idx < form.elements.length; idx++)
  {
	if (form.elements [idx].type == type)
	  toggle_selected (form.elements [idx]);
  }
}

function set_all_controls_of_type (form, type, value)
{
  for (var idx = 0; idx < form.elements.length; idx++)
  {
	if (form.elements [idx].type == type)
	{
	  if ((type == 'checkbox') || (type == 'radio'))
		select_items (form.elements [idx], value);
	  else
		form.elements [idx].value = value;
	}
  }
}

function insert_text (ctrl, text)
{
  // IE support

  if (document.selection) {
    ctrl.focus();
    sel = document.selection.createRange();
    sel.text = text;
  }

  // MOZILLA/NETSCAPE support

  else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
    var startPos = ctrl.selectionStart;
    var endPos = ctrl.selectionEnd;
    ctrl.value = ctrl.value.substring(0, startPos)
                  + text
                  + ctrl.value.substring(endPos, ctrl.value.length);
  } else {
    ctrl.value += text;
  }
}

/* Returns a character offset based on line number and column; units are 1-based.
 * @param String text Determine position within this text
 * @param String lineNo Starting line
 * @param String column Starting column */
function line_and_column_to_character (text, lineNo, column)
{
  if (lineNo > 1)
  {
    pos = text.indexOf ('\n', 0);
    lineIdx = 1;
    while ((lineIdx < lineNo - 1) && (pos >= 0))
    {
      pos = text.indexOf ('\n', pos + 1);
      lineIdx++;
    }
  }
  else
    pos = 0;
  
  return pos + column;
}

/* Select a span of text; units are 1-based.
 * @param TextArea ctrl Control in which to make the selection.
 * @param String fromChar Starting position.
 * @param String toChar Ending position */
function select_char_range (ctrl, fromChar, toChar)
{
  if (ctrl.createTextRange) 
  { 
    /* Create a TextRange, set the internal pointer to
       a specified position and show the cursor at this
       position
    */ 
    var range = ctrl.createTextRange (); 
    range.move ("character", fromChar - 1);
    range.moveEnd ("character", toChar - fromChar);
    range.select ();
    range.scrollIntoView();
  } 
  else
  { 
    /* Gecko is a little bit shorter on that. Simply
       focus the element and set the selection to a
       specified position
    */ 
    ctrl.focus (); 
    ctrl.setSelectionRange (fromChar - 1, toChar - 1);
  }
}

/* Select a span of text; units are 1-based.
 * @param TextArea ctrl Control in which to make the selection.
 * @param String fromLineNo Starting line
 * @param String fromColumn Starting column
 * @param String fromLineNo Ending line
 * @param String fromColumn Ending column */
function select_line_column_range (ctrl, fromLineNo, fromColumn, toLineNo, toColumn)
{
  fromChar = line_and_column_to_character (ctrl.value, fromLineNo, fromColumn);
  toChar = line_and_column_to_character (ctrl.value, toLineNo, toColumn);
  select_char_range (ctrl, fromChar, toChar);
}

function _submit_form (f, submit_all, submitted_name, preview_name)
{
  if (submit_all)
	  enable_all_controls (f);

  if (f [preview_name])
	  f [preview_name].value = 0;
  if (f [submitted_name])
	  f [submitted_name].value = 1;

  f.submit ();
}

function submit_form (form_name, submit_all, submitted_name, preview_name)
{
  _submit_form (document.getElementById (form_name), submit_all, submitted_name, preview_name);
}

function preview_field (form_name, field_name)
{
  var request = new XMLHttpRequest();
  request.open('POST', '/news/generate_preview.php', true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=ISO-8859-1");

  var form = document.getElementById(form_name);
  var idField = form['id'];
  var dataField = document.getElementById(field_name);

  request.send('id=' + encodeURIComponent(idField.value) + '&inputText=' + encodeURIComponent(dataField.value));

  request.onreadystatechange=function()
  {
    if (request.readyState==4 && request.status==200)
    {
      var data = JSON.parse(request.responseText);

      var previewBlock = document.getElementById("inline_preview_block");
      var previewArea = document.getElementById("inline_preview");
      var messageArea = document.getElementById("inline_preview_message");

      previewBlock.style.display = 'block';
      previewArea.innerHTML=data.text;
      messageArea.innerHTML=data.message;
    }
  }
}

function save_field (form_name, field_name)
{
  var request = new XMLHttpRequest();
  request.open('POST', '/news/save_field.php', true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=ISO-8859-1");

  var form = document.getElementById(form_name);
  var idField = form['id'];
  var dataField = document.getElementById(field_name);

  request.send('id=' + encodeURIComponent(idField.value) + '&inputText=' + encodeURIComponent(dataField.value));

  request.onreadystatechange=function()
  {
    if (request.readyState==4 && request.status==200)
    {
      var data = JSON.parse(request.responseText);
      form['time_modified'].value = data.modified;

      var previewBlock = document.getElementById("inline_preview_block");
      var previewArea = document.getElementById("inline_preview");
      var messageArea = document.getElementById("inline_preview_message");

      previewBlock.style.display = 'block';
      previewArea.innerHTML=data.text;
      messageArea.innerHTML=data.message;
    }
  }
}

//document.onkeydown = function(e) {
//  if (!e) e = window.event;
//
//  if (e.shiftKey && e.ctrlKey)
//  {
//    var keynum = e.keyCode ? e.keyCode : e.which; // IE vs others
//
//    switch (keynum)
//    {
//      case 'p':
//
//        return false; //"return false" will avoid further events
//      case 39: case 40: //key is left or down
//      if (currImage >= maxImages) break; //if last do nothing
//      goToNext(); // function which goes to next image
//      return false; //"return false" will avoid further events
//    }
//  }
//
//  return; //using "return" other attached events will execute
//};
//
function preview_form (form_name, submit_all, submitted_name, preview_name)
{
	submit_form (form_name, submit_all, preview_name, submitted_name);
}

function submit_explorer_form (form_name, action)
{
	var msg = "";
	var form = document.getElementById (form_name);
	
	if (msg == "")
	{
	  form.action = action;
	  form.submit ();			
	}
	else
	  alert (msg);
}

function save_as_visible (form_name, submit_all, submitted_name, preview_name)
{
  f = document.getElementById (form_name);
  f.draft.value = 0;
  f.quick_save.value = 0;
  _submit_form (f, submit_all, submitted_name, preview_name);
}

function save_as_draft (form_name, submit_all, submitted_name, preview_name)
{
  f = document.getElementById (form_name);
  f.draft.value = 1;
  f.quick_save.value = 0;
  _submit_form (f, submit_all, submitted_name, preview_name);
}

function quick_save_and_reload (form_name, submit_all, submitted_name, preview_name)
{
  f = document.getElementById (form_name);
  f.quick_save.value = 1;
  f.draft.value = 1;
  _submit_form (f, submit_all, submitted_name, preview_name);
}

var Picker_width = 200;
var Picker_height = 200;
var Picker_resizable = true;
var Picker_page = 'value_picker.html';
var Picker_close_on_select = true;

/** Global list of all pickers in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a value was selected. */
var picker_fields = [];

/** Create a component to select a value.
 * Uses a popup window to display a chooser of some sort and apply it back to
 * a field in the page. */
function VALUE_FIELD ()
{
  /** Name of the 'picker' page. */
  this.page_name = Picker_page;
  /** How wide should the picker window be? */
  this.width = Picker_width;
  /** How high should the picker window be? */
  this.height = Picker_height;
  /** Is the picker for the field resizable? */
  this.resizable = Picker_resizable;

  this.title = 'Select a value';
}

VALUE_FIELD.prototype.attach = function (ctrl, title)
{
  if (! ctrl || ctrl.value == null)
	this.report_error ("Please specify a valid form control.");

  if (title)
	this.title = title;

  this.control = ctrl;

  this.id = picker_fields.length;
  picker_fields [this.id] = this;

  this.refresh ();
}

/** Show an error message.
 * @param string msg */
VALUE_FIELD.prototype.report_error = function (msg)
{
  report_error ('VALUE_FIELD: ' + msg);
}

/** Construct the path and query string for the picker window.
 * @param string initial_value */
VALUE_FIELD.prototype.picker_location = function (initial_value)
{
  return this.page_name + '?fieldid=' + this.id + '&fieldvalue=' + initial_value;
}

/** Height of the picker window.
 * @return integer */
VALUE_FIELD.prototype.picker_height = function ()
{
  return this.height;
}

/** Width of the picker window.
 * @return integer */
VALUE_FIELD.prototype.picker_width = function ()
{
  return this.width;
}

/** Clean up a value to set for this field.
 * @param mixed value */
VALUE_FIELD.prototype.process_value = function (value)
{
  if (value === undefined)
    return 0;
  else
    return value;
}

/** Show the picker window positioned on the current value.
 * Uses 'initial_value' if given. If that is empty, it uses the value displayed
 * in the target control.
 * @param mixed initial_value */
VALUE_FIELD.prototype.show_picker = function (initial_value)
{
  var window_location = this.picker_location (this.process_value (initial_value));

  if (this.picker && ! this.picker.closed)
	this.picker.location = window_location;
  else
  {
	var h = this.picker_height ();
	var w = this.picker_width ();
	var r = 'yes';
	if (! this.resizable)
	  r = 'no';

	var window_params = 'width=' + w + ',height=' + h
					  + ',status=no,resizable=' + r + ',scrollbars=' + r + ',dependent=yes,alwaysRaised=yes';

	/* IE doesn't like this object property...so hack around it. */

	var window_title = ''; // + this.title;

	this.picker = window.open (window_location, window_title, window_params);
	this.picker.focus ();
  }
}

/** Apply a new value to the form control.
 * Generally called from the PICKER object in the page displayed with 'show_picker'.
 * @param Date|integer|string */
VALUE_FIELD.prototype.set_value = function (v)
{
  this.control.value = v;
}

/** Re-apply current value to control.
 * Use this to make sure the control reflects the current display properties. */
VALUE_FIELD.prototype.refresh = function ()
{
  if (this.control.value)
	this.set_value (this.control.value);
}

/** Return the given date as a string.
 * @param Date dt
 * @return string */
VALUE_FIELD.prototype.as_string = function (dt)
{
  fmt = this.output_format;
  if (this.show_time)
	fmt += ' H:i:s';

  return format_date_time (dt, fmt);
}

/** Global list of all calendars in the page.
 * The picker has an instance in the page and an instance in the popup page. The
 * instance in the popup uses this list to notify the instance in the page that
 * a date was selected. */
var pickers = [];

/** A 'picker' that interacts with a VALUE_FIELD.
 * Provides abstract representation of a browser for a value in a form.
 * @see HTML_CALENDAR */
function PICKER ()
{
  /** Close the picker when a selection is made? */
  this.close_on_select = Picker_close_on_select;
}

/** Registers a calendar with a DATE_TIME_FIELD 'id'.
 * Pass in an id, if the calendar should interact with a field and an optional date/time.
 * @param integer id
 * @param integer initial_date_time Should be the result of calling valueOf() on a Date object. */
PICKER.prototype.register = function (id, initial_value)
{
  this.id = id;

  if (! initial_value)
	initial_value = Query_string.item ('value' + id);

  if (window.opener && window.opener.picker_fields)
	this.picker_field = window.opener.picker_fields [id];
  if (! this.picker_field)
	PICKER.prototype.report_error ("Field [" + id + "] not found.");

  pickers [this.id] = this;
}

/** Close the picker and update the value in the field that launched it.
 * @param integer value */
PICKER.prototype.select_value = function (value)
{
  this.picker_field.set_value (value);
  if (this.close_on_select)
	window.close ();
  else
	this.change_value (value);
}

/** Refocus the calendar on the given date.
 * Do not pass a Javascript Date here, use Date.valueOf() instead.
 * @param integer value */
PICKER.prototype.change_value = function (value)
{
  this.picker_field.show_picker (value);
}

/** Show an error message.
 * @param string msg */
PICKER.prototype.report_error = function (msg)
{
  report_error ('PICKER: ' + msg);
}

/** Output a string to the destination document.
 * @param string s */
PICKER.prototype.echo = function (s)
{
  document.write (s + "\n");
}

/** Return JavaScript to select this value.
 * @param mixed value
 * @param string func_name
 * @return string */
PICKER.prototype.make_javascript_selector = function (value, func_name)
{
  return 'pickers [' + this.id + '].' + func_name + ' (' + dt + ')';
}

/** VALUE_FIELD that uses a WebCore browser. */
function WEBCORE_VALUE_FIELD ()
{
  this.page_name = 'value_picker.php';
}
WEBCORE_VALUE_FIELD.prototype = new VALUE_FIELD;

function OBJECT_VALUE_FIELD ()
{
  this.object_id = 0;
}
OBJECT_VALUE_FIELD.prototype = new WEBCORE_VALUE_FIELD;

OBJECT_VALUE_FIELD.prototype.picker_location = function(initial_value)
{
  return this.page_name + '?fieldid=' + this.id + '&fieldvalue=' + initial_value + '&id=' + this.object_id;
}