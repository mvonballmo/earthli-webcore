<?php

/**
 * @copyright Copyright (c) 2002-2007 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage gui
 * @version 2.9.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2007 Marco Von Ballmoos

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
require_once ('webcore/gui/panel.php');

/**
 * Performs setup for various {@link PANEL_MANAGER}s.
 * @package news
 * @subpackage gui
 * @version 2.9.0
 * @since 2.8.1
 */
class NEWS_PANEL_MANAGER_HELPER extends PANEL_MANAGER_HELPER
{
  /**
   * Apply global options to a panel manager.
   * Does nothing by default.
   * @param PANEL_MANAGER &$manager
   */
  function configure (&$manager)
  {
////    $panel =& $manager->panel_at ('article');
////    $panel->default_time_frame = Time_frame_last_month;
  }
}

?>