<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_history_items.php');

/**
 * Manages the audit trail of a {@link PROJECT_ENTRY}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class PROJECT_ENTRY_HISTORY_ITEM extends ENTRY_HISTORY_ITEM
{
  /**
   * Record changes to branches when storing?
   * This is set to true only when branch information has been loaded. It allows the history item to be
   * safely used without explicitly loading branch information and without generating spurious
   * 'removed from branch x' messages in the history item.
   * @var boolean
   */
  public $compare_branches = false;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param PROJECT_ENTRY $orig
   * @param PROJECT_ENTRY $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_text_difference ('Extra description', $orig->extra_description, $new->extra_description);

    if ($orig->kind != $new->kind)
    {
      $orig_title = $orig->kind_as_text ();
      $new_title = $new->kind_as_text ();
      $this->_record_string_difference ('Kind', $orig_title, $new_title);
    }

    if ($orig->component_id != $new->component_id)
    {
      $orig_comp = $orig->component ();
      $new_comp = $new->component ();
      $this->_record_object_difference ('Component', $orig_comp, $new_comp);
    }

    if ($this->compare_branches)
    {
      $orig_branches = $orig->stored_branch_infos ();
      $new_branches = $new->current_branch_infos ();

      if (sizeof ($orig_branches))
      {
        foreach ($orig_branches as $branch_id => $orig_branch)
        {
          if (! isset ($new_branches [$branch_id]))
          {
            $this->record_difference ('Removed from branch [' . $orig_branch->title_as_plain_text () . '].');
          }
          else
          {
            $this->_record_branch_differences ($orig_branch, $new_branches [$branch_id]);
          }
        }
      }

      if (sizeof ($new_branches))
      {
        foreach ($new_branches as $branch_id => $new_branch)
        {
          if (! isset ($orig_branches [$branch_id]))
          {
            $this->record_difference ('Added to branch [' . $new_branch->title_as_plain_text () . '].');
          }
        }
      }
    }

    if ($orig->main_branch_id != $new->main_branch_id)
    {
      $this->_record_object_difference ('Main branch', $orig->main_branch_info (), $new->main_branch_info ());
    }
  }

  /**
   * Record differences for two branches.
   * @param PROJECT_ENTRY_BRANCH_INFO $orig_branch
   * @param PROJECT_ENTRY_BRANCH_INFO $new_branch
   * @access private
   */
  protected function _record_branch_differences ($orig_branch, $new_branch)
  {
    if ($orig_branch->release_id != $new_branch->release_id)
    {
      $branch_title = $orig_branch->title_as_plain_text ();
      $this->_record_object_difference ("Release for branch [$branch_title]", $orig_branch->release (), $new_branch->release (), 'Current');
    }
  }
}

/**
 * Manages the audit trail of a {@link JOB}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class JOB_HISTORY_ITEM extends PROJECT_ENTRY_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param JOB $orig
   * @param JOB $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    if ($orig->reporter_id != $new->reporter_id)
    {
      $this->_record_object_difference ('Reporter', $orig->reporter (), $new->reporter ());
    }

    parent::_record_differences ($orig, $new);

    $this->_record_time_difference ('Time needed', $orig->time_needed, $new->time_needed);

    if ($orig->assignee_id != $new->assignee_id)
    {
      $this->_record_object_difference ('Assignee', $orig->assignee (), $new->assignee ());
    }
  }

  /**
   * Record differences for two branches.
   * @param PROJECT_ENTRY_BRANCH_INFO $orig_branch
   * @param PROJECT_ENTRY_BRANCH_INFO $new_branch
   * @access private
   */
  protected function _record_branch_differences ($orig_branch, $new_branch)
  {
    parent::_record_branch_differences ($orig_branch, $new_branch);

    if ($orig_branch->status != $new_branch->status)
    {
      $orig_title = $orig_branch->status_as_text ();
      $new_title = $new_branch->status_as_text ();
      $branch_title = $orig_branch->title_as_plain_text ();
      $this->_record_string_difference ("Status in branch [$branch_title]", $orig_title, $new_title);
    }

    if ($orig_branch->priority != $new_branch->priority)
    {
      $orig_title = $orig_branch->priority_as_text ();
      $new_title = $new_branch->priority_as_text ();
      $branch_title = $orig_branch->title_as_plain_text ();
      $this->_record_string_difference ("Priority in branch [$branch_title]", $orig_title, $new_title);
    }
  }
}

