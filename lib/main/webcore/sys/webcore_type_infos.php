<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.0
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
require_once ('webcore/sys/system.php');

/**
 * Describes the {@link UNIQUE_OBJECT} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 3.1.0
 * @access private
 */
class UNIQUE_OBJECT_TYPE_INFO extends TYPE_INFO
{
  /**
   * Gets a unique id for the given object.
   *
   * @param WEBCORE_OBJECT $obj
   */
  public function unique_id ($obj)
  {
    return $obj->id;
  }
}

/**
 * Describes the {@link FOLDER} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class FOLDER_TYPE_INFO extends UNIQUE_OBJECT_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'folder';

  /**
   * @var string
   */
  public $singular_title = 'Folder';

  /**
   * @var string
   */
  public $plural_title = 'Folders';

  /**
   * @var string
   */
  public $icon = '{icons}buttons/new_folder';

  /**
   * @var string
   */
  public $edit_page = 'edit_folder.php';
}

/**
 * Describes the {@link ENTRY} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.1
 * @access private
 */
class ENTRY_TYPE_INFO extends UNIQUE_OBJECT_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'entry';

  /**
   * @var string
   */
  public $singular_title = 'Entry';

  /**
   * @var string
   */
  public $plural_title = 'Entries';

  /**
   * @var string
   */
  public $icon = '{icons}buttons/new_object';

  /**
   * @var string
   */
  public $edit_page = 'edit_entry.php';

  /**
   * @var boolean
   */
  public $draftable = false;
}

/**
 * Describes the {@link DRAFTABLE_ENTRY} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.1
 * @access private
 */
class DRAFTABLE_ENTRY_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var boolean
   */
  public $draftable = true;
}

/**
 * Describes the {@link COMMENT} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class COMMENT_TYPE_INFO extends UNIQUE_OBJECT_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'comment';

  /**
   * @var string
   */
  public $singular_title = 'Comment';

  /**
   * @var string
   */
  public $plural_title = 'Comments';

  /**
   * @var string
   */
  public $icon = '{icons}buttons/reply';

  /**
   * @var string
   */
  public $edit_page = 'edit_comment.php';
}

/**
 * Describes the {@link USER} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class USER_TYPE_INFO extends UNIQUE_OBJECT_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'user';

  /**
   * @var string
   */
  public $singular_title = 'User';

  /**
   * @var string
   */
  public $plural_title = 'Users';

  /**
   * @var string
   */
  public $edit_page = 'edit_user.php';
}

/**
 * Describes the {@link GROUP} class.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class GROUP_TYPE_INFO extends UNIQUE_OBJECT_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'group';

  /**
   * @var string
   */
  public $singular_title = 'Group';

  /**
   * @var string
   */
  public $plural_title = 'Groups';

  /**
   * @var string
   */
  public $edit_page = 'edit_group.php';
}

?>