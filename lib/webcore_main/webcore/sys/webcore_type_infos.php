<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.7.0
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
require_once ('webcore/sys/system.php');

/**
 * Describes the {@link FOLDER} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class FOLDER_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'folder';
  /**
   * @var string
   */
  var $singular_title = 'Folder';
  /**
   * @var string
   */
  var $plural_title = 'Folders';
  /**
   * @var string
   */
  var $icon = '{icons}buttons/new_folder';
  /**
   * @var string
   */
  var $edit_page = 'edit_folder.php';
}

/**
 * Describes the {@link ENTRY} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.7.1
 * @access private
 */
class ENTRY_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'entry';
  /**
   * @var string
   */
  var $singular_title = 'Entry';
  /**
   * @var string
   */
  var $plural_title = 'Entries';
  /**
   * @var string
   */
  var $icon = '{icons}buttons/new_object';
  /**
   * @var string
   */
  var $edit_page = 'edit_entry.php';
  /**
   * @var boolean
   */
  var $draftable = FALSE;
}

/**
 * Describes the {@link DRAFTABLE_ENTRY} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.7.1
 * @access private
 */
class DRAFTABLE_ENTRY_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var boolean
   */
  var $draftable = TRUE;
}

/**
 * Describes the {@link COMMENT} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class COMMENT_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'comment';
  /**
   * @var string
   */
  var $singular_title = 'Comment';
  /**
   * @var string
   */
  var $plural_title = 'Comments';
  /**
   * @var string
   */
  var $icon = '{icons}buttons/reply';
  /**
   * @var string
   */
  var $edit_page = 'edit_comment.php';
}

/**
 * Describes the {@link USER} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class USER_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'user';
  /**
   * @var string
   */
  var $singular_title = 'User';
  /**
   * @var string
   */
  var $plural_title = 'Users';
  /**
   * @var string
   */
  var $edit_page = 'edit_user.php';
}

/**
 * Describes the {@link GROUP} class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class GROUP_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'group';
  /**
   * @var string
   */
  var $singular_title = 'Group';
  /**
   * @var string
   */
  var $plural_title = 'Groups';
  /**
   * @var string
   */
  var $edit_page = 'edit_group.php';
}

?>