/**
 * Manages the audit trail of a {@link CHANGE}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class CHANGE_HISTORY_ITEM extends PROJECT_ENTRY_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param CHANGE $orig
   * @param CHANGE $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    $this->_record_text_difference ('Files', $orig->files, $new->files);

    if ($orig->job_id != $new->job_id)
    {
      $this->_record_object_difference ('Job', $orig->job (), $new->job (), '(no job)', '\'', '\'');
    }

    parent::_record_differences ($orig, $new);
  }
}

/**
 * Manages the audit trail of a {@link BRANCH}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class BRANCH_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_branch;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param BRANCH $orig
   * @param BRANCH $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->parent_release_id != $new->parent_release_id)
    {
      $this->_record_object_difference ('Based-on release', $orig->parent_release (), $new->parent_release ());
    }
  }
}

/**
 * Manages the audit trail of a {@link RELEASE}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class RELEASE_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_release;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param RELEASE $orig
   * @param RELEASE $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_text_difference ('Summary', $orig->summary, $new->summary);
    $this->_record_time_difference ('Ship date', $orig->time_scheduled, $new->time_scheduled);
    $this->_record_time_difference ('Test date', $orig->time_testing_scheduled, $new->time_testing_scheduled);

    if ($orig->branch_id != $new->branch_id)
    {
      $this->_record_object_difference ('Branch', $orig->branch (), $new->branch (), '(no branch)', '\'', '\'');
    }
  }
}

/**
 * Manages the audit trail of a {@link COMPONENT}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */
class COMPONENT_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_component;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param COMPONENT $orig
   * @param COMPONENT $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_string_difference ('Icon', $orig->icon_url, $new->icon_url);
  }
}

/**
 * Manages the audit trail of a {@link PROJECT}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 * @access private
 */
class PROJECT_HISTORY_ITEM extends FOLDER_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param PROJECT $orig
   * @param PROJECT $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->trunk_id != $new->trunk_id)
    {
      $this->_record_object_difference ('Trunk', $orig->trunk (), $new->trunk ());
    }

    $orig_options = $orig->options ();
    $new_options = $new->options ();

    if ($orig_options->inherited () != $new_options->inherited ())
    {
      $definer = $new_options->definer ();
      $this->record_difference ('Project options are now inherited from ' . $definer->title_as_plain_text ());
    }

    if (($orig_options->assignee_group_type != $new_options->assignee_group_type) || ($orig_options->assignee_group_id != $new_options->assignee_group_id))
    {
      $this->_record_string_difference ('Assignees', $this->_text_for_assignee_options ($orig_options), $this->_text_for_assignee_options ($new_options));
    }

    if (($orig_options->reporter_group_type != $new_options->reporter_group_type) || ($orig_options->reporter_group_id != $new_options->reporter_group_id))
    {
      $this->_record_string_difference ('Reporters', $this->_text_for_reporter_options ($orig_options), $this->_text_for_reporter_options ($new_options));
    }
      
    if ($orig_options->seconds_until_deadline != $new_options->seconds_until_deadline)
    {
      $this->_record_string_difference ('Release Warning', $orig_options->release_warning_description (), $new_options->release_warning_description ());
    }
  }

  /**
   * Description of assignee options.
   * @param PROJECT_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _text_for_assignee_options ($options)
  {
    return $this->_text_for_user_list_options ($options->assignee_group_type, $options->assignee_group ());
  }

  /**
   * Description of reporter options.
   * @param PROJECT_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _text_for_reporter_options ($options)
  {
    return $this->_text_for_user_list_options ($options->reporter_group_type, $options->reporter_group ());
  }

  /**
   * Description of reporter options.
   * @param PROJECT_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _text_for_user_list_options ($type, $group)
  {
    switch ($type)
    {
    case Project_user_all:
      return 'Allow all users';
    case Project_user_registered_only:
      return 'Allow only registered users';
    case Project_user_group:
      return 'Allow only users from ' . $group->title_as_plain_text ();
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($type);
    }
  }
}

?>