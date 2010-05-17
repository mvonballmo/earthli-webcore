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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/util/image.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class NON_INTERNAL_EXIF_IMAGE extends IMAGE
{
  public function __construct ()
  {
    parent::__construct (false);
  }
}

/**
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class EXIF_TEST_TASK extends TEST_TASK
{
  protected function _process_for ($name, $filename)
  {
    $this->_log ("Processing [$filename] for [$name]", Msg_type_info);
    $this_url = new FILE_URL (__FILE__);
    $this_url->append_folder ('data');
    $this_url->append_folder ('images');
    $this_url->replace_name_and_extension ($filename);
    if ($this->_use_internal_exif)
    {
      $image = new IMAGE ();
    }
    else
    {
      $image = new NON_INTERNAL_EXIF_IMAGE ();
    }
    $image->set_file ($this_url->as_text (), true);
    if ($image->properties->time_created->is_valid ())
    {
      $this->_log ('Extracted date/time: ' . $image->properties->time_created->as_iso (), Msg_type_info);
    }
    else
    {
      $this->_log ('Could not extract date/time.', Msg_type_warning);
    }
  }

  protected function _run_tests ()
  {
    $this->_process_for ('Sean', '105_0531.JPG');
    $this->_process_for ('Diana', 'CIMG0284.JPG');
    $this->_process_for ('Gary', 'Amy_wcv.jpg');
    $this->_process_for ('Marco (Canon)', '107_0734.jpg');
    $this->_process_for ('Canon (converted)', 'IMG_0417.jpg');
    $this->_process_for ('Marco (Old HP)', 'IM000261.jpg');
    $this->_process_for ('Gary (CH)', 'P8040067.jpg');
    $this->_process_for ('Gary (converted)', 'P8090103.jpg');
  }

  protected function _execute ()
  {
    $this->_run_tests ();

    /* Turn off PHP exif processing to force the third party fallback. */
  
    $this->_use_internal_exif = false;  
    $this->_run_tests ();
  }

  /**
   * Initialize any loggers needed for the process.
   * @access private
   */
  protected function _set_up_logging ()
  {
    parent::_set_up_logging ();
    $this->_add_log_channel (Msg_channel_image);
  }
  
  protected $_use_internal_exif = true;
}

?>