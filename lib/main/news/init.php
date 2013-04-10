<?php

/**
 * Initialization file for the {@link NEWS_APPLICATION}.
 * Use {@link ENVIROMENT::make_application()} with {@link News_application_id}
 * to create an instance.
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @version 3.4.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

/**
 * Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope.
 */
include_once ('webcore/init.php');

/**
 * Unique id for the news application.
 * Used with {@link PAGE::register_application()} and {@link
 * PAGE::make_application()}.
 */
define ('News_application_id', 'com.earthli.news');

$Page->register_application (News_application_id, 'NEWS_APPLICATION_ENGINE', 'news/config/news_application_engine.php');

?>