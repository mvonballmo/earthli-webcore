<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 3.1.0
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
require_once ('webcore/sys/property.php');

/**
 * Retrieves the values for the latest version of a project from the database.
 * 
 * @package projects
 * @subpackage util
 * @version 3.4.0
 * @since 3.1.0
 */
class PROJECT_VERSION_FORMATTER
{
  /**
   * Release date as text.
   *
   * @var string
   */
  public $release_date;
  
  /**
   * The URL to the change log for the release.
   *
   * @var string
   */
  public $change_log_link;
  
  /**
   * The version number as text.
   *
   * @var string
   */
  public $version;
  
  public function __construct ($projects_app, $project_id)
  {
    $folder_query = $projects_app->login->folder_query ();
    $project = $folder_query->object_at_id (Webcore_project_id);
    $release_query = $project->release_query ();
    $release_query->restrict ('(rel.state = ' . Shipped . ') or (rel.state = ' . Locked . ')'); 
    $releases = $release_query->objects();
    if (! empty($releases))
    {
      $latest_release = $releases[0];
      $f = $latest_release->time_shipped->formatter ();
      $f->type = Date_time_format_short_date;
      $this->version = $latest_release->title_as_plain_text ();
      $this->release_date = $latest_release->time_shipped->format ($f);
      $this->change_log_link = $projects_app->resolve_file ('{root}/projects/view_release_change_log.php?id=' . $latest_release->id);
    }
  }
}

?>