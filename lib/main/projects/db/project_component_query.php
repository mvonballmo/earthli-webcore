<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.7.0
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
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Retrieves {@link BRANCH}es for a particular {@link PROJECT}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.7.0
 */
class PROJECT_COMPONENT_QUERY extends OBJECT_IN_SINGLE_FOLDER_QUERY 
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'comp';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('comp.*');
    $this->set_table ($this->app->table_names->components . ' comp');
    $this->set_order ('title ASC');
  }

  /**
   * @return COMPONENT
   * @access private
   */
  protected function _make_object() 
  {
    $class_name = $this->app->final_class_name ('COMPONENT', 'projects/obj/component.php');
    return new $class_name ($this->app);
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions() 
  {
    parent :: _prepare_restrictions();
    $this->_calculated_restrictions[] = 'comp.folder_id = '.$this->_folder->id;
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
}

?>