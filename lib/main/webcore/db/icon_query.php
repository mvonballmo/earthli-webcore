<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.2.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Return a list of {@link ICON} objects.
 * @package webcore
 * @subpackage db
 * @version 3.2.0
 * @since 2.2.1
 */
class ICON_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'icon';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    $this->set_select ('icon.*');
    $this->set_table ($this->app->table_names->icons . ' icon');
    $this->set_order ('category, title');
  }

  /**
   * @return ICON
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ICON', 'webcore/obj/icon.php');
    return new $class_name ($this->app);
  }
}

?>