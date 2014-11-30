<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage command
 * @version 3.6.0
 * @since 2.9.0
 * @access private
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
require_once ('webcore/cmd/entry_commands.php');

/**
 * Return the commands for a {@link PICTURE}.
 * @package albums
 * @subpackage command
 * @version 3.6.0
 * @since 2.9.0
 * @access private
 */
class PICTURE_COMMANDS extends ENTRY_COMMANDS
{
  /**
   * @param PICTURE $entry Configure commands for this picture.
   */
  public function __construct ($entry)
  {
    parent::__construct ($entry);

    $cmd = $this->command_at ('edit');
    $cmd->link = "edit_picture.php?id=$entry->id";
    
    $cmd = $this->command_at ('delete');
    $cmd->link = "delete_picture.php?id=$entry->id";

    $cmd = $this->command_at ('purge');
    $cmd->link = "purge_picture.php?id=$entry->id";

    $cmd = $this->command_at ('clone');
    $cmd->link = "clone_picture.php?id=$entry->id";

    $cmd = $this->command_at ('send');
    $cmd->link = "send_picture.php?id=$entry->id";
  }

  /**
   * Add commands that edit the picture.
   * @param PICTURE $entry
   * @access private
   */
  protected function _add_editors ($entry)
  {
    parent::_add_editors($entry);

    $last_page = urlencode ($this->env->url (Url_part_all));

    $cmd = $this->make_command ();
    $cmd->id = 'make_key_photo';
    $cmd->caption = 'Make Key Photo';
    $cmd->link = "make_key_photo.php?id=$entry->id&last_page=$last_page";
    $cmd->icon = '{icons}buttons/password';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $entry->parent_folder());
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
  }
}