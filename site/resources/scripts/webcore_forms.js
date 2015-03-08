/****************************************************************************

 Copyright (c) 2002-2015 Marco Von Ballmoos

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

function toggle_selected(control)
{
  if (control.checked)
  {
    select_none(control);
  }
  else
  {
    select_all(control);
  }
}

function select_all(control)
{
  select_items(control, true);
}

function select_none(control)
{
  select_items(control, false);
}

function select_items(control, b)
{
  if (control)
  {
    control.checked = b;
    for (var i = 0; i < control.length; i++)
    {
      control [i].checked = b
    }
  }
}

function select_item(control, value, b)
{
  if (control)
  {
    if (control.length)
    {
      for (var i = 0; i < control.length; i++)
      {
        if (control [i].value == value)
        {
          control [i].checked = b;
        }
      }
    }
    else
    {
      if (control.value == value)
      {
        control.checked = b;
      }
    }
  }
}

function select_first_available(control)
{
  if (control)
  {
    if (control.length)
    {
      for (var i = 0; i < control.length; i++)
      {
        if (!control [i].disabled)
        {
          control [i].checked = true;
        }
      }
    }
    else
    {
      if (!control.disabled)
      {
        control.checked = true;
      }
    }
  }
}

function enable_items(control, b)
{
  if (control)
  {
    control.disabled = !b;
    for (var i = 0; i < control.length; i++)
    {
      control [i].disabled = !b;
    }
  }
}

function enable_item(control, value, b)
{
  if (control)
  {
    if (control.length)
    {
      for (var i = 0; i < control.length; i++)
      {
        if (control [i].value == value)
          control [i].disabled = !b;
      }
    }
    else
    {
      if (control.value == value)
        control.disabled = !b;
    }
  }
}

function has_selected(control)
{
  var Result = false;

  if (control)
  {
    if (control.length)
    {
      for (var i = 0; i < control.length; i++)
      {
        if ((control [i].checked) && (!control [i].disabled))
        {
          break;
        }
      }

      Result = (i < control.length);
    }
    else
    {
      Result = control.checked && !control.disabled;
    }
  }

  return Result;
}

function is_selected(control, value)
{
  var Result = false;

  if (control)
  {
    if (control.length)
    {
      for (var i = 0; i < control.length; i++)
      {
        if (control [i].value == value)
        {
          return control [i].checked && !control [i].disabled;
        }
      }

      return false;
    }
    else
    {
      Result = (control.value == value) && control.checked && !control.disabled;
    }
  }

  return Result;
}

function ensure_not_selected(control, value)
{
  if (control)
  {
    if (is_selected(control, value))
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
        {
          control.checked = false;
        }
      }
    }
  }
}

function ensure_has_selected(control, value)
{
  if (control)
  {
    if (!has_selected(control))
    {
      /* If there are only selected items that are disabled, then 'has_selected' also returns
       false. Since we will be selecting an item, make sure that all disabled/selected items
       are de-selected and that the selected 'value' is the only selected one. If the selected
       'value' is not selectable, then just select the first available item. */

      select_none(control);
      select_item(control, value, 1);
      if (!has_selected(control))
      {
        select_first_available(control);
      }
    }
  }
}

function first_selected(control)
{
  var Result = false;
  var i = 0;
  while ((i < control.length) && (!Result))
  {
    Result = control [i].checked;
    i++;
  }

  if (Result)
  {
    return control [i - 1].value;
  }

  return 0;
}

function enable_all_controls(f)
{
  for (var idx = 0; idx < f.length; idx++)
  {
    f [idx].disabled = false;
  }
}

function set_all_controls_of_type(form, type, value)
{
  for (var idx = 0; idx < form.elements.length; idx++)
  {
    if (form.elements [idx].type == type)
    {
      if ((type == 'checkbox') || (type == 'radio'))
      {
        select_items(form.elements [idx], value);
      }
      else
      {
        form.elements [idx].value = value;
      }
    }
  }
}

function insert_text(ctrl, text)
{
  // IE support

  if (document.selection)
  {
    ctrl.focus();
    var sel = document.selection.createRange();
    sel.text = text;
  }

  // MOZILLA/NETSCAPE support

  else if (ctrl.selectionStart || ctrl.selectionStart == '0')
  {
    var startPos = ctrl.selectionStart;
    var endPos = ctrl.selectionEnd;
    ctrl.value = ctrl.value.substring(0, startPos)
      + text
      + ctrl.value.substring(endPos, ctrl.value.length);
  } else
  {
    ctrl.value += text;
  }
}

/* Returns a character offset based on line number and column; units are 1-based.
 * @param String text Determine position within this text
 * @param String lineNo Starting line
 * @param String column Starting column */
function line_and_column_to_character(text, lineNo, column)
{
  var pos = 0;
  if (lineNo > 1)
  {
    pos = text.indexOf('\n', 0);
    var lineIdx = 1;
    while ((lineIdx < lineNo - 1) && (pos >= 0))
    {
      pos = text.indexOf('\n', pos + 1);
      lineIdx++;
    }
  }

  return pos + column;
}

/* Select a span of text; units are 1-based.
 * @param TextArea ctrl Control in which to make the selection.
 * @param String fromChar Starting position.
 * @param String toChar Ending position */
