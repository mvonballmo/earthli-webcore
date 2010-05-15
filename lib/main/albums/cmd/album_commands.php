<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage command
 * @version 3.2.0
 * @since 2.9.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/cmd/folder_commands.php');

/**
 * Commands which apply to an {@link ALBUM}.
 * @package albums
 * @subpackage command
 * @version 3.2.0
 * @since 2.9.0
 * @access private
 */
class ALBUM_COMMANDS extends FOLDER_COMMANDS
{
  /**
   * @param ALBUM $folder Configure commands for this object.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $cmd = $this->command_at ('new');
    $cmd->icon = '{app_icons}buttons/new_album';
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $cmd->title = 'New album';
  }

  /**
   * Add commands that provide views on the folder.
   * @param FOLDER $folder Show commands for this folder.
   * @param USER $creator Folder belongs to this user (also available as $folder->creator ()).
   * @access private
   */
  protected function _add_viewers ($folder)
  {
    parent::_add_viewers ($folder);

    $cmd = $this->make_command ();

    $cmd->id = 'print_preview';
    $cmd->title = 'Print preview';

    $cmd->link = "multiple_print.php?id=$folder->id";
    $entry_query = $folder->entry_query ();
    $entry_query->set_type ('journal');
    if ($entry_query->size ())
    {
      $cmd->link .= '&entry_type=journal';
    }
    else
    {
      $cmd->link .= '&entry_type=picture';
    }

    $cmd->icon = '{icons}buttons/print';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'calendar';
    $cmd->title = 'Calendar';
    $cmd->link = "view_calendar.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/calendar';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
  }

  /**
   * Add buttons that create items in the folder.
   * @param FOLDER $folder
   * @param USER $creator Folder belongs to this user (also available as $folder->creator ()).
   * @access private
   */
  protected function _add_creators ($folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'new_picture';
    $cmd->title = 'New picture';
    if ($folder->is_organizational()) 
    {
      $cmd->link = "select_folder.php?page_name=create_picture.php";
    }
    else
    {
      $cmd->link = "create_picture.php?id=$folder->id";
    }    
    $cmd->icon = '{app_icons}buttons/new_picture';
    $cmd->executable = $folder->app->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'new_journal';
    $cmd->title = 'New journal';
    if ($folder->is_organizational()) 
    {
      $cmd->link = "select_folder.php?page_name=create_journal.php";
    }
    else
    {
      $cmd->link = "create_journal.php?id=$folder->id";
    }
    $cmd->link = "create_journal.php?id=$folder->id";
    $cmd->icon = '{app_icons}buttons/new_journal';
    $cmd->executable = $folder->app->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);
    
    $cmd = $this->make_command ();
    $cmd->id = 'upload_pictures';
    $cmd->title = 'Upload pictures';
    if ($folder->is_organizational()) 
    {
      $cmd->link = "select_folder.php?page_name=upload_pictures.php";
    }
    else
    {
      $cmd->link = "upload_pictures.php?id=$folder->id";
    }
    $cmd->icon = '{icons}buttons/upload';
    $cmd->executable = $folder->app->login->is_allowed (Privilege_set_entry, Privilege_create, $folder)
                       && $folder->app->login->is_allowed (Privilege_set_entry, Privilege_upload, $folder)
                       && $folder->uploads_allowed ();
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);
  }
}

?>