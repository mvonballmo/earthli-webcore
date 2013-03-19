<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */

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

/** */
require_once ('webcore/obj/webcore_object.php');
include_once ('webcore/gui/time_frame_selector.php');

/**
 * Name of the default empty panel.
 * @see PANEL_MANAGER
 */
define ('Empty_panel_id', '__empty');

/**
 * Function should move the panel's display location.
 * @see Panel_selection
 * @see PANEL_MANAGER::move_panel_to()
 */
define ('Panel_location', 1);
/**
 * Function should move the panel's selection location.
 * This changes when the panel will be selected by default.
 * @see Panel_location
 * @see PANEL_MANAGER::move_panel_to()
 */
define ('Panel_selection', 2);
/**
 * Function should move the panel's display and selection location.
 * @see Panel_selection
 * @see Panel_location
 * @see PANEL_MANAGER::move_panel_to()
 */
define ('Panel_all', 3);

/**
 * Manages a list of {@link PANEL}s in a page.
 * When the same URL must handle different 'panels' of information,
 * use the panel manager and panels to handle the transition from one panel of
 * information to another. Generally, you should override this class, using
 * calls to {@link add_panel()} from {@link _add_panels()} to create the panels
 * and use {@link move_panel_to()} or {@link move_panel_after()} to adjust which
 * panels are selected by default (empty panels are not shown).
 * @see PANEL
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */
class PANEL_MANAGER extends WEBCORE_OBJECT
{
  /**
   * Base all links on this URL.
   * @var string
   */
  public $page_link;

  /**
   * Id of the currently-displayed panel.
   * @var string
   */
  public $selected_panel_id = '';

  /**
   * Use this panel if there are no visible panels.
   * @var string
   */
  public $empty_panel_id = Empty_panel_id;

  /**
   * Options used by {@link PANELS} in this set.
   * Initialized in the constructor using {@link _make_options()}.
   * @var PANEL_OPTIONS
   */
  public $options;

  /**
   * @param APPLICATION $app Main application.
   * @param boolean $show_time_menu Show a TIME_FRAME_SELECTOR with the panels?
   */
  public function __construct ($app, $show_time_menu = true)
  {
    parent::__construct ($app);

    if ($show_time_menu)
    {
      $this->time_menu = new TIME_FRAME_SELECTOR ($app);
    }
    
    $this->page_link = $app->env->url (Url_part_no_host_path);
    $this->options = $this->_make_options ();
    $this->_init_options ($this->options);

    $this->add_panel (new EMPTY_PANEL ($this));
    $this->_add_panels ();

    $class_name = $this->context->final_class_name ('PANEL_MANAGER_HELPER');
    $helper = new $class_name ($this->context);
    $helper->configure ($this);

    $this->_init_selected_panel_from_request ();
  }

  /**
   * Return whether or not the panel exists.
   * @param string $id
   * @return boolean
   */
  public function is_panel ($id)
  {
    return $id && isset ($this->_panels [$id]);
  }

  /**
   * Get the panel for the given id.
   * Raises an exception if not found.
   * @param string $id
   * @return PANEL
   */
  public function panel_at ($id)
  {
    $this->assert ($this->is_panel ($id), "Panel [$id] does not exist.", 'panel_at', 'PANEL_MANAGER');
    return $this->_panels [$id];
  }

  /**
   * The displayed panel.
   * @return PANEL
   */
  public function selected_panel ()
  {
    if ($this->is_panel ($this->selected_panel_id))
    {
      return $this->_panels [$this->selected_panel_id];
    }

    return null;
  }

  /**
   * Retrieve panels in location or selection order.
   * @see PANEL
   * @param integer $flag Can be {@link Panel_location} or {@link Panel_selection}.
   * @return array[PANEL]
   */
  public function ordered_panels ($flag)
  {
    switch ($flag)
    {
      case Panel_location:
        $panel_ids = $this->_location_order;
        break;
      case Panel_selection:
        $panel_ids = $this->_selection_order;
        break;
      default:
        $this->raise ('Unknown flag [' . $flag . ']', 'ordered_panels', 'PANEL_MANAGER');
    }

    $Result = array ();
    foreach ($panel_ids as $id)
      $Result [] = $this->_panels [$id];

    return $Result;
  }

