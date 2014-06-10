<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
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
require_once ('webcore/forms/unique_object_form.php');

/**
 * Basic HTML forms handling.
 * Manages a list of {@link FIELD}s to validate and display controls.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 * @abstract
 */
abstract class RENDERABLE_FORM extends UNIQUE_OBJECT_FORM
{
  /**
   * Enable previewing for these forms.
   * @var boolean
   */
  public $preview_enabled = true;

  /**
   * Return a preview for the given object.
   * @param STORABLE $obj
   * @return FORM_PREVIEW_SETTINGS
   * @access private
   */
  protected function _make_preview_settings ($obj)
  {
    return new RENDERABLE_FORM_PREVIEW_SETTINGS ($this);
  }

  /**
   * Title for a previewed object.
   * @param object $obj
   * @return string
   */
  protected function _preview_title ($obj)
  {
    return 'Preview of ' . $obj->title_as_html ();
  }
}

/**
 * Represents an object to preview in a form.
 * @version 3.5.0
 * @since 2.5.0
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class RENDERABLE_FORM_PREVIEW_SETTINGS extends FORM_PREVIEW_SETTINGS
{
  /**
   * Render the preview for this object.
   */
  protected function _display ()
  {
    $renderer = $this->object->handler_for (Handler_html_renderer);
    if (isset ($renderer))
    {
      $this->_configure_options ($renderer->options ());
      $renderer->display ($this->object);
    }
    else
    {
      echo '<div class="error">No HTML renderer defined.</div>';
    }
  }
  
  /**
   * Adjust the display options for the object.
   * @param OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _configure_options ($options)
  {
    $options->show_interactive = false;
  }
}

?>