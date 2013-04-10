<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('projects/db/project_release_query.php');
require_once ('projects/db/project_query_toolkit.php');

/**
 * Retrieves {@link RELEASE}s for a particular {@link BRANCH}.
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */
class BRANCH_RELEASE_QUERY extends PROJECT_RELEASE_QUERY
{
  /**
   * @param BRANCH $branch Branch from which releases are retrieved.
   */
  public function __construct ($branch)
  {
    $folder = $branch->parent_folder ();
    parent::__construct ($folder);
    $this->_branch = $branch;
    release_query_order($this);
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = "rel.branch_id = {$this->_branch->id}";
  }

  /**
   * @var BRANCH
   * @access private
   */
  protected $_branch;
}

?>