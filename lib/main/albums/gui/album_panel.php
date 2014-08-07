<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/gui/panel.php');

/**
 * Apply general settings to Albums panels.
 * @param PANEL_MANAGER $manager Add and configure panels for this manager.
 * @access private
 */
function _configure_album_panels ($manager)
{
  /* Force the time menu to read its value, using 'all' as a default. It will be
   * read by the panel manager again later, but the picture and journal panels
   * need to know if 'all' is selected, so they can set up sorting correctly.
   */
  $manager->time_menu->load_period_from_request (Time_frame_all);

  if ($manager->is_panel ('picture'))
  {
    $panel = $manager->panel_at ('picture');
    $panel->rows = 10;
    $panel->columns = 3;
    $panel->default_time_frame = Time_frame_all;
  }

  if ($manager->is_panel ('journal'))
  {
    $panel = $manager->panel_at ('journal');
    $panel->default_time_frame = Time_frame_all;
    $panel->columns = 2;
  }

  if ($manager->is_panel ('album'))
  {
    $panel = $manager->panel_at ('album');
    $panel->columns = 3;
  }
}

/**
 * Manage a list of {@link PANEL}s for all {@link ALBUM}s.
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */
class ALBUM_INDEX_PANEL_MANAGER extends INDEX_PANEL_MANAGER
{
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();    
    _configure_album_panels ($this);
  }
}

/**
 * Manage a list of {@link PANEL}s associated with {@link ALBUM}s.
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */
class ALBUM_FOLDER_PANEL_MANAGER extends FOLDER_PANEL_MANAGER
{
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();    
    _configure_album_panels ($this);
    
    /* Select albums if there are any, then pictures and journals. */
    $this->move_panel_to ('album', 0, Panel_selection);
    if (! $this->_folder->is_organizational ())
    {
      $this->move_panel_to ('picture', 1, Panel_selection);
      $this->move_panel_to ('journal', 2, Panel_selection);
    }
  }
}

/**
 * Manage a list of {@link PANEL}s associated with {@link USER}s.
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */
class ALBUM_USER_PANEL_MANAGER extends USER_PANEL_MANAGER
{
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();    
    _configure_album_panels ($this);
  }
}