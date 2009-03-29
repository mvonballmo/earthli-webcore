<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');

/**
 * Properties for an item in a list.
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class CONTROLS_RENDERER extends WEBCORE_OBJECT
{
  /**
   * Width of all rendered buttons, if set.
   * Value is in CSS units.
   * @param string
   */
  var $button_width = '';
  
  /**
   * @param CONTEXT &$context
   */
  function CONTROLS_RENDERER (&$context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);
    $browser = $this->env->browser ();
    $this->_supports_css_2 = $browser->supports (Browser_CSS_2);
  }

  /**
   * Start a new button-styled row.
   * @param string $title Title to show for this row.
   */
  function start_button_row ($title = ' ')
  {
    echo "<div class=\"form-button-content\">\n";
  }

  /**
   * Close a previously opened row.
   */
  function finish_row ()
  {
    echo "</div>\n";
  }
  
  /**
   * Draw the list of buttons as HTML.
   * Draws a series of buttons previously renderered with {@link javascript_button_as_html()},
   * {@link button_as_html()} or {@link submit_button_as_html()}.
   * @param array[string] $buttons
   */
  function draw_buttons ($buttons)
  {
    $btn_drawn = FALSE;
    foreach ($buttons as $button)
    {
      echo $button;
      if ($btn_drawn && ! $this->_supports_css_2)
      {
        echo '&nbsp';
      }
      $btn_drawn = TRUE;
    }
  }

  /**
   * Draw the list of buttons in a row.
   * Draws a series of buttons previously renderered with {@link javascript_button_as_html()},
   * {@link button_as_html()} or {@link submit_button_as_html()}.
   * @param array[string] $buttons
   * @param string $title Title to show for this row.
   */
  function draw_buttons_in_row ($buttons, $title = ' ')
  {
    $this->start_button_row ($title);
    $this->draw_buttons ($buttons);
    $this->finish_row ();
  }

  /**
   * Return HTML for a button linked to JavaScript.
   * @param string $title Name on the button.
   * @param string $action JavaScript to execute when clicked.
   * @param string $type Can be 'button', 'submit' or 'cancel'.
   * @return string
   */
  function javascript_button_as_html ($title, $action, $icon = '', $icon_size = '16px', $type = 'button')
  {
    if (isset ($icon) && $icon)
    {
      $title = $this->context->resolve_icon_as_html ($icon, '', $icon_size) . ' ' . $title;
    }
    $Result = '<button class="button-control" type="' . $type . '" onClick="' . $action . '"';
    if ($this->button_width)
    {
      $Result .= ' style="width: ' . $this->button_width . '"';
    }
    return $Result . '>' . $title . "</button>\n";
  }

  /**
   * Return HTML for a button linked to a url.
   * @param string $title Name on the button.
   * @param string $action Link to go to when clicked. HTML characters should not be escaped.
   * @return string
   */
  function button_as_html ($title, $location, $icon = '', $icon_size = '16px')
  {
    return $this->javascript_button_as_html ($title, 'window.location=\'' . htmlspecialchars ($location) . '\'', $icon, $icon_size, 'button');
  }

  /**
   * Return HTML for a submitting button.
   * @param string $title Name on the button.
   * @param string $script Name of the JavaScript function to execute (must conform to 'function(form: form; submit_all_fields: boolean; submit_field_name, preview_field_name: string)').
   * @return string
   */
  function submit_button_as_html ($title, $icon = '', $script = null, $icon_size = '16px')
  {
    if (! isset ($script))
    {
      $script = 'submit_form';
    }
    return $this->javascript_button_as_html ($title, $script . ' (\'' . $this->_form->name . '\', '
                                             . $this->submit_all_fields . ', '
                                             . "'" . $this->_form->_form_based_field_name ('submitted') . "', "
                                             . "'" . $this->_form->_form_based_field_name ('previewing') . "'"
                                             . ')', $icon, $icon_size, 'submit');
  }

  /**
   * Can CSS 2 be used to render controls?
   * @var boolean
   */
  var $_supports_css_2;
}

?>