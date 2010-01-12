<?php

/**
 * Initialization file for the {@link PROJECT_APPLICATION}.
 * Use {@link ENVIROMENT::make_application()} with {@link
 * Project_application_id} to create an instance.
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @version 3.2.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

/**
 * Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope.
 */
include_once ('webcore/init.php');

/**
 * Unique id for the projects application.
 * Used with {@link PAGE::register_application()} and {@link
 * PAGE::make_application()}.
 */
define ('Project_application_id', 'com.earthli.projects');

$Page->register_application (Project_application_id, 'PROJECT_APPLICATION_ENGINE', 'projects/config/project_application_engine.php');

?>