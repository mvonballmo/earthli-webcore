<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.6.0
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
require_once ('webcore/sys/application.php');

/**
 * @package webcore
 * @version 3.0.0
 * @since 2.6.0
 * @subpackage tests
 */
class TEST_HARNESS_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  public $users = 'test_harness_users';

  /**
   * @var string
   */
  public $groups = 'test_harness_groups';

  /**
   * @var string
   */
  public $users_to_groups = 'test_harness_users_to_groups';

  /**
   * @var string
   */
  public $folders = 'test_harness_folders';

  /**
   * @var string
   */
  public $comments = 'test_harness_comments';

  /**
   * @var string
   */
  public $entries = 'test_harness_entries';

  /**
   * @var string
   */
  public $user_permissions = 'test_harness_user_permissions';

  /**
   * @var string
   */
  public $folder_permissions = 'test_harness_folder_permissions';

  /**
   * @var string
   */
  public $subscriptions = 'test_harness_subscriptions';

  /**
   * @var string
   */
  public $subscribers = 'test_harness_subscribers';

  /**
   * @var string
   */
  public $history_items = 'test_harness_history_items';

  /**
   * @var string
   */
  public $searches = 'test_harness_searches';

  /**
   * @var string
   */
  public $attachments = 'test_harness_attachments';
}

/**
 * A WebCore application that lets users enter {@link RECIPE}s.
 * @package webcore
 * @subpackage tests
 * @version 1.6.0
 * @since 1.3.0
 */
class TEST_HARNESS_APPLICATION extends APPLICATION
{
  /**
   * @var string
   */
  public $title = 'earthli Test Harness';

  /**
   * @var string
   */
  public $short_title = 'Test Suite';

  /**
   * @var integer
   */
  public $version = '1.0.0 beta 1';

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('APPLICATION_TABLE_NAMES', 'TEST_HARNESS_APPLICATION_TABLE_NAMES');
  }
}

?>