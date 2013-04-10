<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/sys/files.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class FILE_TEST_TASK extends TEST_TASK
{
  protected function _execute ()
  {
    $this->_test_normalized_ids();
    $this->_test_delimiters();
    $this->_test_file_sizes();
    $this->_test_file_lists();

    $file_options = global_file_options();
    $old_delimiter = $file_options->path_delimiter;
    $file_options->path_delimiter = '/';
    
    $this->_test_make_canonical();
    $this->_test_path_between();
    $this->_test_join_paths();
    
    $file_options->path_delimiter = $old_delimiter;
  }
  
  private function _test_file_lists()
  {
    $file_options = global_file_options();
    $delimiter = $file_options->path_delimiter;
    
    $input_path = $this->env->source_path ();

    $input_path->append ('../../../templates/wizards/new_application');

    $files = file_list_for ($input_path->as_text (), '', true);
    $this->_check_equal (140, sizeof ($files));
    $this->_check_equal ('code' . $delimiter . 'cmd' . $delimiter . '[[_entry_name_lc_]]_commands.php', $files [0]);
    $files = file_list_for ($input_path->as_text (), '', false);
    $this->_check_equal (1, sizeof ($files));
    $this->_check_equal ('config.ini', $files [0]);

    $files = file_list_for ($input_path->as_text (), 'my/new/files', true);
    $this->_check_equal (140, sizeof ($files));
    $this->_check_equal ('my' . $delimiter . 'new' . $delimiter . 'files' . $delimiter . 'code' . $delimiter . 'cmd' . $delimiter . '[[_entry_name_lc_]]_commands.php', $files [0]);
    $files = file_list_for ($input_path->as_text (), 'my/new/files', false);
    $this->_check_equal (1, sizeof ($files));
    $this->_check_equal ('my' . $delimiter . 'new' . $delimiter . 'files' . $delimiter . 'config.ini', $files [0]);
  }
  
  private function _test_normalized_ids()
  {
    $this->_check_equal ('untergeordnet_asthetisch', normalize_file_id ('Untergeordnet �sthetisch'));
    $this->_check_equal ('untergeordnet_asthetisch', normalize_file_id ('Untergeordnet    �sthetisch'));
    $this->_check_equal ('aaaaeeeeiiiioooouuuuaaaaeeeeiiiioooouuuunncc', normalize_file_id ('��������������������������������������������'));
    
    $opts = global_file_options ();
    $opts->normalized_ids_are_lower_case = false;
    
    $this->_check_equal ('Untergeordnet_Asthetisch', normalize_file_id ('Untergeordnet �sthetisch'));
    $this->_check_equal ('Untergeordnet_Asthetisch', normalize_file_id ('Untergeordnet    �sthetisch'));
    $this->_check_equal ('aaaaeeeeiiiioooouuuuAAAAEEEEIIIIOOOOUUUUnNcC', normalize_file_id ('��������������������������������������������'));
  }
  
  private function _test_file_sizes()
  {
    $this->_check_equal (4096, text_to_file_size ('4KB'));
    $this->_check_equal (4096, text_to_file_size ('4K'));
    $this->_check_equal (4096 * 1024, text_to_file_size ('4MB'));
    $this->_check_equal (4096 * 1024, text_to_file_size ('4M'));
    $this->_check_equal (4096 * 1024 * 1024, text_to_file_size ('4GB'));
    $this->_check_equal (4096 * 1024 * 1024, text_to_file_size ('4G'));
    $this->_check_equal (4096 * 1024 * 1024 * 1024, text_to_file_size ('4TB'));
    $this->_check_equal (4096 * 1024 * 1024 * 1024, text_to_file_size ('4T'));
  }
  
  private function _test_delimiters()
  {
    $file_options = global_file_options();
    $delimiter = $file_options->path_delimiter;
    
    $this->_check_equal (false, begins_with_delimiter ('path/to/the/folder/'));
    $this->_check_equal (true, begins_with_delimiter ($delimiter . 'path/to/the/folder/'));
    $this->_check_equal (true, ends_with_delimiter ('path/to/the/folder' . $file_options->path_delimiter));
    $this->_check_equal (false, ends_with_delimiter ('path/to/the/folder'));

    $this->_check_equal (true, begins_with_delimiter (ensure_begins_with_delimiter ('path/to/the/folder/')));
    $this->_check_equal (true, begins_with_delimiter (ensure_begins_with_delimiter ('/path/to/the/folder/')));
    $this->_check_equal (true, ends_with_delimiter (ensure_ends_with_delimiter ('path/to/the/folder/')));
    $this->_check_equal (true, ends_with_delimiter (ensure_ends_with_delimiter ('path/to/the/folder')));
  }
  
  private function _test_make_canonical()
  {
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/b/c/'));
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/./b/c/'));
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/././b/c/'));
    
    $this->_check_equal ('a/b/c/', make_canonical ('./a/b/c/'));
    $this->_check_equal ('a/b/c/', make_canonical ('./a/./b/c/'));
    $this->_check_equal ('a/b/c/', make_canonical ('./a/././b/c/'));

    $this->_check_equal ('a/b/c/', make_canonical ('././a/b/c/'));
    $this->_check_equal ('a/b/c/', make_canonical ('././a/./b/c/'));
    $this->_check_equal ('a/b/c/', make_canonical ('././a/././b/c/'));
    
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/b/c/.'));
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/./b/c/.'));
    $this->_check_equal ('/a/b/c/', make_canonical ('/a/././b/c/.'));
    
    $this->_check_equal ('a/b/c/', make_canonical ('a/b/c/.'));
    $this->_check_equal ('a/b/c/', make_canonical ('a/./b/c/.'));
    $this->_check_equal ('a/b/c/', make_canonical ('a/././b/c/.'));
    
    $this->_check_equal ('a/b/c/', make_canonical ('a/b/c/./.'));
    $this->_check_equal ('a/b/c/', make_canonical ('a/./b/c/./.'));
    $this->_check_equal ('a/b/c/', make_canonical ('a/././b/c/./.'));
  }
  
  private function _test_path_between()
  {
    $this->_check_equal ('./', path_between ('/a/b/c/', '/a/b/c/'));
    $this->_check_equal ('.', path_between ('/a/b/c/', '/a/b/c'));
    $this->_check_equal ('./', path_between ('/a/b/c', '/a/b/c/'));

    $this->_check_equal ('../', path_between ('/a/b/c/', '/a/b/'));
    $this->_check_equal ('..', path_between ('/a/b/c/', '/a/b'));
    $this->_check_equal ('../', path_between ('/a/b/c', '/a/b/'));

    $this->_check_equal ('../../', path_between ('/a/b/c/', '/a/'));
    $this->_check_equal ('../..', path_between ('/a/b/c/', '/a'));
    $this->_check_equal ('../../', path_between ('/a/b/c', '/a/'));

    $this->_check_equal ('c/', path_between ('/a/b/', '/a/b/c/'));
    $this->_check_equal ('c', path_between ('/a/b/', '/a/b/c'));
    $this->_check_equal ('c/', path_between ('/a/b', '/a/b/c/'));

    $this->_check_equal ('b/c/', path_between ('/a/', '/a/b/c/'));
    $this->_check_equal ('b/c', path_between ('/a/', '/a/b/c'));
    $this->_check_equal ('b/c/', path_between ('/a', '/a/b/c/'));

    $this->_check_equal ('../../b/c/', path_between ('/a/d/c/', '/a/b/c/'));
    $this->_check_equal ('../../b/c', path_between ('/a/d/c/', '/a/b/c'));
    $this->_check_equal ('../../b/c/', path_between ('/a/d/c', '/a/b/c/'));

    $this->_check_equal ('../b/c/', path_between ('/a/d/', '/a/b/c/'));
    $this->_check_equal ('../b/c', path_between ('/a/d/', '/a/b/c'));
    $this->_check_equal ('../b/c/', path_between ('/a/d', '/a/b/c/'));
    
    $this->_check_equal ('../c/d/', path_between ('/a/b/e/', '/a/b/c/d/'));
    $this->_check_equal ('../c/d', path_between ('/a/b/e/', '/a/b/c/d'));
    $this->_check_equal ('../c/d/', path_between ('/a/b/e', '/a/b/c/d/'));

    $this->_check_equal ('/e/b/c/', path_between ('/a/d/c/', '/e/b/c/'));
    $this->_check_equal ('/e/b/c', path_between ('/a/d/c/', '/e/b/c'));
    $this->_check_equal ('/e/b/c/', path_between ('/a/d/c', '/e/b/c/'));

    $this->_check_equal ('./', path_between ('/a/b/c/', '/a/b/c/'));
    $this->_check_equal ('.', path_between ('/a/b/c/', '/a/./b/c'));
    $this->_check_equal ('./', path_between ('/a/b/c/', '/a/././b/c/'));
    
    $this->_check_equal ('a/b/c/', path_between ('/a/b/c/', 'a/b/c/'));
    $this->_check_equal ('a/b/c', path_between ('/a/b/c/', 'a/./b/c'));
    $this->_check_equal ('a/b/c/', path_between ('/a/b/c/', 'a/././b/c/'));
  }
  
private  function _test_join_paths()
  {
    $this->_check_equal ('/a/b/c/d/', join_paths ('/a/', 'b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a/', 'b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a/', 'b/././c/d'));

    $this->_check_equal ('/a/b/c/d/', join_paths ('/a', 'b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a', 'b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a', 'b/././c/d'));
    
    $this->_check_equal ('/a/b/c/d/', join_paths ('/a', '/b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a', '/b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a', '/b/././c/d'));
    
    $this->_check_equal ('/a/b/c/d/', join_paths ('/a/', '/b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a/', '/b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a/', '/b/././c/d'));
    
    $this->_check_equal ('/a/b/c/d/', join_paths ('/a/b/c/', '../../b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a/b/c/', '../../b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a/b/c/', '../../b/././c/d'));

    $this->_check_equal ('/a/b/c/d/', join_paths ('/a/b/c', '../../b/././c/d/'));
    $this->_check_equal ('a/b/c/d/', join_paths ('a/b/c', '../../b/././c/d/'));
    $this->_check_equal ('/a/b/c/d', join_paths ('/a/b/c', '../../b/././c/d'));
  }
}

?>