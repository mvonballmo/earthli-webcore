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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/util/pcl_archive.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class ARCHIVE_TEST_TASK extends TEST_TASK
{
  function process_file ($archive, $entry, $error_callback)
  {
    $this->_log ('Found ' . $entry->name . ' (' . file_size_as_text ($entry->size) . ')', Msg_type_info);
    $entry->extract_to ($this->_temp_path, $error_callback);
  }

  function show_error ($archive, $msg, $entry = null)
  {
    echo "<div class=\"error\">$msg</div>";
  }

  function _execute ()
  {
    $this->_temp_path = $this->context->resolve_path_for_alias (Folder_name_system_temp, 'webcore-tests'); 
 
    $this_url = new FILE_URL (__FILE__);
    $this_url->append_folder ('data');
    $this_url->append_folder ('archives');
    $this_url->replace_name_and_extension ('oz_pics.zip');

    $archive = new PCL_ARCHIVE ($this_url->as_text ());
    $archive->for_each (new CALLBACK_METHOD ('process_file', $this), new CALLBACK_METHOD ('show_error', $this));
   
    $zip = new PCL_ZIP_FILE ($this_url->as_text ());
  
    $zip->open ();
    $zip->for_each (new CALLBACK_METHOD ('process_file', $this), new CALLBACK_METHOD ('show_error', $this));
    
    $archive->extract_to ($this->_temp_path, new CALLBACK_METHOD ('show_error', $this));
  }
  
  var $_temp_path;
}

?>