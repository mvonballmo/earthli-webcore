<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.7.0
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
 * Return a list of {@link FRAMEWORK_INFO}s.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.7.0
 */
class FRAMEWORK_INFO_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'ver';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('ver.*');
    $this->set_table ($this->app->table_names->versions . ' ver');
    $this->set_order ('title');
  }
  
  /**
   * Get the application info for the given application.
   * @param mixed &$obj Can be an {@link APPLICATION} or an {@link
   * ENVIRONMENT}.
   * @return FRAMEWORK_INFO
   */
  function info_for (&$obj)
  {
    $this->clear_restrictions ();
    $this->restrict_by_op ('title', $obj->framework_id);
    return $this->first_object ();
  }

  /**
   * @return FRAMEWORK_INFO
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->context->final_class_name ('FRAMEWORK_INFO', 'webcore/obj/framework_info.php');
    return new $class_name ($this->context);
  }
}

?>