  /**
   * Add this panel to the list.
   * Commonly called from {@link _add_panels()}. Use {@link move_panel_to()} or
   * {@link move_panel_after()} to modify the order.
   * @param PANEL $panel
   */
  public function add_panel ($panel)
  {
    $this->assert (isset ($panel) && ! empty ($panel->id), 'Panel and panel id cannot be empty', 'add_panel', 'PANEL_MANAGER');
    $this->assert (! $this->is_panel ($panel->id), "Panel [$panel->id] already exists.", 'add_panel', 'PANEL_MANAGER');

    $this->_panels [$panel->id] = $panel;

    if (isset ($this->_location_add_index))
    {
      $this->_move_panel_to ($panel->id, $this->_location_add_index, $this->_location_order);
      $this->_location_add_index += 1;
    }
    else
    {
      $this->_location_order [] = $panel->id;
    }

    if (isset ($this->_selection_add_index))
    {
      $this->_move_panel_to ($panel->id, $this->_selection_add_index, $this->_selection_order);
      $this->_selection_add_index += 1;
    }
    else
    {
      $this->_selection_order [] = $panel->id;
    }
  }

  /**
   * Move the panel 'id' to the 'index' in the ordering.
   * The panel and index position must both exist.
   * @see move_panel_after()
   * @param string $id Name of the panel to move.
   * @param integer $index Position in the ordering.
   * @param integer $flags Can be {@link Panel_location}, {@link
   * Panel_selection} or {@link Panel_all}.
   */
  public function move_panel_to ($id, $index, $flags)
  {
    $this->assert ($this->is_panel ($id), "Panel [$id] does not exist.", 'move_panel_to', 'PANEL_MANAGER');
    $this->assert (($index >= 0) && ($index < sizeof ($this->_selection_order)), "Index [$index] is out of range.", 'move_panel_to', 'PANEL_MANAGER');
    if ($flags & Panel_location)
    {
      $this->_move_panel_to ($id, $index, $this->_location_order);
    }
    if ($flags & Panel_selection)
    {
      $this->_move_panel_to ($id, $index, $this->_selection_order);
    }
  }

  /**
   * Move the panel 'id' after panel 'after'.
   * Both panels must exist.
   * @see move_panel_to()
   * @param string $id Name of the panel to move.
   * @param string $after Name of the panel after which panel 'id' should
   * appear.
   */
  public function move_panel_after ($id, $after, $flags)
  {
    $this->assert ($this->is_panel ($after), "Panel [$after] (after) does not exist.", 'move_panel_after', 'PANEL_MANAGER');
    if ($flags & Panel_location)
    {
      $index = array_search ($after, $this->_location_order);
      $this->move_panel_to ($id, $index + 1, Panel_location);
    }

    if ($flags & Panel_selection)
    {
      $index = array_search ($after, $this->_selection_order);
      $this->move_panel_to ($id, $index + 1, Panel_selection);
    }
  }

  /**
   * Subsequent panels are added after panel 'id'.
   * If a descendent needs to insert a lot of panels, set the insertion
   * point so that calls to {@link add_panel()} insert after panel 'id' rather
   * than appending. Panels can always be moved with {@link move_panel_after()}
   * and {@link move_panel_to()}.
   * @param string $id Name of the panel after which to insert.
   */
  public function add_panels_after ($id)
  {
    $this->assert ($this->is_panel ($id), "Panel [$id] does not exist.", 'add_panels_after', 'PANEL_MANAGER');
    $this->_location_add_index = array_search ($id, $this->_location_order) + 1;
    $this->_selection_add_index = array_search ($id, $this->_selection_order) + 1;
  }

  /**
   * Render the time_frame selector.
   * Should be called only if the panel itself makes use of the selector. Otherwise, it will
   * not be initially and will throw an exception.
   * @see TIME_FRAME_SELECTOR.
   */
  public function display_time_menu ()
  {
    $this->assert (isset ($this->time_menu), 'time menu is not available (turned off in constructor)', 'display_time_menu', 'PANEL_MANAGER');
    $this->time_menu->display ();
  }

  /**
   * Display a selection of the available panels.
   */
  public function display ()
  {
  ?>
    <table cellpadding="0" cellspacing="0">
    <?php
      foreach ($this->_location_order as $id)
      {
        $panel = $this->_panels [$id];
        if ($panel->selectable ())
        {
    ?>
      <tr>
        <td class="label">
          <?php
            $num_objects = $panel->num_objects ();
            if ($num_objects)
            {
              echo $num_objects;
            }
          ?>
        </td>
        <td>
          <?php echo $panel->title_as_link (); ?>
        </td>
      </tr>
    <?php
        }
      }
    ?>
    </table>
<?php
  }

