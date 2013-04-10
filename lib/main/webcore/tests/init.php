<?php

/**
 * Initialization file for the {@link TEST_HARNESS_APPLICATION}.
 * Use {@link ENVIROMENT::make_application()} with {@link
 * Test_harness_application_id} to create an instance.
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.4.0
 * @since 2.6.0
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

/**
 * Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope.
 */
include_once ('webcore/init.php');

/**
 * Unique id for the tests application.
 * Used with {@link ENVIRONMENT::register_application()} and {@link
 * ENVIRONMENT::make_application()}.
 */
define ('Test_harness_application_id', 'com.earthli.tests');

$Page->register_application (Test_harness_application_id, 'TEST_HARNESS_APPLICATION_ENGINE', 'webcore/tests/config/test_harness_application_engine.php');

?>