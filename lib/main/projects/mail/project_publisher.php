<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage mail
 * @version 3.5.0
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
require_once ('webcore/mail/publisher.php');

/**
 * Handles publishing for {@link PROJECT}s.
 * This includes rendering notifications for {@link RELEASE}s, {@link JOB}s, {@link CHANGE}s
 * {@link BRANCH}es and {@link COMMENT}s.
 * @package projects
 * @subpackage mail
 * @version 3.5.0
 * @since 1.4.1
 */
class PROJECT_PUBLISHER extends PUBLISHER
{
  /**
   * Return a query for the requested objects.
   * @param string $object_type
   * @return QUERY
   * @access private
   */
  protected function _object_query_for ($object_type)
  {
    switch ($object_type)
    {
    case History_item_branch:
      return $this->login->all_branch_query ();
    case History_item_release:
      return $this->login->all_release_query ();
    case History_item_component:
      return $this->login->all_release_query ();
    default:
      return parent::_object_query_for ($object_type);
    }
  }
}

?>