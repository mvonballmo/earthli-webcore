<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class QUERY_ITERATOR_TEST_TASK extends BASELINE_DATA_TEST_TASK
{
  protected function _execute ()
  {
    $this->_log_in_as_tester ();
//    $this->_set_up_data ();
    $this->_test_query_iterator ();
  }

  protected function _test_query_iterator ()
  {
    $entry_query = $this->app->login->all_entry_query ();
    $num_items_in_db = $entry_query->size ();

    $iterator = new QUERY_ITERATOR ($entry_query);
    $iterator->go_to_first ();
    while ($iterator->has_items ())
    {
      $iterator->item ();
      $iterator->go_to_next ();
    }

    $this->_check_equal ($num_items_in_db, $iterator->num_items_iterated ());
  }
}

?>