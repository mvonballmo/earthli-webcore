<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage page
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/gui/page_renderer.php');

/**
 * Provides automated {@link THEME} support.
 * @package webcore
 * @subpackage page
 * @version 3.4.0
 * @since 2.2.1
 */
class THEMED_PAGE_RENDERER extends PAGE_RENDERER
{
  /**
   * Helper function called from {@link _include_styles_and_scripts()}.
   */
  public function display_styles ()
  {
    $page = $this->page;
    $theme = $page->theme;

    $res = $page->resources ();
    $styles [] = $res->resolve_file ('{styles}core/core');
    if (! $theme->dont_apply_to_forms)
    {
      $styles [] = $res->resolve_file ('{styles}core/core_forms');
    }
    $styles [] = $res->resolve_file ($theme->font_size_CSS_file_name);
    $styles [] = $res->resolve_file ($theme->font_name_CSS_file_name);
    $styles [] = $res->resolve_file ($theme->main_CSS_file_name);
    if (! $theme->dont_apply_to_forms)
    {
      $url = new URL ($theme->main_CSS_file_name);
      $url->append_to_name ('_forms');
      $styles [] = $res->resolve_file ($url->as_text ());
    }

    foreach ($styles as $style)
    {
?>
  <link rel="stylesheet" type="text/css" href="<?php echo $style; ?>">
<?php
    }
    parent::display_styles ();
  }
  
  /**
   * Helper function called from {@link _include_styles_and_scripts()}.
   */
  public function display_scripts ()
  {
    $page = $this->page;
    $script_folder = $page->path_to (Folder_name_scripts);
    $icon_folder = $page->path_to (Folder_name_icons);
?>
  <script type="text/javascript">
    var image_path = "<?php echo $icon_folder; ?>";
    var image_extension = "<?php echo $page->extension_for_alias (Folder_name_icons); ?>";
  </script>
  <script type="text/javascript" src="<?php echo "{$script_folder}webcore_base.js"; ?>"></script>
<?php
    parent::display_scripts ();
  }
}

?>