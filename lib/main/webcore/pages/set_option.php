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
  $opt_name = read_array_index ($_GET, 'opt_name');
  $opt_value = read_array_index ($_GET, 'opt_value');
  $opt_page_context = read_array_index ($_GET, 'opt_page_context');
  $opt_duration = read_array_index ($_GET, 'opt_duration');
  
  if (isset ($App) && ! $opt_page_context)
  {
    $context = $App;
  }
  else
  {
    $context = $Page;
  }

  if ($last_page && $opt_name)
  {
    if (is_numeric ($opt_duration))
    {
      $context->storage->expire_in_n_days ($opt_duration);
    }
    else
    {
      $context->storage->expire_in_n_days ($context->storage_options->setting_duration);
    }
    
    $context->storage->set_value ($opt_name, $opt_value);
    $Env->redirect_root ($last_page);
  }
  else
  {
    $Page->start_display ();
    echo "<div class=\"error\">Could not set [$opt_name] to [$opt_value].</div>";
    $Page->finish_display ();
  }
?>