  /**
   * Determine which panel is selected.
   * Make sure to set the panel only if it exists. Otherwise, use a default.
   * @access private
   */
  protected function _init_selected_panel_from_request ()
  {
    // load the value from the request, then clear it if the panel doesn't exist

    $this->selected_panel_id = read_var ('panel');

    // If the selected panel doesn't exist, then set a default one.

    if (! $this->selected_panel ())
    {
      $this->selected_panel_id = $this->_find_default_panel_id ();
      $selected_panel = $this->selected_panel ();
      $this->assert (isset ($selected_panel), "Panel [$this->selected_panel_id] does not exist.", 'init_selected_panel_from_request', 'PANEL_MANAGER');
    }

    if (isset ($this->time_menu))
    {
      $panel = $this->selected_panel ();
      $this->time_menu->load_period_from_request ($panel->default_time_frame);
    }
  }

  /**
   * Search for a panel to show intially.
   * This will search for a non-empty panel and use the default one if a non-empty panel is not found.
   * @return string
   * @access private
   */
  protected function _find_default_panel_id ()
  {
    foreach ($this->_selection_order as $id)
    {
      if ($this->_panels [$id]->selectable ())
      {
        return $id;
      }
    }

    /* Return empty if no selectable panel is found. */

    return $this->empty_panel_id;
  }

  /**
   * Create the set of panels to use.
   * Override in descendent classes.
   * @access private
   */
  protected function _add_panels ()
  {
  }

  /**
   * Update an ordering array internally.
   * Called from {@link move_panel()}.
   * @param string $id Name of the panel to move.
   * @param integer $index Position in the ordering.
   * @param array &$panels List of panels to move.
   * @access private
   */
  protected function _move_panel_to ($id, $index, &$panels)
  {
    $old_index = array_search ($id, $panels);
    if ($old_index !== false)
    {
      unset ($panels [$old_index]);
    }
    array_insert ($panels, $index, $id);
  }

  /**
   * Create panel options for display.
   * @return PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PANEL_OPTIONS ();
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
  }

  /**
   * @var array[string,PANEL]
   * @see PANEL
   * @access private
   */
  protected $_panels;

  /**
   * Panels ids in the order they should be selected.
   * By default, these are in the order in which they were added with {@link
   * add_panel()}.
   * @var array[PANEL]
   * @see PANEL
   * @access private
   */
  protected $_selection_order;

  /**
   * Panels ids in the order they should be displayed.
   * By default, these are in the order in which they were added with {@link
   * add_panel()}.
   * @var array[PANEL]
   * @see PANEL
   * @access private
   */
  protected $_location_order;

  /**
   * Insert new panels after this index in the selection.
   * @see add_panels_after()
   * @access private
   */
  protected $_selection_add_index;

  /**
   * Insert new panels after this index in the display.
   * @see add_panels_after()
   * @access private
   */
  protected $_location_add_index;
}

/**
 * Performs setup for various {@link PANEL_MANAGER}s.
 * Regiseter a descendent of this class and override {@link configure()} in
 * order to apply changes to all panel managers in an application (without
 * explicitly overriding each type).
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.1
 */
class PANEL_MANAGER_HELPER extends WEBCORE_OBJECT
{
  /**
   * Apply global options to a panel manager.
   * Does nothing by default.
   * @param PANEL_MANAGER $manager
   */
  public function configure ($manager)
  {
  }
}

/**
 * Manages and displays content within a page.
 * Used by the {@link PANEL_MANAGER} to switch between groups of content
 * within the same URL. Panels will be displayed in the selection list if they
 * are {@link selectable()}. They are selectable if they are {@link $visible}
 * and have positive {@link num_objects()} or if they are {@link
 * informational()}.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 */
abstract class PANEL extends WEBCORE_OBJECT
{
  /**
   * @var string
   */
  public $id = 'objects';

  /**
   * @var string
   */
  public $title = 'Objects';

  /**
   * @var boolean
   */
  public $visible = true;

  /**
   * Does this panel want the time selected rendered?
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * Which time frame is used as the default?
   * Used only if {@link $uses_time_selector} is <c>True</c>.
   * @var string
   */
  public $default_time_frame = Time_frame_recent;

