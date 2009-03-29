<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.9.0
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
require_once ('webcore/forms/delete_form.php');

/**
 * Handles deletion a {@link BRANCH}.
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.7.0
 */
class DELETE_BRANCH_FORM extends DELETE_OBJECT_IN_FOLDER_FORM
{
  /**
   * Delete the given object.
    * @param BRANCH $obj
    * @access private
    */
  function commit ($obj)
  {
    if ($this->value_for ('purge'))
    {
      $options = new PURGE_OPTIONS ();
      $options->sub_history_item_publication_state = FALSE;
      $obj->purge ($options);
    }
    else
    {
      $obj->delete ();
    }
  }
}

?>