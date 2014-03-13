<?php

/**
 * Including this file creates and assigns a global albums application.
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @version 3.5.0
 * @since 2.8.0
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
require_once ('albums/init.php');

/**
 * Creates and sets the global albums application.
 * Calls {@link PAGE::make_application()} to create the application. See
 * {@link PAGE::register_application()} for help on customizing the
 * application creation and initialization.
 * @global ALBUM_APPLICATION $App
 * @version 3.5.0
 * @since 2.5.0
 */
$App = $Page->make_application (Album_application_id, true, true);

?>