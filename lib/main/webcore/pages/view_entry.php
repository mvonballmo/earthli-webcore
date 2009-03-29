<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder =& $folder_query->folder_for_entry_at_id ($id);

  if (isset ($folder))
  {
    $entry_query = $folder->entry_query ();

    /* Set this global value to optimize the query for accessing the particular
       type of entry. */
    if (isset ($entry_type_id))
    {
      $entry_query->set_type ($entry_type_id);
    }

    $entry =& $entry_query->object_at_id ($id);
  }

  if (isset ($entry) && $App->login->is_allowed (Privilege_set_entry, Privilege_view, $entry))
  {
    $App->set_referer ();
    $App->set_search_text (read_var ('search_text'));

    $entry_info = $entry->type_info ();
    
    $location_renderer = $entry->handler_for (Handler_location);
    $location_renderer->add_to_page_as_text ($Page, $entry);

    $navigator = $entry->handler_for (Handler_navigator);
    $navigator->set_query ($entry_query);
    $navigator->set_selected ($id);

    $Page->start_display ();
?>
  <div class="menu-bar-top">
  <?php 
    $renderer = $entry->handler_for (Handler_menu);
    $renderer->display ($entry->handler_for (Handler_commands));

    if ($navigator->size () > 1)
    {
      echo '<div style="float: left">';
      include_once ('webcore/util/options.php');
      $option = new STORED_OPTION ($App, "show_{$entry_info->id}_list");
      $show_entry_list = $option->value ();
      $opt_link = $option->setter_url_as_html (! $show_entry_list);
      if (! $show_entry_list)
      {
  ?>
    <a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/show_list', 'Show list', '16px'); ?></a>
  <?php
      }      

      echo $navigator->controls ();
      echo '</div>';
    }
  ?>
    <div style="clear: both"></div>
  </div>
  <?php
    $need_side_panel = ($navigator->size () > 1) && $show_entry_list;
     
    if ($need_side_panel)
    {
      $box = $Page->make_box_renderer ();
      $box->start_column_set ();
      $box->new_column ('padding-right: 1em; padding-top: 1em');
?>
      <div class="chart">
        <div class="chart-title">
          <div style="float: right"><a href="<?php echo $opt_link; ?>"><?php echo $App->resolve_icon_as_html ('{icons}buttons/close', 'Close list', '16px'); ?></a></div>
          <?php echo $entry_info->plural_title; ?>
        </div>
        <div class="chart-body" style="white-space: nowrap">
          <?php echo $navigator->list_near_selected (); ?>
        </div>
      </div>
<?php
      $box->new_column ('width: 75%');
    }     
?>
      <div class="box">
        <div class="box-title">
<?php
    $t = $entry->title_formatter ();
    $t->max_visible_output_chars = 0;
    echo $entry->title_as_html ($t);
?>
        </div>
        <div class="box-body">
<?php      
    $renderer = $entry->handler_for (Handler_html_renderer);
    $renderer->display ($entry);
?>
        </div>
      </div>
<?php
    if ($need_side_panel)
    {
      $box->finish_column_set ();
    }

    $associated_data = $entry->handler_for (Handler_associated_data);
    if (isset ($associated_data))
    {
      $associated_data->display ($entry);
    }

    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ("You are not allowed to view this item.", $folder);
  }
?>