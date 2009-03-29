<?php

/**
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
require_once ('webcore/tests/test_task.php');

define ('Baseline_test_owner1_id', 4000);
define ('Baseline_test_owner2_id', 8000);
define ('Baseline_test_owner3_id', 12000);

/**
 * Tests all possible security combinations.

    Creates the following test hierarchy.

    Root                      - Owner1 and Owner2 nothing
      - entry (vis, owner1)
      - entry (vis, owner2)
      - entry (invis, owner1)
      - entry (invis, owner2)
      - entry (draft, owner1)
      - entry (draft, owner2)
      Folder (Vis1)            - Owner1 and Owner2 vis
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
      Folder (Invis1)          - Owner1 and Owner2 vis
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
      Folder (Vis2)           - Owner1 vis/invis, Owner2 vis
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
      Folder (Invis2)         - Owner1 vis/invis, Owner2 vis
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
      Folder (Vis3)  - Owner1 and Owner2 nothing
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
      Folder (Invis3)  - Owner1 and Owner2 nothing
        - entry (vis, owner1)
        - entry (vis, owner2)
        - entry (invis, owner1)
        - entry (invis, owner2)
        - entry (draft, owner1)
        - entry (draft, owner2)
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class BASELINE_DATA_TEST_TASK extends TEST_TASK
{
  /**
   * @param FOLDER $fldr
   * @param integer $state
   * @param string $title
   * @param string $allow_id
   * @access private
   */
  function _add_folder ($fldr, $state, $title, $allow_id = 'all')
  {
    $this->_log ("Adding folder [$title]...");
    $Result = $fldr->new_folder ();
    $Result->title = $title;
    $Result->state = $state;
    $Result->store ();

    $sec = $Result->security_definition ();
    $sec->set_inherited (FALSE);

    if ($allow_id)
    {
      if ($allow_id == 'all')
      {
        $p = $sec->new_permissions (Privilege_kind_user);
        $p->ref_id = Baseline_test_owner1_id;
        $p->set (Privilege_set_folder, Privilege_view, TRUE);
        $p->set (Privilege_set_entry, Privilege_view, TRUE);
        $p->set (Privilege_set_comment, Privilege_view, TRUE);
        $p->set (Privilege_set_folder, Privilege_view_hidden, TRUE);
        $p->set (Privilege_set_entry, Privilege_view_hidden, TRUE);
        $p->set (Privilege_set_comment, Privilege_view_hidden, TRUE);
        $p->store ();

        $p = $sec->new_permissions (Privilege_kind_user);
        $p->ref_id = Baseline_test_owner2_id;
        $p->set (Privilege_set_folder, Privilege_view, TRUE);
        $p->set (Privilege_set_entry, Privilege_view, TRUE);
        $p->set (Privilege_set_comment, Privilege_view, TRUE);
        $p->store ();
      }
      else
      {
        $p = $sec->new_permissions (Privilege_kind_user);
        $p->ref_id = Baseline_test_owner1_id;
        $p->set (Privilege_set_folder, Privilege_view, TRUE);
        $p->set (Privilege_set_entry, Privilege_view, TRUE);
        $p->set (Privilege_set_comment, Privilege_view, TRUE);
        $p->store ();

        $p = $sec->new_permissions (Privilege_kind_user);
        $p->ref_id = Baseline_test_owner2_id;
        $p->set (Privilege_set_folder, Privilege_view, TRUE);
        $p->set (Privilege_set_entry, Privilege_view, TRUE);
        $p->set (Privilege_set_comment, Privilege_view, TRUE);
        $p->store ();
      }
    }

    return $Result;
  }

  /**
   * @param ENTRY $entry
   * @param integer $state
   * @param string $title
   * @param integer $owner_id
   * @access private
   */
  function _add_comment ($entry, $state, $title, $owner_id)
  {
    $this->_log ("Adding comment [$title]...");
    $comment = $entry->new_comment (0);
    $comment->title = $title;
    $comment->state = $state;
    $comment->store ();
    $comment->owner_id = $owner_id;
    $comment->store ();
  }

  /**
   * @param FOLDER $fldr
   * @param integer $state
   * @param string $title
   * @param integer $owner_id
   * @access private
   */
  function _add_entry ($fldr, $state, $title, $owner_id)
  {
    $this->_log ("Adding entry [$title]...");
    $entry = $fldr->new_object ();
    $entry->title = $title;
    $entry->state = $state;
    $entry->store ();
    $entry->owner_id = $owner_id;
    $entry->store ();

    $this->_add_comment ($entry, Visible, 'Vis1', Baseline_test_owner1_id);
    $this->_add_comment ($entry, Visible, 'Vis2', Baseline_test_owner2_id);
    $this->_add_comment ($entry, Hidden, 'Invis1', Baseline_test_owner1_id);
    $this->_add_comment ($entry, Hidden, 'Invis2', Baseline_test_owner2_id);
  }

  /**
   * @return USER
   * @access private
   */
  function _new_transient_user ()
  {
    $Result = $this->app->new_user ();
    $Result->_permissions = new USER_PERMISSIONS ($this->app);
    return $Result;
  }

  /**
   * @param FOLDER $fldr
   * @access private
   */
  function _add_entries_to_folder ($fldr)
  {
    $this->_log ("Adding entries to [$fldr->title]...");
    $this->_add_entry ($fldr, Visible, 'Vis1', Baseline_test_owner1_id);
    $this->_add_entry ($fldr, Visible, 'Vis2', Baseline_test_owner2_id);
    $this->_add_entry ($fldr, Hidden, 'Invis1', Baseline_test_owner1_id);
    $this->_add_entry ($fldr, Hidden, 'Invis2', Baseline_test_owner2_id);
    $this->_add_entry ($fldr, Draft, 'Draft1', Baseline_test_owner1_id);
    $this->_add_entry ($fldr, Draft, 'Draft2', Baseline_test_owner2_id);
  }

  /**
   * @access private
   */
  function _clear_and_return_root_folder ()
  {
    $folder_query = $this->app->login->folder_query ();
    $root_folder = $folder_query->object_at_id ($this->app->root_folder_id);

    if (isset ($root_folder))
    {
      $this->_log ('Resetting data...', Msg_type_info);
      $root_folder->purge ();
    }
    else
    {
      $root_folder = $this->app->new_folder ();
    }

    // ensure that we get the root folder id again
    $this->_query ("TRUNCATE TABLE `test_harness_folders`");

    $root_folder->title = 'Root';
    $root_folder->store ();

    return $root_folder;
  }

  /**
   * @access private
   */
  function _set_up_data ()
  {
    $root_folder = $this->_clear_and_return_root_folder ();

    $this->_log ('Populating with baseline data...', Msg_type_info);

    $this->_add_entries_to_folder ($root_folder);

    $vis_fldr = $this->_add_folder ($root_folder, Visible, 'Vis1', Baseline_test_owner1_id);
    $this->_add_entries_to_folder ($vis_fldr);
    $invis_fldr = $this->_add_folder ($root_folder, Hidden, 'Invis1', Baseline_test_owner1_id);
    $this->_add_entries_to_folder ($invis_fldr);

    $vis_fldr = $this->_add_folder ($root_folder, Visible, 'Vis2');
    $this->_add_entries_to_folder ($vis_fldr);
    $invis_fldr = $this->_add_folder ($root_folder, Hidden, 'Invis2');
    $this->_add_entries_to_folder ($invis_fldr);

    $vis_fldr = $this->_add_folder ($root_folder, Visible, 'Vis3', 0);
    $this->_add_entries_to_folder ($vis_fldr);
    $invis_fldr = $this->_add_folder ($root_folder, Hidden, 'Invis3', 0);
    $this->_add_entries_to_folder ($invis_fldr);
  }
}

?>