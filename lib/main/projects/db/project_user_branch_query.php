<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
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
require_once ('webcore/db/user_entry_query.php');

/**
 * Retrieves {@link BRANCH}es visible to a {@link PROJECT_USER}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */
class PROJECT_USER_BRANCH_QUERY extends USER_ENTRY_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'bra';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('bra.*');
    $this->set_table ($this->app->table_names->branches . ' bra');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'bra.folder_id = fldr.id');
    $this->restrict ('not fldr.organizational and (fldr.parent_id > 0)');
    $this->set_order ('fldr.title, bra.title');
  }

  /**
   * @return BRANCH
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('BRANCH', 'projects/obj/branch.php');
    return new $class_name ($this->app);
  }
}

?>