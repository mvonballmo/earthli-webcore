<?php

/**
 * Including this file creates and assigns a global tests application.
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
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

/** */
require_once ('webcore/tests/init.php');

/**
 * Creates and sets the global tests application.
 * Calls {@link ENVIRONMENT::make_application()} to create the application. See
 * {@link ENVIRONMENT::register_application()} for help on customizing the
 * application creation and initialization.
 * @global TEST_HARNESS_APPLICATION $App
 * @version 3.3.0
 * @since 2.6.0
 */
$App = $Page->make_application (Test_harness_application_id, true, true);

?>