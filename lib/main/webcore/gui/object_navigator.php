<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.4.0
 */

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
 * @version 3.0.0
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
   */
  public $page_anchor = "navigator";

  /**
   * Text for link that goes to previous page in the near list.
   * If the number of entries in the list is greater than {@link $window_size},
   * this link is provided to jump 'size' entries back in the list.
   * @var string
   */
  public $list_previous_text = "[Previous Page]";

  /**
   * Text for link that goes to next page in the near list.
   * If the number of entries in the list is greater than {@link $window_size},
   * this link is provided to jump 'window_size' entries forward in the list.
   * @var string
   */
  public $list_next_text = "[Next Page]";

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
   * @param CONTEXT $context
   */
  public function OBJECT_NAVIGATOR ($context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);

    $opts = $context->display_options;
    $this->window_size = $opts->objects_to_show;
    $this->separator = $opts->menu_separator . "\n";
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

    $this->_context->set_selected_id ($this->_selected);
    if ($this->_context->is_valid ())
    {
      $this->_url = new URL ($this->page_link);

      // Build the context list

      if (! $this->_context->is_on_first_page ())
      {
        $this->_list_near_selected .= '<div style="text-align: center">' .
                                      $this->_text_for_control ($this->_context->previous_page_object, $this->list_previous_text, 'move_up') .
                                      "</div>\n";
      }

      $objs = $this->_context->objects_in_window;

      foreach ($objs as $obj)
      {
        $this->_list_near_selected .= $this->_text_for_list ($obj) . "<br>\n";
      }

      if (! $this->_context->is_on_last_page ())
      {
        $this->_list_near_selected .= '<div style="text-align: center">' .
                                      $this->_text_for_control ($this->_context->next_page_object, $this->list_next_text, 'move_down') .
                                      "</div>\n";
      }

      // Build the navigation controls

      if (! $this->_context->is_first ())
      {
        $this->_controls .= $this->_text_for_control ($this->_context->first_object, $this->controls_first_text, 'go_to_first') . $this->separator;
        $this->_controls .= $this->_text_for_control ($this->_context->previous_object, $this->controls_previous_text, 'go_to_previous') . $this->separator;
      }
      else
      {
        $this->_controls .= $this->app->resolve_icon_as_html ('{icons}buttons/go_to_first_disabled', $this->controls_first_text, '16px') . $this->separator;
        $this->_controls .= $this->app->resolve_icon_as_html ('{icons}buttons/go_to_previous_disabled', $this->controls_previous_text, '16px') . $this->separator;
      }

      if ($this->page_anchor)
      {
        $this->_controls .= '<span class="field" id="' . $this->page_anchor . '">' . $this->_context->position_of_selected_id .
                            '</span> of <span class="field">' . $this->_context->num_objects_in_list . '</span>' . $this->separator . "\n";
      }
      else
      {
        $this->_controls .= '<span class="field">' . $this->_context->position_of_selected_id .
                            '</span> of <span class="field">' . $this->_context->num_objects_in_list . '</span>' . $this->separator . "\n";
      }

      if (! $this->_context->is_last ())
      {
        $this->_controls .= $this->_text_for_control ($this->_context->next_object, $this->controls_next_text, 'go_to_next') . $this->separator;
        $this->_controls .= $this->_text_for_control ($this->_context->last_object, $this->controls_last_text, 'go_to_last');
      }
      else
      {
        $this->_controls .= $this->app->resolve_icon_as_html ('{icons}buttons/go_to_next_disabled', $this->controls_next_text, '16px') . $this->separator;
        $this->_controls .= $this->app->resolve_icon_as_html ('{icons}buttons/go_to_last_disabled', $this->controls_last_text, '16px');
      }
    }
  }

  /**
   * Return the object title as a link or text.
   * If this is the selected entry, it is returned as text (with style 'selected'), otherwise
   * a link to the entry's home page is returned.
   * @param NAMED_OBJECT $obj
   * @param string $text Text to use; may be empty.
   * @param string $icon
   * @return string
   * @access private
   */
  protected function _text_for_control ($obj, $text, $icon = '')
  {
    $this->_url->replace_argument ('id', $obj->id);
    $t = $this->_formatter_for_object ($obj);
    $t->max_visible_output_chars = 0;
    $title = $obj->title_as_plain_text ($t);

    if ($icon)
    {
      $text = $this->app->resolve_icon_as_html ("{icons}buttons/$icon", $title, '16px');
    }

    $title = $this->context->text_options->convert_to_html_attribute ($title);

    if ($this->page_anchor)
    {
      return '<a href="' . $this->_url->as_html () . '#' . $this->page_anchor . '" title="' . $title . '">' . $text . '</a>';
    }

    return '<a href="' . $this->_url->as_html () . '" title="' . $title . '">' . $text . '</a>';
  }

  /**
   * Return the object title for the list.
   * @param NAMED_OBJECT $obj
   * @return string
   */
  protected function _text_for_list ($obj)
  {
    $id = $obj->id;
    $t = $this->_formatter_for_object ($obj);
    $this->_url->replace_argument ('id', $obj->id);
    $t->location = $this->_url->as_text ();
    if ($this->page_anchor)
    {
      $t->location .= '#' . $this->page_anchor;
    }

    if ($id == $this->_selected)
    {
      $t->CSS_class = 'selected';
      $Result = $obj->title_as_html ($t);
    }
    else
    {
      $t->CSS_class = '';
      $Result = $obj->title_as_link ($t);
    }
    return $Result;
  }

  /**
   * Return a formatter for this entry.
   * Override in descendants to customize the formatter.
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

  /**
   * Previous/next/first/last controls.
   * @see ENTRY_NAVIGATOR::_generate()
   * @var string
   * @access private
   */
  protected $_controls;

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
}

?>