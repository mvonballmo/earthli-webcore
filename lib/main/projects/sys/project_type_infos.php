<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.9.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/sys/webcore_type_infos.php');

/**
 * Describes the {@link PROJECT} class.
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.5.0
 * @access private
 */
class PROJECT_TYPE_INFO extends FOLDER_TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'project';
  /**
   * @var string
   */
  var $singular_title = 'Project';
  /**
   * @var string
   */
  var $plural_title = 'Projects';
  /**
   * @var string
   */
  var $edit_page = 'edit_project.php';
}

/**
 * Describes the {@link JOB} class.
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.5.0
 * @access private
 */
class JOB_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'job';
  /**
   * @var string
   */
  var $singular_title = 'Job';
  /**
   * @var string
   */
  var $plural_title = 'Jobs';
  /**
   * @var string
   */
  var $icon = '{app_icons}buttons/new_job';
  /**
   * @var string
   */
  var $edit_page = 'edit_job.php';
}

/**
 * Describes the {@link CHANGE} class.
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.5.0
 * @access private
 */
class CHANGE_TYPE_INFO extends ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'change';
  /**
   * @var string
   */
  var $singular_title = 'Change';
  /**
   * @var string
   */
  var $plural_title = 'Changes';
  /**
   * @var string
   */
  var $icon = '{app_icons}buttons/new_change';
  /**
   * @var string
   */
  var $edit_page = 'edit_change.php';
}

/**
 * Describes the {@link BRANCH} class.
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.5.0
 * @access private
 */
class BRANCH_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'branch';
  /**
   * @var string
   */
  var $singular_title = 'Branch';
  /**
   * @var string
   */
  var $plural_title = 'Branchs';
  /**
   * @var string
   */
  var $icon = '{app_icons}buttons/new_branch';
  /**
   * @var string
   */
  var $edit_page = 'edit_branch.php';
}

/**
 * Describes the {@link RELEASE} class.
 * @package projects
 * @subpackage sys
 * @version 3.0.0
 * @since 1.5.0
 * @access private
 */
class RELEASE_TYPE_INFO extends TYPE_INFO
{
  /**
   * @var string
   */
  var $id = 'release';
  /**
   * @var string
   */
  var $singular_title = 'Release';
  /**
   * @var string
   */
  var $plural_title = 'Releases';
  /**
   * @var string
   */
  var $icon = '{app_icons}buttons/new_release';
  /**
   * @var string
   */
  var $edit_page = 'edit_release.php';
}

?>