  /**
   * State restriction hint for this panel.
   * If set, this is used by a search box to restrict to the state of the
   * selected panel.
   * @var integer
   */
  public $state = Visible;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   */
  public function __construct ($manager)
  {
    parent::__construct ($manager->app);
    $this->_panel_manager = $manager;
  }

  /**
   * Is this the selected panel?
   * @return boolean
   */
  public function selected ()
  {
    return $this->id == $this->_panel_manager->selected_panel_id;
  }

  /**
   * Can this be a selected panel?
   * If <code>True</code>, the panel can selected and will be rendered with a
   * link.
   */
  public function selectable ()
  {
    return $this->informational () || ($this->visible && $this->num_objects ());
  }

  /**
   * Return <code>True</code> to always display a link.
   * This allows panels that do not return anything from {@link num_objects()}
   * to be displayed as a link.
   * @return boolean
   */
  public function informational ()
  {
    return false;
  }

  /**
   * Format the title of this panel as a link.
   * @return string
   */
  public function title_as_link ()
  {
    if ($this->selected ())
    {
      return '<span class="selected">' . $this->raw_title () . '</span>';
    }

    if ($this->selectable ())
    {
      return '<a href="' . $this->page_link () . '">' . $this->raw_title () . '</a>';
    }

    return $this->raw_title ();
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->title;
  }

  /**
   * How many objects are displayed on this panel?
   * @return integer
   */
  public function num_objects ()
  {
    return 0;
  }

  /**
   * Renders this panel.
   * The page number is passed to the embedded grid, if applicable.
   * @abstract
   */
  public abstract function display ();

  /**
   * The URL needed to show this panel.
   * @return string
   * @access private
   */
  public function page_link ()
  {
    $pm = $this->_panel_manager;
    $url = new URL ($pm->page_link);
    $url->replace_argument ('panel', $this->id);
    return $url->as_html ();
  }

  /**
   * Commands associated with this panel.
   * @return COMMANDS
   */
  public function commands ()
  {
    return null;
  }
}

/**
 * Rendering options passed to a {@link PANEL}.
 * The {@link PANEL_MANAGER} prepares the options and passes them to the
 * {@link PANEL::display()} method.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class PANEL_OPTIONS
{
  /**
   * Allow DHTML in the grid?
   * @var boolean
   */
  public $use_DHTML = true;

  /**
   * Show user names in the grid?
   * Does not apply to all grids.
   * @var boolean
   */
  public $show_user = true;

  /**
   * Show folder info with objects in the grid?
   * Does not apply to all grids.
   * @var boolean
   */
  public $show_folder = true;

  /**
   * Retrieve the page number from this request variable.
   * @var string
   */
  public $page_number_var_name = 'page_number';

  /**
   * Copy these settings to the given object.
   * Simply assigns the properties and values from these options to properties
   * with the same name in 'obj'. Usually applied to a {@link GRID} object by
   * {@link GRID_PANEL}.
   * @param object $obj
   */
  public function apply_to ($obj)
  {
    $vars = get_object_vars ($this);
    foreach ($vars as $name => $value)
    {
      $obj->$name = $value;
    }
  }
}

