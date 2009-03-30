<?php

/**
 * @copyright Copyright (c) 2002-2005 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage sys
 * @version 3.0.0
 * @since 2.8.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2005 Marco Von Ballmoos

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
require_once ('webcore/sys/webcore_type_infos.php');

/**
 * Modifies the {@link ARTICLE} class.
 * @package news
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class ARTICLE_TYPE_INFO extends DRAFTABLE_ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'article';

  /**
   * @var string
   */
  public $singular_title = 'Article';

  /**
   * @var string
   */
  public $plural_title = 'Articles';

  /**
   * @var string
   */
  public $icon = '{app_icons}buttons/new_article';

  /**
   * @var string
   */
  public $edit_page = 'edit_article.php';
}

/**
 * Describes the {@link FOLDER} class.
 * @package news
 * @subpackage sys
 * @version 3.0.0
 * @since 2.8.0
 * @access private
 */
class SECTION_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'section';

  /**
   * @var string
   */
  public $singular_title = 'Section';

  /**
   * @var string
   */
  public $plural_title = 'Sections';

  /**
   * @var string
   */
  public $icon = '{icons}buttons/new_folder';

  /**
   * @var string
   */
  public $edit_page = 'edit_section.php';
}

?>