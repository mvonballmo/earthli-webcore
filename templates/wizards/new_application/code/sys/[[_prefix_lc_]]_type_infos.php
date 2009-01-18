<?php

/**
 * @copyright Copyright (c) 2006 [[_author_name_]]
 * @author [[_author_name_]] <[[_author_email_]]>
 * @filesource
 * @package [[_app_name_]]
 * @subpackage pages
 * @version 1.0.0
 * @since 1.0.0
 */

/****************************************************************************

Copyright (c) 2006 [[_author_name_]]

This file is part of [[_app_title_]].

[[_app_title_]] is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

[[_app_title_]] is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with [[_app_title_]]; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the [[_app_title_]], visit:

[[_app_url_]]

****************************************************************************/

/** */
require_once ('webcore/sys/webcore_type_infos.php');

/** Describes the {@link [[_entry_name_uc_]]} class.
 * @package [[_app_name_]]
 * @subpackage sys
 * @version 1.0.0
 * @since 1.0.0
 * @access private */
class [[_entry_name_uc_]]_TYPE_INFO extends TYPE_INFO
{
  /** @var string */
  var $id = '[[_entry_name_lc_]]';
  /** @var string */
  var $singular_title = '[[_entry_name_mc_]]';
  /** @var string */
  var $plural_title = '[[_entry_name_mc_]]';
  /** @var string */
  var $icon = '{app_icons}buttons/new_[[_entry_name_lc_]]';
  /** @var string */
  var $edit_page = 'edit_[[_entry_name_lc_]].php';
}

/** Describes the {@link [[_folder_name_uc_]]} class.
 * @package [[_app_name_]]
 * @subpackage sys
 * @version 1.0.0
 * @since 1.0.0
 * @access private */
class [[_folder_name_uc_]]_TYPE_INFO extends FOLDER_TYPE_INFO
{
  /** @var string */
  var $singular_title = '[[_folder_name_mc_]]';
  /** @var string */
  var $plural_title = '[[_folder_name_mc_]]';
  /** @var string */
  var $edit_page = 'edit_[[_folder_name_lc_]].php';
}

?>