/**
 * Panel manager with basic panel initialization.
 * Extends the very generalized {@link PANEL_MANAGER} to provide
 * support for {@link COMMENT_PANEL}s, {@link ENTRY_PANEL}s and {@link
 * FOLDER_PANEL}s.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class WEBCORE_PANEL_MANAGER extends PANEL_MANAGER
{
  /**
   * Add a {@link FOLDER_PANEL} for the given query.
   * @param array[FOLDER] $folders
   * @see FOLDER
   * @see _add_folder_panels()
   * @access private
   */
  protected function _add_folder_panels_for ($folders)
  {
    $class_name = $this->app->final_class_name ('FOLDER_PANEL');
    $panel = new $class_name ($this, $folders);
    $panel->recurse_tree_for_count = true;
    $this->add_panel ($panel);
  }

  /**
   * Add a {@link ENTRY_PANEL} for the given query.
   * Iterates the {@link APPLICATION::entry_type_infos()} list, adding
   * a panel with {@link _add_entry_panel()} for each registered type.
   * @param QUERY $query
   * @see _add_entry_panels()
   * @access private
   */
  protected function _add_entry_panels_for ($query)
  {
    $type_infos = $this->app->entry_type_infos ();

    foreach ($type_infos as $type_info)
    {
      $query_for_type = clone($query);
      $query_for_type->set_type ($type_info->id);
      $class_name = $this->app->final_class_name ('ENTRY_PANEL', '', $type_info->id);
      $this->_add_entry_panel_for ($query_for_type, $type_info, $class_name);
    }
  }

  /**
   * Add a {@link COMMENT_PANEL} for the given query.
   * @param QUERY $query
   * @see _add_comment_panels()
   * @access private
   */
  protected function _add_comment_panel_for ($query)
  {
    $class_name = $this->app->final_class_name ('COMMENT_PANEL');
    $panel = new $class_name ($this, $query);
    $this->add_panel ($panel);
  }

  /**
   * Add an {@link ENTRY_PANEL} to the manager.
   * Override in descendents to adjust the configuration and number of panels to
   * add per entry. {@link DRAFTABLE_ENTRY}s have more than one panel and are
   * configured by {@link _add_draft_panels_for()}.
   * @param QUERY $query
   * @param TYPE_INFO $type_info Type information for the {@link ENTRY}.
   * @param string $panel_class_name Name of the {@link PANEL} class to create.
   * */
  protected function _add_entry_panel_for ($query, $type_info, $panel_class_name)
  {
    $this->add_panel (new $panel_class_name ($this, $query, $type_info));
    if ($type_info->draftable)
    {
      $panel = $this->panel_at ($type_info->id);
      $panel->query->filter_out (Unpublished);
      $this->_add_draft_panels_for ($query, $type_info, $panel_class_name);
    }
  }

  /**
   * Add a {@link ENTRY_PANEL} for drafting states.
   * Adds three panels, one for {@link Draft}s, one for {@link Abandoned}
   * entries and one for {@link Queued} entries.
   * @param QUERY $query
   * @see _add_entry_panels_for()
   * @access private
   */
  protected function _add_draft_panels_for ($query, $type_info, $panel_class_name)
  {
    $draft_query = clone($query);
    $draft_query->set_filter (Draft);
    $draft_query->set_day_field ('entry.time_created');
    $panel = new $panel_class_name ($this, $draft_query, $type_info);
    $panel->id = 'drafts';
    $panel->title = 'Drafts';
    $panel->state = Draft;
    $this->add_panel ($panel);

    $queued_query = clone($query);
    $queued_query->set_filter (Queued);
    $draft_query->set_day_field ('entry.time_created');
    $panel = new $panel_class_name ($this, $queued_query, $type_info);
    $panel->id = 'queued';
    $panel->title = 'Queued';
    $panel->state = Queued;
    $this->add_panel ($panel);

    $abandoned_query = clone($query);
    $abandoned_query->set_filter (Abandoned);
    $draft_query->set_day_field ('entry.time_created');
    $panel = new $panel_class_name ($this, $abandoned_query, $type_info);
    $panel->id = 'abandoned';
    $panel->title = 'Abandoned';
    $panel->state = Abandoned;
    $this->add_panel ($panel);

    /* Select the published and draft panels first, if non-empty. */
    $this->move_panel_to ($type_info->id, 0, Panel_selection);
    $this->move_panel_after ('drafts', $type_info->id, Panel_all);
  }
}

/**
 * Placeholder for an index page-specific {@link PANEL_MANAGER}.
 * Provides an extension point for applications to override display of an
 * application's home page.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class INDEX_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  /**
   * @param APPLICATION $app Main application.
   * @param array[FOLDER] $folders Show panels for these folders.
   * @see FOLDER
   */
  public function __construct ($app, $folders)
  {
    $this->_folders = $folders;
    parent::__construct ($app);
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = true;
    $options->show_user = true;
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    $this->_add_folder_panels_for ($this->_folders);
    $this->_add_entry_panels_for ($this->login->all_entry_query ());
    $this->_add_comment_panel_for ($this->login->all_comment_query ());

    $class_name = $this->app->final_class_name ('GROUP_PANEL');
    $group_query = $this->app->group_query ();
    $panel = new $class_name ($this, $group_query);
    $this->add_panel ($panel);

    $class_name = $this->app->final_class_name ('USER_PANEL');
    $user_query = $this->app->user_query ();
    $show_anon = read_var ('show_anon');
    if ($show_anon)
    {
      $user_query->set_kind (Privilege_kind_anonymous);
    }
    else
    {
      $user_query->set_kind (Privilege_kind_registered);
    }
    $panel = new $class_name ($this, $user_query);
    $this->add_panel ($panel);

    $class_name = $this->app->final_class_name ('THEME_PANEL');
    $theme_query = $this->app->theme_query ();
    $panel = new $class_name ($this, $theme_query);
    $this->add_panel ($panel);

    $class_name = $this->app->final_class_name ('ICON_PANEL');
    $icon_query = $this->app->icon_query ();
    $panel = new $class_name ($this, $icon_query);
    $this->add_panel ($panel);
  }

  /**
   * @var array[FOLDER]
   * @see FOLDER
   * @access private
   */
  protected $_folders;
}

