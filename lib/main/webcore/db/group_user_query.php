<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/db/user_query.php');

/**
 * Return {@link USER}s from a security {@link GROUP}.
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.2.1
 */
class GROUP_USER_QUERY extends USER_QUERY
{
  /**
   * @param GROUP $group Retrieve users in this group.
   */
  public function __construct ($group)
  {
    parent::__construct ($group->app);

    $this->_group = $group;

    $this->add_table ("{$this->app->table_names->users_to_groups} utg", 'utg.user_id = usr.id');
  }

  /**
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    $this->_calculated_restrictions [] = 'utg.group_id = ' . $this->_group->id;;
  }

  /**
   * @var GROUP
   * @access private
   */
  protected $_group;
}
?>