<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage obj
 * @version 3.5.0
 * @since 2.7.0
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
require_once ('webcore/obj/search.php');
require_once ('albums/forms/album_search_fields.php');

/**
 * A filter for {@link PICTURE}s.
 * @package albums
 * @subpackage obj
 * @version 3.5.0
 * @since 2.7.0
 */
class PICTURE_SEARCH extends MULTI_ENTRY_SEARCH
{
  /**
   * @var string
   */
  public $type = 'picture';

  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context, new SEARCH_PICTURE_FIELDS ($context));
  }
}

/**
 * A filter for {@link JOURNAL}s.
 * @package albums
 * @subpackage obj
 * @version 3.5.0
 * @since 2.7.0
 */
class JOURNAL_SEARCH extends MULTI_ENTRY_SEARCH
{
  /**
   * @var string
   */
  public $type = 'journal';

  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context, new SEARCH_JOURNAL_FIELDS ($context));
  }
}

?>