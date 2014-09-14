<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage command
 * @version 3.6.0
 * @since 2.8.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('webcore/cmd/folder_commands.php');

/**
 * Return the commands for a {@link SECTION}.
 * @package news
 * @subpackage command
 * @version 3.6.0
 * @since 2.8.0
 * @access private
 */
class SECTION_COMMANDS extends FOLDER_COMMANDS
{
  /**
   * @param SECTION $folder Configure commands for this object.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $cmd = $this->command_at ('new');
    $cmd->caption = 'New section';

    $cmd = $this->command_at ('new_entry');
    $cmd->caption = 'New article';
    $cmd->icon = '{app_icons}buttons/new_article';
    
    if ($folder->is_organizational()) 
    {
    	$cmd->link = "select_folder.php?page_name=create_article.php";
    }
    else
    {
      $cmd->link = "create_article.php?id=$folder->id";
    }
  }
}

?>