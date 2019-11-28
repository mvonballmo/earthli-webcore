<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.4.0
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Displays a list of {@link UNIQUE_OBJECT}s from a {@link QUERY}.
 * This list of object's is relative to a selected object and will display a 'window' of objects
 * surrounding the selected one.
 * Clients must call {@link set_query()} before calling {@link size()}, {@link
 * list_near_selected()} or {@link controls()}.
 * @see UNIQUE_OBJECT_CACHE
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.4.0
 */
class OBJECT_NAVIGATOR extends WEBCORE_OBJECT
{
  /**
   * Displayed between links in the controls.
   * Defaults to 'menu_separator' in application display options.
   * @see APPLICATION_DISPLAY_OPTIONS::$menu_separator
   * @var string
   */
  public $separator;

  /**
   * Page on which the navigator is displayed.
   * Generated links go this url.
   * @var string
   */
  public $page_link;

  /**
   * Text for the anchor of all generated links; can be empty.
   * @var string
   */
  public $page_anchor = "";

  /**
   * The name of the parameter to get/set in the query string.
   * @var string  */
  public $primary_key_field_name = "id";

  /**
   * Text for link that goes to previous page in the near list.
   * If the number of entries in the list is greater than {@link UNIQUE_OBJECT_CACHE::$window_size},
   * this link is provided to jump 'size' entries back in the list.
   * @var string
   */
  public $list_previous_text = "[Previous Items]";

  /**
   * Text for link that goes to next page in the near list.
   * If the number of entries in the list is greater than {@link UNIQUE_OBJECT_CACHE::$window_size},
   * this link is provided to jump 'window_size' entries forward in the list.
   * @var string
   */
  public $list_next_text = "[Next Items]";

  /**
   * Text for link that goes to first entry in the list.
   * @var string
   */
  public $controls_first_text = "First";

  /**
   * Text for link that goes to previous entry in the list.
   * @var string
   */
  public $controls_previous_text = "Previous";

  /**
   * Text for link that goes to next entry in the list.
   * @var string
   */
  public $controls_next_text = "Next";

  /**
   * Text for link that goes to last entry in the list.
   * @var string
   */
  public $controls_last_text = "Last";