function select_char_range(ctrl, fromChar, toChar)
{
  if (ctrl.createTextRange)
  {
    /* Create a TextRange, set the internal pointer to
     a specified position and show the cursor at this
     position
     */
    var range = ctrl.createTextRange();
    range.move("character", fromChar - 1);
    range.moveEnd("character", toChar - fromChar);
    range.select();
    range.scrollIntoView();
  }
  else
  {
    /* Gecko is a little bit shorter on that. Simply
     focus the element and set the selection to a
     specified position
     */
    ctrl.focus();
    ctrl.setSelectionRange(fromChar - 1, toChar - 1);
  }
}

/* Select a span of text; units are 1-based.
 * @param TextArea ctrl Control in which to make the selection.
 * @param String fromLineNo Starting line
 * @param String fromColumn Starting column
 * @param String fromLineNo Ending line
 * @param String fromColumn Ending column */
function select_line_column_range(ctrl, fromLineNo, fromColumn, toLineNo, toColumn)
{
  var fromChar = line_and_column_to_character(ctrl.value, fromLineNo, fromColumn);
  var toChar = line_and_column_to_character(ctrl.value, toLineNo, toColumn);
  select_char_range(ctrl, fromChar, toChar);
}

function _submit_form(f, submit_all, submitted_name, preview_name)
{
  if (submit_all)
    enable_all_controls(f);

  if (f [preview_name])
    f [preview_name].value = 0;
  if (f [submitted_name])
    f [submitted_name].value = 1;

  f.submit();
}

function submit_form(form_name, submit_all, submitted_name, preview_name)
{
  _submit_form(document.getElementById(form_name), submit_all, submitted_name, preview_name);
}

/**
 * Set's the value of the control with the given {@link id}
 * @param event Event
 * @param id string
 * @param value string
 */
function set_control_value(event, id, value)
{
  event.stopPropagation();
  document.getElementById(id).value = value;
  closeMenus(document);
}

// Build the DOM selection query
function $q(selector, ctx)
{
  ctx = ctx || document;

  // Return methods for lazy evaluation of the query
  return {

    // Return array of all matches
    all: function ()
    {
      var list, ary = [];
      list = ctx.querySelectorAll(selector);
      for (var i = 0; i < list.length; i++)
      {
        ary[ i ] = list[ i ];
      }
      return ary;
    },

    // Return first match
    first: function ()
    {
      return ctx.querySelector(selector);
    },

    // Return last match
    last: function ()
    {
      var list = ctx.querySelectorAll(selector);
      return list.length > 0 ? list[ list.length - 1 ] : null;
    }
  };
}

function execute_field(url, form_name)
{
  // Make the page max-width = two times what it is (1800px instead of 900px)
  // Make the submitting form class be "top" instead of "left"

  var page = $q('.page').first();
  var form = document.getElementById(form_name);
  var form_container = $q('.basic-form', form).first();

  form_container.className = 'basic-form top';
  page.style.maxWidth = '1600px';

  var request = new XMLHttpRequest();
  request.open('POST', url, true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=ISO-8859-1");

  var idField = form['id'];
  var title = document.getElementById('title');
  var description = document.getElementById('description');

  request.send('id=' + encodeURIComponent(idField.value) + '&description=' + encodeURIComponent(description.value) + '&title=' + encodeURIComponent(title.value));

  request.onreadystatechange = function ()
  {
    if (request.readyState == 4 && request.status == 200)
    {
      var data = JSON.parse(request.responseText);

      var previewBlock = $q('.inline-preview-block', form).first();
      var previewArea = $q('.inline-preview', form).first();
      var messageArea = $q('.inline-preview-message', form).first();

      var errors = data.errors;
      var modified = data.modified;

      if (errors.length > 0)
      {
        var error_text = '<ul>';

        for (var i = 0; i < errors.length; i++)
        {
          var error = errors[i];

          error_text += "<li>[<a href='#' onclick='select_line_column_range ($q(\"#description\").first(), " + error.line_number + ", " + error.column_start + ", " + error.line_number + ", " + error.column_end + ")'>line " + error.line_number + ', col ' + error.column_start + '</a>]: ' + error.message + '</li>';
        }

        error_text += '</ul>';

        previewArea.innerHTML = error_text;
      }
      else
      {
        previewArea.innerHTML = data.text;
      }

      messageArea.innerHTML = data.message;
      previewBlock.style.display = 'flex';

      var modifiedField = form.time_modified;
      modifiedField.value = modified;
    }
  }
}

function preview_form(form_name, submit_all, submitted_name, preview_name)
{
  submit_form(form_name, submit_all, preview_name, submitted_name);
}

function submit_explorer_form(form_name, action)
{
  var msg = "";
  var form = document.getElementById(form_name);

  if (msg == "")
  {
    form.action = action;
    form.submit();
  }
  else
    alert(msg);
}

function save_as_visible(form_name, submit_all, submitted_name, preview_name)
{
  var f = document.getElementById(form_name);
  f.draft.value = 0;
  f.quick_save.value = 0;
  _submit_form(f, submit_all, submitted_name, preview_name);
}

function save_as_draft(form_name, submit_all, submitted_name, preview_name)
{
  var f = document.getElementById(form_name);
  f.draft.value = 1;
  f.quick_save.value = 0;
  _submit_form(f, submit_all, submitted_name, preview_name);
}

function quick_save_and_reload(form_name, submit_all, submitted_name, preview_name)
{
  var f = document.getElementById(form_name);
  f.quick_save.value = 1;
  f.draft.value = 1;
  _submit_form(f, submit_all, submitted_name, preview_name);
}