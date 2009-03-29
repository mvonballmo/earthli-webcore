<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage forms
 * @version 3.0.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Edit or create an {@link ARTICLE}.
 * @package news
 * @subpackage forms
 * @version 3.0.0
 * @since 2.4.0
 */
class ARTICLE_FORM extends DRAFTABLE_ENTRY_FORM
{
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  function _draw_controls ($renderer)
  {
    $renderer->set_width ('40em');
    $renderer->default_control_height = '25em';

    parent::_draw_controls ($renderer);
  }
}
?>