/**
 * Placeholder for a folder page-specific {@link PANEL_MANAGER}.
 * Provides an extension point for applications to override display of a
 * {@link FOLDER}'s home page.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class FOLDER_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  public function __construct ($folder)
  {
    $this->_folder = $folder;

    parent::__construct ($folder->app);

    $this->page_link = $folder->replace_page_arguments ($this->page_link);
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = false;
    $options->show_user = true;
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    $this->_add_folder_panels_for ($this->_folder->sub_folders (), true);
    if (! $this->_folder->is_organizational ())
    {
      $this->_add_entry_panels_for ($this->_folder->entry_query (), false, true);
      $this->_add_comment_panel_for ($this->_folder->comment_query (), false, true);
    }
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}

/**
 * Placeholder for a user page-specific {@link PANEL_MANAGER}.
 * Provides an extension point for applications to override display of a
 * {@link USER}'s home page.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class USER_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  public function __construct ($user)
  {
    $this->_user = $user;

    parent::__construct ($user->app);

    $this->page_link = $user->replace_page_arguments ($this->page_link);
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = true;
    $options->show_user = false;
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    $class_name = $this->app->final_class_name ('USER_SUMMARY_PANEL');
    $this->add_panel(new $class_name ($this, $this->_user));
    $this->_add_entry_panels_for ($this->login->user_entry_query ($this->_user->id), true, false);
    $this->_add_comment_panel_for ($this->login->user_comment_query ($this->_user->id), true, false);
    $this->move_panel_to ('summary', 0, Panel_all);
  }

  /**
   * @var USER
   * @access private
   */
  protected $_user;
}

/**
 * The default panel if there is no other content.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */
class EMPTY_PANEL extends PANEL
{
  /**
   * @var string
   */
  public $id = Empty_panel_id;

  /**
   * @var string
   */
  public $title = 'No Content';

  /**
   * @var boolean
   */
  public $visible = false;

  /**
   * @return integer
   */
  public function num_objects ()
  {
    return 0;
  }

  /**
   * Renders this panel (does nothing here).
   */
  public function display () {}
}

/**
 * A panel that uses a {@link GRID} to display content.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 */
abstract class GRID_PANEL extends PANEL
{
  /**
   * Number of rows to use in the grid.
   * @var integer
   */
  public $rows = 15;
  
  /**
   * Number of columns to use in the grid.
   * @var integer
   */
  public $columns = 1;

  /**
   * Renders this panel.
   */
  public function display ()
  {
    $grid = $this->_make_grid ();

    $this->_panel_manager->options->apply_to ($grid);
    $this->_set_up_grid ($grid);

    $grid->set_ranges ($this->rows, $this->columns);
    $grid->display ();
  }

  /**
   * Configure the grid before displaying it.
   * @param GRID $grid The grid to be displayed.
   * @access private
   */
  protected function _set_up_grid ($grid)
  {
  }

  /**
   * @return GRID
   * @access private
   * @abstract
   */
  protected abstract function _make_grid ();
}

/**
 * A grid panel that displays the results of a {@link QUERY}.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 */
abstract class QUERY_PANEL extends GRID_PANEL
{
  /**
   * @var boolean
   */
  public $uses_time_selector = true;
  
  /**
   * @var QUERY
   */
  public $query;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query)
  {
    parent::__construct ($manager);
    $this->query = $query;
  }

  /**
   * @return integer
   */
  public function num_objects ()
  {
    return $this->query->size ();
  }

  /**
   * Configure the grid before displaying.
   * @param GRID $grid The grid to be displayed.
   * @access private
   */
  protected function _set_up_grid ($grid)
  {
    if ($this->uses_time_selector && $this->_panel_manager->time_menu)
    {
      $this->_panel_manager->time_menu->prepare_query ($this->query);
    }
    $grid->set_query ($this->query);
  }
}

/**
 * Shows {@link HISTORY_ITEM}s from a {@link QUERY} in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */
