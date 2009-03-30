
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
class SECURITY_TEST_TASK extends BASELINE_DATA_TEST_TASK
{
  protected function _execute ()
  {
    $this->_log_in_as_tester ();
    $this->_users_tested = 0;
    $this->_set_up_data ();
    $this->_test_global_user_permissions ();
    $this->_log ("Tested [$this->_users_tested] user situations.");
  }

  /**
   * Test for all combinations of user rights for entries and folders.
   * Tests all combinations of granted/denied for user permissions. Does
   * not test any content permissions.
   */
  protected function _test_global_user_permissions ()
  {
    $this->_test_global_user_permission ( 42, 168
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        );

    $this->_test_global_user_permission ( 42, 84
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_denied
                                        );

    $this->_test_content_user_permission ( 14, 14, 14, 54, 28, 28
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_denied
                                        , Privilege_controlled_by_content
                                        );

    $this->_test_global_user_permission ( 14, 28
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_denied
                                        , Privilege_always_denied
                                        );

    $this->_test_content_user_permission ( 27, 21, 14, 108, 42, 28
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_global_user_permission ( 24, 96
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_denied
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        );

    $this->_test_global_user_permission ( 8, 16
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_granted
                                        , Privilege_always_denied
                                        , Privilege_always_denied
                                        , Privilege_always_denied
                                        );

    $this->_test_content_user_permission ( 15, 12, 8, 60, 24, 16
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 30, 24, 24, 120, 96, 96
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 10, 8, 8, 20, 16, 16
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 21, 12, 8, 42, 24, 16
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );
    $this->_test_content_user_permission ( 24, 24, 0, 48, 48, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 8, 8, 0, 16, 16, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 24, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 12, 12, 0, 48, 24, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 4, 4, 0, 8, 8, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 9, 6, 0, 36, 12, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 48, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 6, 4, 0, 12, 8, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 15, 6, 0, 60, 12, 0
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );


    $this->_test_content_user_permission ( 24, 24, 0, 96, 96, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 8, 8, 0, 16, 16, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 24, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 12, 12, 0, 48, 48, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 4, 4, 0, 8, 8, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 9, 6, 0, 36, 12, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 48, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 6, 4, 0, 12, 8, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 15, 6, 0, 60, 12, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 24, 24, 0, 96, 96, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 8, 8, 0, 16, 16, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 24, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 24, 24, 0, 96, 48, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 8, 8, 0, 16, 16, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 24, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 12, 12, 0, 48, 48, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );

    $this->_test_content_user_permission ( 4, 4, 0, 8, 8, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 9, 6, 0, 36, 12, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_content_user_permission ( 18, 12, 0, 72, 48, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_granted
                                         , Privilege_always_granted
                                         );


    $this->_test_content_user_permission ( 6, 4, 0, 12, 8, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_always_denied
                                         , Privilege_always_denied
                                         );

    $this->_test_content_user_permission ( 15, 6, 0, 60, 12, 0
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         , Privilege_controlled_by_content
                                         );

    $this->_test_global_user_permission ( 24, 96
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_granted
                                 , Privilege_always_granted
                                 );

    $this->_test_global_user_permission ( 0, 0
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 , Privilege_always_denied
                                 );

  }

  protected function _privilege_to_char ($p)
  {
    switch ($p)
    {
    case Privilege_always_denied:
      return 'd';
    case Privilege_always_granted:
      return 'g';
    default:
      return 'f';
    }
  }

  protected function _test_content_user_permission ($exp_entry1, $exp_entry2, $exp_entry3, $exp_comment1, $exp_comment2, $exp_comment3, $vf, $ve, $vc, $if, $ie, $ic)
  {
    $user = $this->_set_up_user ($vf, $ve, $vc, $if, $ie, $ic);
    $user->id = Baseline_test_owner1_id;
    $this->_test_user ($user, $exp_entry1, $exp_comment1);

    $user = $this->_set_up_user ($vf, $ve, $vc, $if, $ie, $ic);
    $user->id = Baseline_test_owner2_id;
    $this->_test_user ($user, $exp_entry2, $exp_comment2);

    $user = $this->_set_up_user ($vf, $ve, $vc, $if, $ie, $ic);
    $user->id = Baseline_test_owner3_id;
    $this->_test_user ($user, $exp_entry3, $exp_comment3);
  }

  protected function _test_global_user_permission ($exp_entry, $exp_comment, $vf, $ve, $vc, $if, $ie, $ic)
  {
    $this->_test_user ($this->_set_up_user ($vf, $ve, $vc, $if, $ie, $ic), $exp_entry, $exp_comment);
  }

  protected function _set_up_user ($vf, $ve, $vc, $if, $ie, $ic)
  {
    $vft = $this->_privilege_to_char ($vf);
    $vet = $this->_privilege_to_char ($ve);
    $vct = $this->_privilege_to_char ($vc);
    $ift = $this->_privilege_to_char ($if);
    $iet = $this->_privilege_to_char ($ie);
    $ict = $this->_privilege_to_char ($ic);

    $this->_log ("Testing user with rights = [fv$vft, ev$vet, cv$vct, fi$ift, ei$iet, ci$ict]", Msg_type_info);
    $Result = $this->_new_transient_user ();
    $perms = $Result->permissions ();
    $perms->set (Privilege_set_folder, Privilege_view, $vf);
    $perms->set (Privilege_set_entry, Privilege_view, $ve);
    $perms->set (Privilege_set_comment, Privilege_view, $vc);
    $perms->set (Privilege_set_folder, Privilege_view_hidden, $if);
    $perms->set (Privilege_set_entry, Privilege_view_hidden, $ie);
    $perms->set (Privilege_set_comment, Privilege_view_hidden, $ic);

    return $Result;
  }

  protected function _test_user ($user, $exp_entry, $exp_comment)
  {
    $this->app->set_login ($user);
    $entry_query = $this->app->login->all_entry_query ();

        $this->_check_equal ($exp_entry, $entry_query->size ());

    $com_query = $this->app->login->all_comment_query ();

//          $this->_check_equal ($exp_comment, $com_query->size ());

    $this->_users_tested++;
  }
}

?>