
<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/tests/baseline_data_test_task.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class MOVING_TEST_TASK extends BASELINE_DATA_TEST_TASK
{
  function _execute ()
  {
    $this->_log_in_as_tester ();
    $this->_test_moving_folders ();
  }

  /**
   * Make sure permissions are properly maintained when moving folders.
   */
  function _test_moving_folders ()
  {
    $root_folder = $this->_clear_and_return_root_folder ();
    /* Make a folder and give it some permissions. */

    $target = $root_folder->new_folder ();
    $target->store ();
    $target_sec = $target->security_definition ();
    $target_sec->set_inherited (FALSE);

    $p = $target_sec->new_permissions (Privilege_kind_user);
    $p->ref_id = 1;
    $p->set_all ();
    $p->store ();

    /* Make another folder with inherited permissions. */
    $to_move = $root_folder->new_folder ();
    $to_move->store ();

    $to_move_sec = $to_move->security_definition ();
    $to_move_perm_query = $to_move_sec->permissions_query ();
    $target_perm_query = $target_sec->permissions_query ();

    $this->_check ($to_move_sec->inherited (), 'Security should be inherited before the move.');
    $this->_check_equal( $to_move->permissions_id, $root_folder->permissions_id );
    $this->_check_equal( 2, $to_move_perm_query->size () );
    $this->_check_equal( 3, $target_perm_query->size () );

    $options = $to_move->make_move_options ();
    $options->maintain_permissions = TRUE;

    $this->_log ("Moved folder from [$to_move->parent_id] to [$target->id].", Msg_type_info);
    $to_move->move_to ($target, $options);

    $this->_check (! $to_move_sec->inherited (), 'Security should not be inherited after the move.');
    $to_move_perm_query->clear_results ();
    $target_perm_query->clear_results ();
    $this->_check_equal( 2, $to_move_perm_query->size () );
    $this->_check_equal( 3, $target_perm_query->size () );
    $this->_check_equal( $to_move->permissions_id, $to_move->id );

    $to_move_sec->set_inherited (TRUE);
    $this->_check ($to_move_sec->inherited (), 'Security should be inherited.');

    $to_move_perm_query->clear_results ();
    $target_perm_query->clear_results ();
    $this->_check_equal( 3, $to_move_perm_query->size () );
    $this->_check_equal( 3, $target_perm_query->size () );
    $this->_check_equal( $target->id, $target->permissions_id );
    $this->_check_equal( $target->permissions_id, $to_move->permissions_id );

    $options->maintain_permissions = FALSE;
    $this->_log ("Moved folder from [$to_move->parent_id] to [$root_folder->id].", Msg_type_info);
    $to_move->move_to ($root_folder, $options);
    $this->_check ($to_move_sec->inherited (), 'Security should stay inherited after the move.');

    $to_move_perm_query->clear_results ();
    $target_perm_query->clear_results ();
    $this->_check_equal( 2, $to_move_perm_query->size () );
    $this->_check_equal( 3, $target_perm_query->size () );
    $this->_check_equal( $to_move->permissions_id, $root_folder->id );
  }
}

?>