class HISTORY_ITEM_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'history';
  
  /**
   * @var string
   */
  public $title = 'History';

  /**
   * @return HISTORY_ITEM_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('HISTORY_ITEM_GRID', 'webcore/gui/history_item_grid.php');
    return new $class_name ($this->app);
  }
}

/**
 * Shows {@link COMMENT}s from a {@link QUERY} in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */
class COMMENT_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'comments';
  
  /**
   * @var string
   */
  public $title = 'Comments';
  
  /**
   * @var boolean
   */
  public $show_folder = true;

  /**
   * Number of columns to use in the grid.
   * @var integer
   */
  public $columns = 2;

  /**
   * @return COMMENT_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('COMMENT_GRID', 'webcore/gui/comment_grid.php');
    return new $class_name ($this->app);
  }
}

/**
 * Shows {@link GROUP}s from a {@link QUERY} in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.5.0
 */
class GROUP_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'groups';
  
  /**
   * @var string
   */
  public $title = 'Groups';
  
  /**
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query)
  {
    parent::__construct ($manager, $query);
    $this->visible = $this->app->login->is_allowed (Privilege_set_group, Privilege_view);
  }

  /**
   * @return GROUP_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('GROUP_GRID', 'webcore/gui/group_grid.php');
    return new $class_name ($this->app);
  }

  /**
   * The URL needed to show this panel.
   * @return string
   * @access private
   */
  public function page_link ()
  {
    return 'view_groups.php';
  }
}

/**
 * Shows {@link USER}s from a {@link QUERY} in a {@link GRID}.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.5.0
 */
class USER_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'users';
  
  /**
   * @var string
   */
  public $title = 'Users';

  /**
   * Number of columns to use in the grid.
   * @var integer
   */
  public $columns = 3;

  /**
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query)
  {
    parent::__construct ($manager, $query);
    $this->visible = $this->app->login->is_allowed (Privilege_set_user, Privilege_view);
  }

  /**
   * @return USER_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('USER_GRID', 'webcore/gui/user_grid.php');
    return new $class_name ($this->app);
  }

  /**
   * Commands associated with this panel.
   * @return COMMANDS
   */
  public function commands ()
  {
    $class_name = $this->app->final_class_name ('USER_MANAGEMENT_COMMANDS', 'webcore/cmd/user_management_commands.php');
    return new $class_name ($this->app);
  }

  /**
   * The URL needed to show this panel.
   * @return string
   * @access private
   */
  public function page_link ()
  {
    return 'view_users.php';
  }
}

/**
 * Shows {@link THEME}s from a {@link QUERY} in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class THEME_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'themes';
  
  /**
   * @var string
   */
  public $title = 'Themes';
  
  /**
   * @var boolean
   */
  public $uses_time_selector = false;
  
  /**
   * Number of rows to use in the grid.
   * @var integer
   */
  public $rows = 10;
  
  /**
   * Number of columns to use in the grid.
   * @var integer
   */
  public $columns = 3;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query)
  {
    parent::__construct ($manager, $query);
    $this->visible = $this->app->login->is_allowed (Privilege_set_global, Privilege_resources);
  }

  /**
   * @return GROUP_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('THEME_GRID', 'webcore/gui/theme_grid.php');
    return new $class_name ($this->app);
  }

  /**
   * The URL needed to show this panel.
   * @return string
   * @access private
   */
  public function page_link ()
  {
    return 'view_themes.php';
  }
}

/**
 * Shows {@link ICON}s from a {@link QUERY} in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class ICON_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'icons';
  
  /**
   * @var string
   */
  public $title = 'Icons';
  
  /**
   * @var boolean
   */
  public $uses_time_selector = false;
  
  /**
   * Number of rows to use in the grid.
   * @var integer
   */
  public $rows = 8;
  
  /**
   * Number of columns to use in the grid.
   * @var integer
   */
  public $columns = 3;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query)
  {
    parent::__construct ($manager, $query);
    $this->visible = $this->app->login->is_allowed (Privilege_set_global, Privilege_resources);
  }

  /**
   * @return ICON_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('ICON_GRID', 'webcore/gui/icon_grid.php');
    return new $class_name ($this->app);
  }

  /**
   * The URL needed to show this panel.
   * @return string
   * @access private
   */
  public function page_link ()
  {
    return 'view_icons.php';
  }
}

