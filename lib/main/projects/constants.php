<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.6.0
 * @since 1.4.1
 * @package projects
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

/**
 * Permission set applied to an {@link RELEASE}.
 * Uses the folder permissions instead of defining separate ones.
 */
define ('Privilege_set_release', Privilege_set_folder);
/**
 * Permission set applied to an {@link BRANCH}.
 * Uses the folder permissions instead of defining separate ones.
 */
define ('Privilege_set_branch', Privilege_set_folder);
/**
 * Permission set applied to an {@link COMPONENT}.
 * Uses the folder permissions instead of defining separate ones.
 */
define ('Privilege_set_component', Privilege_set_folder);

/**
 * Indicate that a job status should be treated as open.
 * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::job_statuses()
 */
define ('Job_status_kind_open', 1);
/**
 * Indicate that a job status should be treated as closed.
 * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::job_statuses()
 */
define ('Job_status_kind_closed', 2);

/**
 * Identifies a 'release closed' event.
 * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::job_status_map()
 */
define ('Event_release_shipped', 'ship_release');

/**
 * A history item for a {@link BRANCH}.
  * @access private
  */
define ('History_item_branch', 'branch');
/**
 * A history item for a {@link RELEASE}.
  * @access private
  */
define ('History_item_release', 'release');
/**
 * A history item for a {@link COMPONENT}.
  * @access private
  */
define ('History_item_component', 'component');

/**
 * Allows all users to be assigned jobs.
  * @see PROJECT_OPTIONS::$assignee_group_type*/
define ('Project_user_all', 0);
/**
 * Allows only registered users to be assigned jobs.
  * @see PROJECT_OPTIONS::$assignee_group_type*/
define ('Project_user_registered_only', 1);
/**
 * Allows only users in a specific group to be assigned jobs.
  * @see PROJECT_OPTIONS::$assignee_group_type*/
define ('Project_user_group', 2);

/**
 * {@link RELEASE} is in the planning stage.
 */
define ('Planned', 0x09);
/**
 * {@link RELEASE} has been shipped, but is still modifiable.
 */
define ('Shipped', 0x11);
/**
 * {@link RELEASE} is in final testing phase.
 */
define ('Testing', 0x21);

/**
 * {@link RELEASE} is not locked.
 */
define ('Release_not_locked', Planned | Testing | Shipped);
/**
 * {@link RELEASE} is pending (not shipped).
 */
define ('Release_is_pending', Planned | Testing);

?>