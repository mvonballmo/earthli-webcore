<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  $folder = $folder_query->folder_for_entry_at_id ($id);

  if (isset ($folder))
  {
    $entry_query = $folder->entry_query ();

    /* Set this global value to optimize the query for accessing the particular
       type of entry. */
    if (isset ($entry_type_id))
    {
      $entry_query->set_type ($entry_type_id);
    }

    $entry = $entry_query->object_at_id ($id);
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
<div class="top-box">
  <?php
    if ($navigator->size () > 1)
    {
      include_once ('webcore/util/options.php');
      $option = new STORED_OPTION ($App, "show_{$entry_info->id}_list");
      $show_entry_list = $option->value ();
      $opt_link = $option->setter_url_as_html (! $show_entry_list);

      $need_side_panel = ($navigator->size () > 1) && $show_entry_list;

      echo '<div class="object-navigator">';

      if ($need_side_panel)
      {
        ?>
        <div class="links">
          <?php echo $navigator->list_near_selected (); ?>
        </div>
        <?php
      }

      echo '<p class="controls">';

      echo $navigator->controls ();

      ?>
      <a href="<?php echo $opt_link; ?>" class="list-toggle">
      <?php

      if (! $show_entry_list)
      {
        ?>
        <?php echo $App->resolve_icon_as_html ('{icons}buttons/show_list', 'Show list', '16px', ''); ?> <span class="caption">Show list</span>
        <?php
      }
      else
      {
        ?>
        <?php echo $App->resolve_icon_as_html ('{icons}buttons/close', 'Close list', '16px', ''); ?> <span class="caption">Close list</span></a>
        <?php
      }

      echo '</a>';

      $renderer = $entry->handler_for (Handler_menu);
      $renderer->set_size (Menu_size_compact);
      $renderer->alignment = Menu_align_inline;
      $renderer->display ($entry->handler_for (Handler_commands));

      echo '</p></div>';

      $subscription_status = $entry->handler_for (Handler_subscriptions);
      $subscription_status->display ($entry);
    }
?>
  </div>
  <div class="box">
    <div class="box-body" style="margin-top: 15px">
      <h1>
      <?php
      $t = $entry->title_formatter ();
      $t->max_visible_output_chars = 0;
      echo $entry->title_as_html ($t);
      ?>
      </h1>
<?php
    $renderer = $entry->handler_for (Handler_html_renderer);
    $renderer->display ($entry);
?>
    </div>
  </div>
<?php
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