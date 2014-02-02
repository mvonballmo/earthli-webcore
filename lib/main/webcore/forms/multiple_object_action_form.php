<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
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
require_once ('webcore/forms/form.php');
require_once ('webcore/obj/object_list_builder.php');

/**
 * Performs an action on multiple {@link FOLDER}s and/or {@link ENTRY}s.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 * @abstract
 */
abstract class MULTIPLE_OBJECT_ACTION_FORM extends ID_BASED_FORM
{
  /**
   * Holds information on the selected folders and entries.
   * @var OBJECT_LIST_BUILDER
   */
  public $object_list;

  /**
   * @var string
   * @access private
   */
  public $method = 'request';

  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * @param FOLDER $folder Objects are from this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;

    $field = new ARRAY_FIELD ();
    $field->id = 'folder_ids';
    $field->caption = 'Folders';
    $field->visible = false;
    $this->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'entry_ids';
    $field->caption = 'Entries';
    $field->visible = false;
    $this->add_field ($field);

    $this->object_list = new OBJECT_LIST_BUILDER ($folder);    
  }

  /**
   * Apply the action to all selected folders and entries.
   * @param object $obj This parameter is ignored.
   * @access private
   */
  public function commit ($obj)
  {
    if ($this->object_list->has_folders ())
    {
      $this->_folders_run ();
    }

    if ($this->object_list->has_entries ())
    {
      $this->_entries_run ();
    }
  }

  /**
   * Read in values from the {@link $method} array.
   * @access private
   */
  protected function _load_from_request ()
  {
    parent::_load_from_request ();
    $this->object_list->load_from_request ();
    $this->set_value ('entry_ids', $this->object_list->entry_ids ());
    $this->set_value ('folder_ids', $this->object_list->folder_ids ());
  }

  /**
   * Execute action for all selected folders.
   * @access private
   */
  protected function _folders_run ()
  {
    foreach ($this->object_list->folders as &$folder)
    {
      $this->_folder_run ($folder);
    }
  }

  /**
   * Execute action for all selected entries.
   * @access private
   */
  protected function _entries_run ()
  {
    foreach ($this->object_list->entries as &$entry)
    {
      $this->_entry_run ($entry);
    }
  }

  /**
   * Execute action for a single folder.
   * @param FOLDER $fldr
   * @access private
   * @abstract
   */
  protected abstract function _folder_run ($fldr);

  /**
   * Execute action for a single entry.
   * @param ENTRY $entry
   * @access private
   * @abstract
   */
  protected abstract function _entry_run ($entry);

  /**
   * Displays the list of selected entries and folders.
   * Broken out into this method to all descendants to change the layout, but
   * still make use of this pre-built list.
   * @access private
   */
  protected function _draw_selected_objects ()
  {
?>
<div class="left-sidebar" style="white-space: nowrap">
  <?php
    $count = sizeof ($this->object_list->folders);

    if ($count)
    {
      $folder_info = $this->app->type_info_for ('FOLDER');
      echo '<h2>' . $folder_info->plural_title . '</h2>';
      echo '<ul class="object-list">';
      
      foreach ($this->object_list->folders as &$folder)
      {
        echo '<li>' . $folder->title_as_link () . "</li>";
      }

      echo '</ul>';
    }

    $count = sizeof ($this->object_list->entries);
    if ($count)
    {
      $current_type = 'none';
      $index = 0;
      foreach ($this->object_list->entries as &$entry)
      {
        $type_info = $entry->type_info ();

        if ($type_info->plural_title != $current_type)
        {
          if ($index != 0)
          {
            echo '</p>';
          }
          $current_type = $type_info->plural_title;
          echo "<h2>{$type_info->plural_title}</h2>\n";
          echo '<ul class="object-list">';
        }

        echo "<li>";
        echo $entry->title_as_link ();
        echo "</li>\n";

        $index += 1;
      }

      echo "</ul>\n";
    }
  ?>
</div>
<?php
  }

  /**
   * Draw a confirmation message for this action.
   * @param FORM_RENDERER $renderer
   * @access private
   * @abstract
   */
  protected abstract function _draw_message ($renderer);

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->set_width ('');

    $renderer->start ();

    if ($this->object_list->has_objects ())
    {
      $renderer->start_column ();
      $renderer->start_row ('', ' ');
        echo $this->_draw_selected_objects ();
      $renderer->finish_row ();
      $renderer->start_column ();
      $this->_draw_message ($renderer);
      $renderer->finish_column ();
    }
    else
    {
      $renderer->draw_text_row ('', 'No items were selected. Please select at least one item and try again.', 'error');
    }

    $renderer->finish ();
  }

  /**
   * List of selected folders.
   * Available only when not submitted.
   * @var array[FOLDER]
   * @access private
   */
  public $folders;

  /**
   * List of selected entries.
   * Available only when not submitted.
   * @var array[ENTRY]
   * @access private
   */
  public $entries;
}
?>