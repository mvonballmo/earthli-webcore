<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
include_once ('webcore/config/application_engine.php');

/**
 * Creates a {@link TEST_HARNESS_APPLICATION}.
 * Inherit from this class to customize startup for the application.
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.7.0
 */
class TEST_HARNESS_APPLICATION_ENGINE extends APPLICATION_ENGINE
{
  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('APPLICATION', 'TEST_HARNESS_APPLICATION', 'webcore/tests/sys/test_harness_application.php');
  }  
}

?>