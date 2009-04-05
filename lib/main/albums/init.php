<?php

/**
 * Initialization file for the {@link ALBUM_APPLICATION}.
 * Use {@link ENVIROMENT::make_application()} with {@link Album_application_id}
 * to create an instance.
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage config
 * @version 3.1.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

/**
 * Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope.
 */
include_once ('webcore/init.php');

/**
 * Unique id for the albums application.
 * Used with {@link ENVIRONMENT::register_application()} and {@link
 * ENVIRONMENT::make_application()}.
 */
define ('Album_application_id', 'com.earthli.albums');

$Page->register_application (Album_application_id, 'ALBUM_APPLICATION_ENGINE', 'albums/config/album_application_engine.php');

?>