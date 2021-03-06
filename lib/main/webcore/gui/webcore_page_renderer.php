<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage page
 * @version 3.6.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/gui/themed_page_renderer.php');

/**
 * Provides print-setup and browser-detection.
 * @package webcore
 * @subpackage page
 * @version 3.6.0
 * @since 2.2.1
 */
class WEBCORE_PAGE_RENDERER extends THEMED_PAGE_RENDERER
{
  /**
   * @return boolean
   */
  public function browser_supported ()
  {
    $browser = $this->env->browser ();
    return $browser->is (Browser_robot) ||
           ($browser->supports (Browser_CSS_2) &&
            $browser->supports (Browser_JavaScript) &&
            $browser->supports (Browser_cookie) &&
            $browser->supports (Browser_alpha_PNG) &&
            $browser->supports (Browser_columns));
  }

  /**
   * @access private
   */
  public function start_display ()
  {
    $page = $this->page;

    if ($page->printable)
    {
      $page->display_options->use_DHTML = false;
      $options = $page->template_options; 
      $options->header_visible = false;
      $options->show_statistics = false;
      $options->show_links = false;
    }

    parent::start_display ();
  }
}