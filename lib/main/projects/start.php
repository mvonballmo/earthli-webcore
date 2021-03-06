<?php

/**
 * Including this file creates and assigns a global projects application.
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @version 3.6.0
 * @since 1.8.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('projects/init.php');

/**
 * Creates and sets the global projects application.
 * Calls {@link PAGE::make_application()} to create the application. See
 * {@link PAGE::register_application()} for help on customizing the
 * application creation and initialization.
 * @global PROJECT_APPLICATION $App
 * @version 3.6.0
 * @since 1.4.1
 */
$App = $Page->make_application (Project_application_id, true, true);

?>