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

$search_query = $App->login->search_query ();
/** @var $search SEARCH */
$search = $search_query->object_at_id (read_var ('id'));

if (isset ($search))
{
  $App->set_referer ();

  $Page->title->add_object ($search);
  $Page->title->subject = $search->title_as_html ();

  $Page->location->add_root_link ();
  $Page->location->append ('Searches', 'view_searches.php');
  $Page->location->append ($Page->title->subject);

  $Page->start_display ();

  $search_query = $search->prepared_query ();
  $num_results = $search_query->size ();
?>
<div class="top-box">
  <div class="button-content">
    <?php
    /** @var $renderer MENU_RENDERER */
    $renderer = $search->handler_for (Handler_menu);
    $renderer->display ($search->handler_for (Handler_commands));
    ?>
  </div>
</div>
<div class="main-box">
  <div class="text-flow">
  <?php
    /** @var $renderer OBJECT_RENDERER */
    $renderer = $search->handler_for (Handler_html_renderer);
    $renderer->display ($search);
  ?>
  <h2>
    <?php echo $num_results; ?> Result(s)
  </h2>
  <?php
    $grid = $search->grid ();
    $grid->show_folder = true;
    $grid->set_ranges (10, 2);
    $grid->set_query ($search_query);
    $grid->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to see this search.', $search);
  }
?>