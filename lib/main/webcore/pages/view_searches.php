<?php

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

  $Page->title->subject = 'Searches';

  $App->set_referer ();

  $Page->location->add_root_link ();
  $Page->location->append ($Page->title->subject);

  $Page->start_display ();
?>
  <div class="main-box">
    <?php
      $search_query = $App->login->search_query ();
      $class_name = $App->final_class_name ('SEARCH_GRID', 'webcore/gui/search_grid.php');
      /** @var SEARCH_GRID $grid */
      $grid = new $class_name ($App);
      $grid->last_page = '';
      $grid->set_ranges (10, 1);
      $grid->set_query ($search_query);
      $grid->display ();
    ?>
  </div>
<?php
  $Page->finish_display ();
?>