<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage sys
 * @version 3.3.0
 * @since 2.9.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/sys/webcore_type_infos.php');

/**
 * Describes the {@link ALBUM} class.
 * @package albums
 * @subpackage sys
 * @version 3.3.0
 * @since 2.6.0
 * @access private
 */
class ALBUM_TYPE_INFO extends FOLDER_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'album';

  /**
   * @var string
   */
  public $singular_title = 'Album';

  /**
   * @var string
   */
  public $plural_title = 'Albums';

  /**
   * @var string
   */
  public $icon = '{app_icons}buttons/new_album';

  /**
   * @var string
   */
  public $edit_page = 'edit_album.php';
}

/**
 * Describes the {@link JOURNAL} class.
 * @package albums
 * @subpackage sys
 * @version 3.3.0
 * @since 2.6.0
 * @access private
 */
class JOURNAL_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'journal';

  /**
   * @var string
   */
  public $singular_title = 'Journal';

  /**
   * @var string
   */
  public $plural_title = 'Journals';

  /**
   * @var string
   */
  public $icon = '{app_icons}buttons/new_journal';

  /**
   * @var string
   */
  public $edit_page = 'edit_journal.php';
}

/**
 * Describes the {@link PICTURE} class.
 * @package albums
 * @subpackage sys
 * @version 3.3.0
 * @since 2.6.0
 * @access private
 */
class PICTURE_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'picture';

  /**
   * @var string
   */
  public $singular_title = 'Picture';

  /**
   * @var string
   */
  public $plural_title = 'Pictures';

  /**
   * @var string
   */
  public $icon = '{app_icons}buttons/new_picture';

  /**
   * @var string
   */
  public $edit_page = 'edit_picture.php';
}

?>