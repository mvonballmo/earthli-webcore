<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
 * @since 2.6.0
 * @access private
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Properties for an item in a list.
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
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
  public $button_width = '';
  
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
  }

  /**
   * Start a new button-styled row.
   * @param string $title Title to show for this row.
   */
  public function start_button_row ($title = ' ')
  {
    echo "<div class=\"form-button-content\">\n";
  }

  /**
   * Close a previously opened row.
   */
  public function finish_row ()
  {
    echo "</div>\n";
  }
  
  /**
   * Draw the list of buttons as HTML.
   * Draws a series of buttons previously rendered with {@link javascript_button_as_html()},
   * {@link button_as_html()} or {@link submit_button_as_html()}.
   * @param string[] $buttons
   */
  public function draw_buttons ($buttons)
  {
    foreach ($buttons as $button)
    {
      echo $button;
    }
  }

  /**
   * Draw the list of buttons in a row.
   * Draws a series of buttons previously rendered with {@link javascript_button_as_html()},
   * {@link button_as_html()} or {@link submit_button_as_html()}.
   * @param string[] $buttons
   * @param string $title Title to show for this row.
   */
  public function draw_buttons_in_row ($buttons, $title = ' ')
  {
    $this->start_button_row ($title);
    $this->draw_buttons ($buttons);
    $this->finish_row ();
  }

  /**
   * Return HTML for a button linked to JavaScript.
   * @param string $title Name on the button.
   * @param string $action JavaScript to execute when clicked.
   * @param string $icon
   * @param string $icon_size
   * @param string $type Can be 'button', 'submit' or 'cancel'.
   * @return string
   */
  public function javascript_button_as_html ($title, $action, $icon = '', $icon_size = Sixteen_px, $type = 'button')
  {
    if (isset ($icon) && $icon)
    {
      $title = $this->context->get_icon_with_text($icon, $icon_size, $title);
    }
    $Result = '<button type="' . $type . '" onClick="' . $action . '"';
    if ($this->button_width)
    {
      $Result .= ' style="width: ' . $this->button_width . '"';
    }
    return $Result . '>' . $title . "</button>";
  }

  /**
   * Return HTML for a button linked to a url.
   * @param string $title Name on the button.
   * @param $location
   * @param string $icon
   * @param string $icon_size
   * @internal param string $action Link to go to when clicked. HTML characters should not be escaped.
   * @return string
   */
  public function button_as_html ($title, $location, $icon = '', $icon_size = Sixteen_px)
  {
    return $this->javascript_button_as_html ($title, 'window.location=\'' . htmlspecialchars ($location) . '\'', $icon, $icon_size, 'button');
  }

  /**
   * Return HTML for a submitting button.
   * @param string $title Name on the button.
   * @param string $icon
   * @param string $script Name of the JavaScript function to execute (must conform to 'function(form: form; submit_all_fields: boolean; submit_field_name, preview_field_name: string)').
   * @param string $icon_size
   * @return string
   */
  public function submit_button_as_html ($title = null, $icon = '', $script = null, $icon_size = Sixteen_px)
  {
    if (! isset ($script))
    {
      $script = 'submit_form';
    }

    return $this->javascript_button_as_html (
      $title, 
      $script . ' (\'' . $this->_form->name . '\', ' . $this->submit_all_fields . ', ' . "'" . 
      $this->_form->form_based_field_name ('submitted') . "', " . "'" . 
      $this->_form->form_based_field_name ('previewing') . "'" . ')', 
      $icon, 
      $icon_size, 
      'submit'
    );
  }
}