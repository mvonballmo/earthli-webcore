<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link FOLDER}s.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */
class FOLDER_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param FOLDER &$obj
   * @access private
   */
  function _display_as_html (&$obj)
  {
    $use_table = ! $this->_options->show_as_summary && $obj->icon_url;
    if ($use_table)
    {
      $box =& $this->context->make_box_renderer ();
      $box->start_column_set ();
      $box->new_column ('padding-right: .5em');  
      echo $obj->icon_as_html ('50px');
      $box->new_column ();
    }

    $this->_echo_html_content ($obj);
    $this->_echo_subscribe_status ($obj);
    
    if ($use_table)
    {
      $box->finish_column_set ();
    }
  }

  /**
   * Shows content (independent of chosen icon).
   * @param FOLDER &$obj
   */
  function _echo_html_content (&$obj)
  {
    $this->_echo_html_user_information ($obj);
    $this->_echo_html_descriptions ($obj);
  }

  function _echo_html_descriptions (&$obj)
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

  /**
   * Shows the subscription status for this object.
   * @param FOLDER &$obj
   * @access private
   */
  function _echo_subscribe_status (&$obj)
  {
    if (! $obj->is_root ())
    {
      $this->_echo_html_subscribed_toggle ($obj, 'subscribe_to_folder.php?id=' . $obj->id, Subscribe_folder);
    }
  }
}

?>