/**
 * Shows {@link ENTRY}s from a {@link QUERY} in a {@link GRID}.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class ENTRY_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'entries';
  
  /**
   * @var string
   */
  public $title = 'Entries';

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param QUERY $query Show objects from this query.
   */
  public function __construct ($manager, $query, $type_info = null)
  {
    parent::__construct ($manager, $query);
    if (! isset ($type_info))
    {
      $type_info = $this->app->type_info_for ('ENTRY', 'webcore/obj/entry.php');
    }
    $this->_type_info = $type_info;
    $this->id = $type_info->id;
    $this->title = $type_info->plural_title;
  }

  /**
   * @return ENTRY_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('ENTRY_GRID', 'webcore/gui/entry_grid.php', $this->_type_info->id);
    return new $class_name ($this->app);
  }

  /**
   * @var TYPE_INFO
   * @access private
   */
  protected $_type_info;
}

/**
 * Shows {@link FOLDER}s in a grid.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 */
class FOLDER_PANEL extends GRID_PANEL
{
  /**
   * @var string
   */
  public $id = 'folders';
  
  /**
   * @var string
   */
  public $title = 'Folders';
  
  /**
   * @var integer
   */
  public $columns = 3;
  
  /**
   * @var integer
   */
  public $rows = 5;
  
  /**
   * @var array[FOLDER]
   * @see FOLDER
   */
  public $folders;

  /**
   * @param PANEL_MANAGER $manager Owner of this panel.
   * @param array[FOLDER] $folders Show this list of folders in this panel.
   * @see FOLDER
   */
  public function __construct ($manager, $folders)
  {
    parent::__construct ($manager);
    $this->folders = $folders;
    $type_info = $this->app->type_info_for ('FOLDER', 'webcore/obj/folder.php');
    $this->id = $type_info->id;
    $this->title = $type_info->plural_title;
  }

  /**
   * @return integer
   */
  public function num_objects ()
  {
    if ($this->folders)
    {
      if ($this->recurse_tree_for_count)
      {
        return $this->recursive_num_objects ($this->folders);
      }

      return sizeof ($this->folders);
    }

    return 0;
  }

  /**
   * Renders this panel.
   */
  public function display ()
  {
    $old_val = $this->recurse_tree_for_count;
    $this->recurse_tree_for_count = false;
    parent::display ();
    $this->recurse_tree_for_count = $old_val;
  }

  /**
   * @param array[FOLDER]
   * @return integer
   * @access private
   */
  public function recursive_num_objects ($folders)
  {
    $Result = sizeof ($folders);
    if (is_array ($folders))
    {
      foreach ($folders as $folder)
      {
        $Result += $this->recursive_num_objects ($folder->sub_folders ());
      }
    }

    return $Result;
  }

  /**
   * @return RECIPE_BOOK_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_GRID', 'webcore/gui/folder_grid.php');
    return new $class_name ($this->app);
  }

  /**
   * Configure the grid before displaying it.
   * @param GRID $grid The grid to be displayed.
   * @access private
   */
  protected function _set_up_grid ($grid)
  {
    $grid->set_folders ($this->folders);
  }

  /**
   * Use the number of tree nodes for 'num_objects'?
   * @var bool
   * @access private
   */
  public $recurse_tree_for_count = false;
}

/**
 * Displays a {@link FORM} within a panel.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 */
abstract class FORM_PANEL extends PANEL
{
  /**
   * Renders this panel.
   */
  public function display ()
  {
    $this->form->display ();
  }

  /**
   * Process the form for this panel.
   * @abstract
   */
  public abstract function check ();
}

/**
 * Displays a summary of all user information.
 * @package webcore
 * @subpackage panels
 * @version 3.3.0
 * @since 2.7.0
 */
class USER_SUMMARY_PANEL extends PANEL
{
  /**
   * @var string
   */
  public $id = 'summary';
  
  /**
   * @var string
   */
  public $title = 'Summary';

  /**
   * @param USER_PANEL_MANAGER $panel_manager
   * @param USER $user
   */
  public function __construct ($panel_manager, $user)
  {
    parent::__construct ($panel_manager);
    $this->_user = $user;
  }

  /**
   * Return <code>True</code> to always display a link.
   * @return boolean
   */
  public function informational ()
  {
    return true;
  }

  /**
   * @param PANEL_OPTIONS $options
   */
  public function display ()
  {
    $renderer = $this->_user->handler_for (Handler_html_renderer);
    $options = $renderer->options ();
    $options->show_users = false;
    $renderer->display ($this->_user);
  }

  /**
   * @var USER
   * @access private
   */
  protected $_user;
}

?>