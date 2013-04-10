<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/entry_renderer.php');
require_once ('webcore/gui/location_renderer.php');

/**
 * Renders a {@link PROJECT_ENTRY} for display in an email (plain text or HTML).
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.4.1
 * @abstract
 */
abstract class PROJECT_ENTRY_RENDERER extends ENTRY_RENDERER
{
  /**
   * Outputs branches for this entry.
   * @param PROJECT_ENTRY $entry
   * @access private
   */
  protected function _echo_branches_as_html ($entry)
  {
  ?>
  <dl>
  <?php
    /* If the entry is new or cloned, use the current information, not
       that stored in the database. */

    $branch_infos = $entry->current_branch_infos ();
    if (sizeof ($branch_infos) == 0)
    {
      $branch_info_query = $entry->branch_info_query ();
      $branch_infos = $branch_info_query->objects ();
    }

    foreach ($branch_infos as $branch_info)
    {
      $rel = $branch_info->release ();
  ?>
    <dt>
    <?php
      if ($branch_info->is_main () && (sizeof ($branch_infos) > 1))
      {
        echo '<span title="Used for non-branch-specific lists.">&bull;&nbsp;</span>';
      }
      $branch = $branch_info->branch ();
      if ($branch->locked ())
      {
        echo $this->app->resolve_icon_as_html ('{icons}indicators/locked', 'Locked', '16px') . ' ';
      }
      echo $branch_info->title_as_link ();
      echo $this->app->display_options->object_separator;
      if ($rel)
      {
        if ($rel->locked ())
        {
          echo $this->app->resolve_icon_as_html ('{icons}indicators/locked', 'Locked', '16px') . ' ';
        }
        $rel_status = $rel->status ();

        echo $rel->title_as_link ();
      }
      else
      {
        $this->_echo_html_branch_release_info ($branch_info);
      }
    ?>
    </dt>
    <dd class="text-flow">
      <?php
        echo $this->_echo_html_branch_info ($entry, $branch_info);
        if (isset ($rel_status))
        {
          echo '<br>' . $rel_status->as_html ();
        }
      ?>
    </dd>
  <?php
    }
  ?>
  </dl>
  <?php
  }

  /**
   * Show the extra description in a DHTML layer.
   * @param PROJECT_ENTRY $entry
   * @access private
   */
  protected function _echo_html_extra_description ($entry)
  {
    if ($entry->extra_description && ! $this->_options->preferred_text_length)
    {
      $layer = $this->context->make_layer ("id_{$entry->id}_long_description");
      $layer->margin_left = '1em';
      $layer->visible = ! $this->context->dhtml_allowed();
?>
<div style="margin-bottom: .75em">
<?php
      if (! $layer->visible)
      {
        $layer->draw_toggle ();
      }
      echo ' <span class="field">' . strlen ($entry->extra_description) . ' bytes</span> of extra information';
?>
</div>
<div>
<?php
      $layer->start ();
      echo $entry->extra_description_as_html ();
      $layer->finish ();
?>
</div>
<?php
    }
  }

  /**
   * Show information for this branch as HTML.
   * @param PROJECT_ENTRY $obj
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   * @abstract
   */
  protected abstract function _echo_html_branch_info ($obj, $branch_info);

  /**
   * Show information for a branch's release as HTML.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   * @abstract
   */
  protected abstract function _echo_html_branch_release_info ($branch_info);

  /**
   * Render properties for this entry as plain text.
   * @param PROJECT_ENTRY $entry
   * @access private
   */
  protected function _echo_branches_as_plain_text ($entry)
  {
    $branch_infos = $entry->current_branch_infos ();
    if (sizeof ($branch_infos) == 0)
    {
      $branch_info_query = $entry->branch_info_query ();
      $branch_infos = $branch_info_query->objects ();
    }
    foreach ($branch_infos as $branch_info)
    {
      $is_main_branch = $branch_info->is_main () && (sizeof ($branch_infos) > 1);

      if ($is_main_branch)
      {
        echo "\x95 ";
      }
      else
      {
        echo '  ';
      }

      echo $branch_info->title_as_plain_text ();
      echo $this->app->mail_options->object_separator;
      $rel = $branch_info->release ();
      if ($rel)
      {
        $status = $rel->status ();
        echo $rel->title_as_plain_text ();
      }
      else
      {
        $this->_echo_plain_text_branch_release_info ($branch_info);
      }

      echo $this->line ();

      if (isset ($status))
      {
        echo '    ' . $this->line ($status->as_plain_text ());
      }

      $this->_echo_plain_text_branch_info ($entry, $branch_info);

      echo $this->par ('');
    }
  }

  /**
   * Display a user and date in plain text.
   * Formatted as: ['caption']: 'time' by 'user'
   * @param string $caption
   * @param USER $user
   * @param DATE_TIME $time
   * @access private
   */
  protected function _echo_plain_text_user ($caption, $user, $time)
  {
    echo $this->line ('[' . $caption . ']: ' . $this->time ($time) . ' by ' . $user->title_as_plain_text ());
  }

  /**
   * Render the description for this entry as plain text.
   * @param PROJECT_ENTRY $entry
   * @access private
   */
  protected function _echo_plain_text_extra_description ($entry)
  {
    if (! $this->_options->preferred_text_length && $entry->extra_description)
    {
      if ($entry->description)
      {
        echo $this->sep ();
        echo $this->line ();
      }
      echo $entry->extra_description_as_plain_text ();
    }
  }

  /**
   * Show information for this branch as plain text.
   * @param PROJECT_ENTRY $obj
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   * @abstract
   */
  protected abstract function _echo_plain_text_branch_info ($obj, $branch_info);

  /**
   * Show information for a branch's release as plain text.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   * @abstract
   */
  protected abstract function _echo_plain_text_branch_release_info ($branch_info);

  /**
   * Outputs the object for print preview.
   * @param PROJECT_ENTRY $entry
   */
  public function display_as_printable ($entry)
  {
    $this->_hide_users = ! $this->_options->show_users;
    parent::display_as_printable ($entry);
  }

  /**
   * Show user info when renderered?
   * The print preview can toggle this value.
   * @var boolean
   * @access private
   */
  protected $_hide_users = false;
}

/**
 * Renders a location for a {@link PROJECT_ENTRY} into a {@link PAGE}.
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.9.1
 */
class PROJECT_ENTRY_LOCATION_RENDERER extends OBJECT_IN_FOLDER_LOCATION_RENDERER
{
  /**
   * Render any parent objects to the title and location.
   * @param PAGE $page
   * @param PROJECT_ENTRY $obj
   * @access private
   */
  protected function _add_context ($page, $obj)
  {
    parent::_add_context ($page, $obj);

    $branch_info = $obj->main_branch_info ();
    $branch = $branch_info->branch ();
    $release = $branch_info->release ();

    $page->location->add_object_link ($branch);
    $page->title->add_object ($branch);

    if (isset ($release))
    {
      $page->location->add_object_link ($release);
      $page->title->add_object ($release);
    }
  }
}

?>