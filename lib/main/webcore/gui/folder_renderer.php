<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link FOLDER}s.
 * @package webcore
 * @subpackage renderer
 * @version 3.5.0
 * @since 2.5.0
 */
class FOLDER_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param FOLDER $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $use_table = ! $this->_options->show_as_summary && $obj->icon_url;
    if ($use_table)
    {
      $box = $this->context->make_box_renderer ();
      $box->start_column_set ();
      $box->new_column_of_type ('left-column');
      echo $obj->icon_as_html (Fifty_px);
      $box->new_column ();
    }

    $this->_echo_html_content ($obj);

    if ($use_table)
    {
      $box->finish_column_set ();
    }
  }

  /**
   * Shows content (independent of chosen icon).
   * @param FOLDER $obj
   */
  protected function _echo_html_content ($obj)
  {
    $this->_echo_html_user_information ($obj);
    $this->_echo_html_descriptions ($obj);
  }

  /**
   * @param $obj FOLDER
   */
  protected function _echo_html_descriptions ($obj)
  {
    if ($obj->summary && $this->_options->show_as_summary)
    {
      echo $obj->summary_as_html ();
    }
    if ($obj->description && ! $this->_options->show_as_summary)
    {
      echo $this->_echo_html_description ($obj);
    }
  }
}