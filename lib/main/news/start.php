<?php

/**
 * Including this file creates and assigns a global news application.
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @version 3.5.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('news/init.php');

/**
 * Creates and sets the global news application.
 * Calls {@link PAGE::make_application()} to create the application. See
 * {@link PAGE::register_application()} for help on customizing the
 * application creation and initialization.
 * @global NEWS_APPLICATION $App
 * @version 3.5.0
 * @since 2.4.0
 */
$App = $Page->make_application (News_application_id, true, true);

?>