  /**
   * Show icons for the previous/next links?
   * @var boolean
   */
  public $use_icons_for_buttons = false;

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->num_entries = Unassigned;
    $this->page_link = $this->env->url (Url_part_no_host_path);
  }

  /**
   * Assign the query to navigate.
   * This must be assigned before calling {@link size()}, {@link
   * list_near_selected()} or {@link controls()}.
   * @param QUERY $query
   */
  public function set_query ($query)
  {
    $this->_adjust_query ($query);
    include_once ('webcore/util/unique_object_cache.php');
    $this->_context = new UNIQUE_OBJECT_CACHE ($query);
    $this->_context->storage_enabled = false;
    $this->_query = $query;
  }

  /**
   * How many objects does this navigator iterate?
   * @return integer
   */
  public function size ()
  {
    return $this->_query->size ();
  }

  /**
   * Set the id of the selected {@link UNIQUE_OBJECT}.
   * Navigator will be built around this object. Must call this function before displaying the navigator.
   * @var integer $selected
   */
  public function set_selected ($selected)
  {
    $this->_selected = $selected;
  }

  /**
   * Returns first/previous/next/last controls centered on the selected entry.
   * @return string
   */
  public function controls ()
  {
    if (! isset ($this->_controls))
    {
      $this->_generate ();
    }
    return $this->_controls;
  }

  /**
   * Returns scripts for first/previous/next/last shortcut keys centered on the selected entry.
   * @return string
   */
  public function shortcut_key_scripts ()
  {
    if (! isset ($this->_shortcut_key_scripts))
    {
      $this->_generate ();
    }
    return $this->_shortcut_key_scripts;
  }

  /**
   * Returns scripts for current index and total items for the selected entry.
   * @return string
   */
  public function counts ()
  {
    if (! isset ($this->_counts))
    {
      $this->_generate ();
    }
    return $this->_counts;
  }

  /**
   * Returns a list of linked entries 'near' the selected entry.
   * @return string
   */
  public function list_near_selected ()
  {
    if (! isset ($this->_list_near_selected))
    {
      $this->_generate ();
    }
    return $this->_list_near_selected;
  }

  /**
   * Generate the controls and list.
   * @access private
   */
  protected function _generate ()
  {
    $this->_list_near_selected = '';
    $this->_controls = '';
    $this->_shortcut_key_scripts = '';

    $this->_context->set_selected_id ($this->_selected);
    if ($this->_context->is_valid ())
    {
      $this->_list_near_selected = '<ul>';

      $this->_url = new URL ($this->page_link);

      // Build the context list

      /** @var UNIQUE_OBJECT $first_object */
      $first_object = $this->_context->first_object;
      /** @var UNIQUE_OBJECT $previous_object */
      $previous_object = $this->_context->previous_object;
      /** @var UNIQUE_OBJECT $previous_page_object */
      $previous_page_object = $this->_context->previous_page_object;
      /** @var UNIQUE_OBJECT $next_object */
      $next_object = $this->_context->next_object;
      /** @var UNIQUE_OBJECT $next_page_object */
      $next_page_object = $this->_context->next_page_object;
      /** @var UNIQUE_OBJECT $last_object */
      $last_object = $this->_context->last_object;

      if (! $this->_context->is_on_first_page ())
      {
        $this->_list_near_selected .= '<li class="page-up">' . $this->_text_for_control ($previous_page_object, 'page_up') . "</li>\n";
      }

      $objs = $this->_context->objects_in_window;

      /** @var UNIQUE_OBJECT $obj */
      foreach ($objs as $obj)
      {
        $this->_list_near_selected .= '<li>' . $this->_text_for_list ($obj) . "</li>\n";
      }

      if (! $this->_context->is_on_last_page ())
      {
        $this->_list_near_selected .= '<li class="page-down">' . $this->_text_for_control ($next_page_object, 'page_down') . "</li>\n";
      }

      $this->_list_near_selected .= '</ul>';

      // Build the navigation controls

      if (! $this->_context->is_first ())
      {
        $this->_controls .= $this->_text_for_control ($first_object, 'first') . $this->separator;
        $this->_controls .= $this->_text_for_control ($previous_object, 'previous') . $this->separator;
        $this->_shortcut_key_scripts .= $this->_text_for_shortcut('ArrowLeft', 'swiped-right', $previous_object);
      }
      else
      {
        $this->_controls .= '<span class="button disabled">' . $this->_get_button_content('first') . '</span>';
        $this->_controls .= '<span class="button disabled">' . $this->_get_button_content('previous') . '</span>';
      }

      if (! $this->_context->is_last ())
      {
        $this->_controls .= $this->_text_for_control ($next_object, 'next') . $this->separator;
        $this->_controls .= $this->_text_for_control ($last_object, 'last');
        $this->_shortcut_key_scripts .= $this->_text_for_shortcut('ArrowRight', 'swiped-left', $next_object);
      }
      else
      {
        $this->_controls .= '<span class="button disabled">' . $this->_get_button_content('next') . '</span>';
        $this->_controls .= '<span class="button disabled">' . $this->_get_button_content('last') . '</span>';
      }

      $this->_counts = $this->_text_for_counters ();
      $this->_controls .= $this->_counts;
    }
  }

  protected function _text_for_counters ()
  {
    $id = '';
    if ($this->page_anchor)
    {
      $id = 'id="' . $this->page_anchor . '"';
    }

    return '<span class="counters"' . $id . '>' . $this->_context->position_of_selected_id .
      ' of ' . $this->_context->num_objects_in_list . $this->separator . "</span>";
  }

  /**
   * Return the object title as a link or text.
   * If this is the selected entry, it is returned as text (with style 'selected'), otherwise
   * a link to the entry's home page is returned.
   * @param UNIQUE_OBJECT $obj
   * @param string $type The type of button/link to generate.
   * @internal param string $text Text to use; may be empty.
   * @internal param string $icon
   * @return string
   * @access private
   */
  protected function _text_for_control ($obj, $type)
  {
    $this->_url->replace_argument ($this->primary_key_field_name, $obj->id);
    $t = $this->_formatter_for_object ($obj);
    $t->max_visible_output_chars = 0;
    $title = $obj->title_as_plain_text ($t);

    $text = $this->_get_button_content($type);
    $title = $this->_get_button_title($type) . ' (' . $this->context->text_options->convert_to_html_attribute ($title) . ')';

    $href = $this->_url->as_html ();
    if ($this->page_anchor)
    {
      $href .= '#' . $this->page_anchor;
    }

    return '<a class="button" href="' . $href . '" title="' . $title . '">' . $text . '</a>';
  }

  /**
   * Return the object title for the list.
   * @param UNIQUE_OBJECT $obj
   * @return string
   */
  protected function _text_for_list ($obj)
  {
    $id = $obj->id;
    $t = $this->_formatter_for_object ($obj);
    $this->_url->replace_argument ($this->primary_key_field_name, $obj->id);
    $t->location = $this->_url->as_text ();
    if ($this->page_anchor)
    {
      $t->location .= '#' . $this->page_anchor;
    }

    if ($id == $this->_selected)
    {
      $t->css_class = 'selected';
      $Result = $obj->title_as_html ($t);
    }
    else
    {
      $t->css_class = '';
      $Result = $obj->title_as_link ($t);
    }
    return $Result;
  }

  /**
   * Return a formatter for this entry.
   * Override in descendants to customize the formatter.
   * @param $obj UNIQUE_OBJECT
   * @return TITLE_FORMATTER
   * @access private
   */
  protected function _formatter_for_object ($obj)
  {
    return $obj->title_formatter ();
  }

  /**
   * Modify the query to navigate.
   * Called from {@link set_query()}.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
  }

  protected function _get_button_content($type)
  {
    if ($this->use_icons_for_buttons)
    {
      $title = $this->_get_button_title($type);

      switch ($type)
      {
        case 'first':
          return $this->context->resolve_icon_as_html ('{icons}buttons/go_to_first', Sixteen_px, $title);
        case 'previous':
          return $this->context->resolve_icon_as_html ('{icons}buttons/go_to_previous', Sixteen_px, $title);
        case 'next':
          return $this->context->resolve_icon_as_html ('{icons}buttons/go_to_next', Sixteen_px, $title);
        case 'last':
          return $this->context->resolve_icon_as_html ('{icons}buttons/go_to_last', Sixteen_px, $title);
        case 'page_up':
          return $this->context->resolve_icon_as_html ('{icons}buttons/move_up', Sixteen_px, $title);
        case 'page_down':
          return $this->context->resolve_icon_as_html ('{icons}buttons/move_down', Sixteen_px, $title);
      }
    }
    else
    {
      switch ($type)
      {
        case 'first':
          return '|&lt;';
        case 'previous':
          return '&lt;';
        case 'next':
          return '&gt;';
        case 'last':
          return '&gt;|';
        case 'page_up':
          return $this->list_previous_text;
        case 'page_down':
          return $this->list_next_text;
      }
    }
  }

  protected function _get_button_title($type)
  {
    switch ($type)
    {
      case 'first':
        return 'First page';
      case 'previous':
        return 'Previous page';
      case 'next':
        return 'Next page';
      case 'last':
        return 'Last page';
      case 'page_up':
        return $this->list_previous_text;
      case 'page_down':
        return $this->list_next_text;
    }
  }

  /**
   * @param string $keyName
   * @param string $event_name
   * @param UNIQUE_OBJECT $obj
   * @return string
   */
  private function _text_for_shortcut(string $keyName, string $event_name, UNIQUE_OBJECT $obj): string
  {
    $this->_url->replace_argument ($this->primary_key_field_name, $obj->id);
    $href = $this->_url->as_text ();

    return "
    document.addEventListener('keyup', e => { console.log(e); if (e.key === '{$keyName}') { window.location = '$href' } });
    document.addEventListener('{$event_name}', e => window.location = '$href');
    ";
  }

  /**
   * Previous/next/first/last controls.
   * @see ENTRY_NAVIGATOR::_generate()
   * @var string
   * @access private
   */
  protected $_controls;

  /**
   * Current and total item counts.
   * @see ENTRY_NAVIGATOR::_generate()
   * @var string
   * @access private
   */
  protected $_counts;

  /**
   * Previous/next/first/last scripts.
   * @see ENTRY_NAVIGATOR::_generate()
   * @var string
   * @access private
   */
  protected $_shortcut_key_scripts;

  /**
   * List of entries 'near' this selected one.
   * @see ENTRY_NAVIGATOR::_generate()
   * @var string
   * @access private
   */
  protected $_list_near_selected;

  /**
   * Objects for this list are pulled from this query.
   * @var QUERY
   * @access private
   */
  protected $_query;

  /**
   * Used to generate URLs for the items.
   * The current URL is used, replacing the id with each item's id.
   * @var URL
   * @access private
   */
  protected $_url;

  /**
   * Id of the selected object.
   * @var int
   */
  protected $_selected;

  /**
   * @var UNIQUE_OBJECT_CACHE
   */
  protected $_context;
}