<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/db/query.php');

/**
 * Return user security {@link GROUP}s in an application.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class GROUP_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'grp';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults ()
  {
    $this->set_select ("grp.*");
    $this->set_table ($this->app->table_names->groups . ' grp');
    $this->set_order ('title ASC');
  }

  /**
   * @return GROUP
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('GROUP', 'webcore/obj/group.php');
    return new $class_name ($this->app);
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set = Privilege_set_group;
}

/**
 * Returns users from a security {@link GROUP}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class USER_GROUP_QUERY extends GROUP_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->add_table ($this->app->table_names->users_to_groups . ' utog', 'grp.id = utog.group_id');
  }
}
?>