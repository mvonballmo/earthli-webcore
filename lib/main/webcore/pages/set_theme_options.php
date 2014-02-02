<?php

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

  $last_page = read_array_index ($_GET, 'last_page');

  if ($last_page)
  {
    $theme_name = read_array_index ($_GET, 'main_CSS_file_name', null);
    $theme_font_name = read_array_index ($_GET, 'font_name_CSS_file_name', null);
    $theme_font_size = read_array_index ($_GET, 'font_size_CSS_file_name', null);
    $theme_dont_apply_to_forms = read_array_index ($_GET, 'dont_apply_to_forms', null);

    if (isset ($theme_name))
    {
      $Page->set_theme_main ($theme_name);
    }
    if (isset ($theme_font_name))
    {
      $Page->set_theme_font_name ($theme_font_name);
    }
    if (isset ($theme_font_size))
    {
      $Page->set_theme_font_size ($theme_font_size);
    }
    if (isset ($theme_dont_apply_to_forms))
    {
      $Page->set_theme_dont_apply_to_forms ($theme_dont_apply_to_forms);
    }

    $Env->redirect_root ($last_page);
  }
  else
  {
    $Page->start_display ();
    echo "<div class=\"error\">Could not set theme options.</div>";
    $Page->finish_display ();
  }
?>