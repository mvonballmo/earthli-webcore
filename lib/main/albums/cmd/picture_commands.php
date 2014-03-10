<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage command
 * @version 3.5.0
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
 * @version 3.5.0
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
}
?>