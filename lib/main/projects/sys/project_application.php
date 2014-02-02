<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage sys
 * @version 3.4.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/sys/application.php');
require_once ('projects/constants.php');
require_once ('projects/sys/project_type_infos.php');

/**
 * @package projects
 * @subpackage sys
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_APPLICATION_PAGE_NAMES extends APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  public $change_home = 'view_change.php';

  /**
   * @var string
   */
  public $job_home = 'view_job.php';

  /**
   * @var string
   */
  public $release_home = 'view_release.php';

  /**
   * @var string
   */
  public $branch_home = 'view_branch.php';

  /**
   * @var string
   */
  public $component_home = 'view_component.php';
}

/**
 * @package projects
 * @subpackage sys
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  public $folders = 'project_folders';

  /**
   * @var string
   */
  public $comments = 'project_comments';

  /**
   * @var string
   */
  public $entries = 'project_entries';

  /**
   * @var string
   */
  public $user_permissions = 'project_user_permissions';

  /**
   * @var string
   */
  public $folder_permissions = 'project_folder_permissions';

  /**
   * @var string
   */
  public $folder_options = 'project_options';

  /**
   * @var string
   */
  public $jobs = 'project_jobs';

  /**
   * @var string
   */
  public $changes = 'project_changes';

  /**
   * @var string
   */
  public $releases = 'project_releases';

  /**
   * @var string
   */
  public $branches = 'project_branches';

  /**
   * @var string
   */
  public $components = 'project_components';

  /**
   * @var string
   */
  public $entries_to_branches = 'project_entries_to_branches';

  /**
   * @var string
   */
  public $changes_to_branches = 'project_changes_to_branches';

  /**
   * @var string
   */
  public $jobs_to_branches = 'project_jobs_to_branches';

  /**
   * @var string
   */
  public $branches_to_releases = 'project_branches_to_releases';

  /**
   * @var string
   */
  public $subscriptions = 'project_subscriptions';

  /**
   * @var string
   */
  public $subscribers = 'project_subscribers';

  /**
   * @var string
   */
  public $history_items = 'project_history_items';

  /**
   * @var string
   */
  public $searches = 'project_searches';

  /**
   * @var string
   */
  public $attachments = 'project_attachments';
}

/**
 * @package projects
 * @subpackage sys
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_APPLICATION_XML_OPTIONS
{
  /**
   * Log XML operations to this file.
   * This is only used when importing changes.
   * @var string
   */
  public $log_file_name = '';

  /**
   * Location of project settings export file.
   * The list of projects and change types can be exported to a file. This is for integrtion with systems
   * that then load that file to display project settings for users.
   * @var string
   */
  public $export_file_name = '';

  /**
   * Location of changes files.
   * Changes can be exported to XML file; projects will load these changes from here.
   * @var string
   */
  public $import_file_name = '';
}

