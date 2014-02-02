<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.6.0
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
require_once ('webcore/gui/theme_grid.php');

/**
 * Displays {@link THEME}s from a {@link QUERY}.
 * Shows only the title of the theme, with a link to a page that will set that
 * theme for the browser.
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.6.0
 */
class THEME_SELECTOR_GRID extends THEME_GRID
{
  public $box_style = '';
  public $width = '';
  public $show_separator = false;
  public $centered = true;

  /**
   * @param THEME $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
		$setter_url = $this->page->resolve_file ('{pages}set_theme_options.php');
?>
  <div style="text-align: center">
    <a title="Switch to this theme" href="<?php echo $setter_url; ?>?last_page=<?php echo urlencode ($this->env->url ()); ?>&amp;main_CSS_file_name=<?php echo $obj->id; ?>"><?php echo $obj->title_as_plain_text (); ?></a>
  </div>
<?php
  }
}

?>