/**
 * A WebCore application that is a defect and change tracker. Also manages
 * {@link BRANCH}es and {@link RELEASE}s and generates {@link CHANGE_LOG}s.
 * @package projects
 * @subpackage sys
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_APPLICATION extends APPLICATION
{
  /**
   * @var string
   */
  public $title = 'earthli Projects';

  /**
   * @var string
   */
  public $short_title = 'Projects';

  /**
   * @var string
   */
  public $icon = '{app_icons}app/projects';

  /**
   * @var string
   */
  public $support_url = 'http://earthli.com/software/webcore/app_projects.php';

  /**
   * Unique ID for this framework.
   * @var string
   */
  public $framework_id = 'com.earthli.projects';

  /**
   * @var integer
   */
  public $version = '3.4.0';

  /**
   * @param PAGE $page Page to which this application is attached.
   */
  public function __construct ($page)
  {
    parent::__construct ($page);

    $this->xml_options = new PROJECT_APPLICATION_XML_OPTIONS ();

    $this->set_path (Folder_name_application, '{' . Folder_name_apps . '}projects');
    $this->set_path (Folder_name_attachments, '{' . Folder_name_data . '}projects/attachments');
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('FOLDER', 'PROJECT', 'projects/obj/project.php');
    $this->register_class ('USER', 'PROJECT_USER', 'projects/obj/project_user.php');
    $this->register_class ('COMMENT', 'PROJECT_COMMENT', 'projects/obj/project_comment.php');
    $this->register_class ('USER_ENTRY_QUERY', 'PROJECT_USER_ENTRY_QUERY', 'projects/db/project_user_entry_query.php');
    $this->register_class ('USER_COMMENT_QUERY', 'PROJECT_USER_COMMENT_QUERY', 'projects/db/project_user_comment_query.php');
    $this->register_class ('USER_FOLDER_QUERY', 'USER_PROJECT_QUERY', 'projects/db/user_project_query.php');
    $this->register_class ('FOLDER_COMMENT_QUERY', 'PROJECT_COMMENT_QUERY', 'projects/db/project_comment_query.php');
    $this->register_class ('FOLDER_ENTRY_QUERY', 'PROJECT_ENTRY_QUERY', 'projects/db/project_entry_query.php');
    $this->register_class ('FOLDER_GRID', 'PROJECT_GRID', 'projects/gui/project_grid.php');
    $this->register_class ('ENTRY_GRID', 'JOB_GRID', 'projects/gui/job_grid.php', 'job');
    $this->register_class ('ENTRY_GRID', 'CHANGE_GRID', 'projects/gui/change_grid.php', 'change');
    $this->register_class ('ENTRY_FORM', 'JOB_FORM', 'projects/forms/job_form.php', 'job');
    $this->register_class ('ENTRY_FORM', 'CHANGE_FORM', 'projects/forms/change_form.php', 'change');
    $this->register_class ('FOLDER_FORM', 'PROJECT_FORM', 'projects/forms/project_form.php');
    $this->register_class ('MULTIPLE_OBJECT_PRINTER_FORM', 'PROJECT_MULTIPLE_OBJECT_PRINTER_FORM', 'projects/forms/project_multiple_object_printer_form.php', 'change');
    $this->register_class ('PRINT_PREVIEW', 'PROJECT_PRINT_PREVIEW', 'projects/gui/project_print_preview.php');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'JOB_SUMMARY_GRID', 'projects/gui/job_grid.php', 'job');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'CHANGE_SUMMARY_GRID', 'projects/gui/change_grid.php', 'change');
    $this->register_class ('ENTRY_LIST', 'JOB_LIST', 'projects/gui/job_list.php', 'job');
    $this->register_class ('ENTRY_PANEL', 'JOB_PANEL', 'projects/gui/project_panel.php', 'job');
    $this->register_class ('ENTRY_PANEL', 'CHANGE_PANEL', 'projects/gui/project_panel.php', 'change');
    $this->register_class ('PUBLISHER', 'PROJECT_PUBLISHER', 'projects/mail/project_publisher.php');
    $this->register_class ('APPLICATION_TABLE_NAMES', 'PROJECT_APPLICATION_TABLE_NAMES');
    $this->register_class ('APPLICATION_PAGE_NAMES', 'PROJECT_APPLICATION_PAGE_NAMES');
    $this->register_class ('CONTEXT_DISPLAY_OPTIONS', 'PROJECT_APPLICATION_DISPLAY_OPTIONS', 'projects/config/project_application_config.php');
    $this->register_class ('INDEX_PANEL_MANAGER', 'PROJECT_INDEX_PANEL_MANAGER', 'projects/gui/project_panel.php');
    $this->register_class ('FOLDER_PANEL_MANAGER', 'PROJECT_FOLDER_PANEL_MANAGER', 'projects/gui/project_panel.php');
    $this->register_class ('USER_PANEL_MANAGER', 'PROJECT_USER_PANEL_MANAGER', 'projects/gui/project_panel.php');

    $this->register_entry_class ('job', 'JOB', 'projects/obj/job.php');
    $this->register_entry_class ('change', 'CHANGE', 'projects/obj/change.php');

    $this->register_search ('job', 'JOB', 'JOB_SEARCH', 'projects/obj/project_search.php');
    $this->register_search ('change', 'CHANGE', 'CHANGE_SEARCH', 'projects/obj/project_search.php');
  }

  /**
   * Name used for version information.
   * @return string
   */
  public function name ()
  {
    return 'earthli Projects';
  }

  /**
   * Return the requested branch.
   * Since many objects may request a branch in a page, retrieve branch objects through
   * this function in order to take advantage of caching.
   * @param integer $id
   * @return BRANCH
   */
  public function branch_at_id ($id)
  {
    if (! isset ($this->_branch_cache))
    {
      include_once ('projects/db/project_user_branch_query.php');
      $this->_branch_cache = new QUERY_BASED_CACHE (new PROJECT_USER_BRANCH_QUERY ($this->login));
    }
    return $this->_branch_cache->object_at_id ($id);
  }

  /**
   * Return the requested release.
   * Since many objects may request a release in a page, retrieve release objects through
   * this function in order to take advantage of caching.
   * @param integer $id
   * @return RELEASE
   */
  public function release_at_id ($id)
  {
    if (! isset ($this->_release_cache))
    {
      include_once ('projects/db/project_user_release_query.php');
      $this->_release_cache = new QUERY_BASED_CACHE (new PROJECT_USER_RELEASE_QUERY ($this->login));
    }

    return $this->_release_cache->object_at_id ($id);
  }

  /**
   * Return the requested component.
   * Since many objects may request a component in a page, retrieve component objects through
   * this function in order to take advantage of caching.
   * @param integer $id
   * @return COMPONENT
   */
  public function component_at_id ($id)
  {
    if (! isset ($this->_component_cache))
    {
      include_once ('projects/db/project_user_component_query.php');
      $this->_component_cache = new QUERY_BASED_CACHE (new PROJECT_USER_COMPONENT_QUERY ($this->login));
    }
    return $this->_component_cache->object_at_id ($id);
  }

  /**
   * The actual file system location of the application source.
   * Copy/paste to descendents to return the correct location.
   * @return string
   * @access private
   */
  protected function _source_path ()
  {
    return __FILE__;
  }
}

?>
