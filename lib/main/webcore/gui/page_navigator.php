<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
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

require_once ('webcore/obj/webcore_object.php');

/**
 * A navigator for browsing numbered pages of information.
 * Generates a page navigator with a list of numbers and list navigation buttons.
 * The default implementation looks like this:
 *
 * << < 1 * 2 * 3 * 4 * 5 * 6 * 7 > >>
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.2.1
 */
class PAGE_NAVIGATOR extends WEBCORE_OBJECT
{
  /**
   * Page number to display.
   * Normalizes the page number to a value in the range calculated from 'set_ranges'.
   * If this number is zero, then the value is automatically read from the page request
   * value stored in {@link PAGE_NAVIGATOR::$page_number_var_name}.
   * @see PAGE_NAVIGATOR::set_ranges()
   * @var integer
   */
  public $page_number = 0;

  /**
   * Retrieve the page number from this request variable.
   * Only used if {@link PAGE_NAVIGATOR::$page_number} is 0.
   * @var string
   */
  public $page_number_var_name = 'page_number';

  /**
   * Page to which the navigator should link.
   * Specify a url that ends in 'var=', where 'var' is the name of the query string variable used to hold the
   * page number in the request.
   * @var string
   */
  public $page_link;

  /**
   * Optional page anchor for the page_link.
   * @var string
   */
  public $page_anchor = '';

  /**
   * Inserted between page numbers.
   * @var string
   */
  public $separator = '&nbsp;&bull;&nbsp';

  /**
   * Inserted after first/previous links and before page numbers.
   * @var string
   */
  public $begin_block = '';

  /**
   * Inserted after page numbers and before next/last links.
   * @var string
   */
  public $end_block = '';

  /**
   * Number of page numbers to display at once.
   * Pages displayed will be centered on the current page if this number is exceeded. For example, if
   * this value is the default, 7, and there are 40 pages to display, when the 30th page is viewed, the
   * page numbers displayed are 27-33. Odd number work best here.
   * @var integer
   */
  public $pages_to_show = 7;

  /**
   * Offset the actual page number by this amount when displayed.
   * Useful when rendering things other than pages. For example, the calendar uses this offset to
   * display year numbers instead of page numbers.
   * @var integer
   */
  public $page_offset = 0;

  /**
   * Name used when displaying total pages.
   * If there are more pages than 'pages_to_show', the navigator indicates the total number of pages
   * at the end, like this (# Pages)
   * @var string
   */
  public $entry_type = 'Pages';

  /**
   * Show the total number of pages?
   * @var boolean
   */
  public $show_total = true;

  /**
   * Show the total number of pages?
   * @var boolean
   */
  public $show_first_and_last = true;

  /**
   * Show icons for the first/previous/next/last links?
   * @var boolean
   */
  public $use_icons_for_buttons = false;

  /**
   * Show disabled buttons for non-functional buttons? Or just hide them?
   * @var boolean
   */
  public $show_disabled_buttons = true;

  /**
   * Show the pager even if there's only a single page.
   * @var boolean
   */
  public $show_single_page = false;

  /**
   * Gets a value indicating whether to default to the last page if no page is explicitly set.
   * @var bool
   */
  public $default_to_last = false;

  /**
   * @param CONTEXT $context
   * @param integer $num_total_objects Total number of objects that need to be displayed.
   * @param integer $num_objects_per_page Number of objects to show per page.
   * @param bool $default_to_last Sets the initial value of {@link $default_to_last}
   * @throws Exception
   */
  public function __construct ($context, $num_total_objects = 0, $num_objects_per_page = 0, $default_to_last = false)
  {
    parent::__construct ($context);
    $this->separator = $context->display_options->page_separator;
    $this->default_to_last = $default_to_last;
    if ($num_objects_per_page > 0)
    {
      $this->set_ranges ($num_total_objects, $num_objects_per_page);
    }
    $this->page_link = $context->env->url (Url_part_no_host_path);
  }

  /**
   * Set the number of objects/objects per page.
   * The navigator will calculate the number of pages needed to display the
   * requested objects.
   * @param integer $num_total_objects
   * @param integer $num_objects_per_page
   */
  public function set_ranges ($num_total_objects, $num_objects_per_page)
  {
    $this->assert ($num_objects_per_page > 0, "'num_objects_per_page' must be greater than 0.", 'set_ranges', 'PAGE_NAVIGATOR');

    $this->_page_size = $num_objects_per_page;
    $this->_item_count = $num_total_objects;
    $this->_count = floor ($num_total_objects / $num_objects_per_page);
    if (($num_total_objects % $num_objects_per_page > 0) && ($num_objects_per_page > 1))
    {
      $this->_count += 1;
    }

    if (! $this->page_number)
    {
      $default_value = $this->default_to_last ? $this->_count : 1;
      $this->page_number = read_var ($this->page_number_var_name, $default_value);
    }

    if ($this->page_number > $this->_count)
    {
      $this->page_number = $this->_count;
    }
    if ($this->page_number < 1)
    {
      $this->page_number = 1;
    }
  }

  /**
   * Render the navigator.
   * @param bool $reset If true, the pager's content will be regenerated even if it already exists.
   */
  public function display ($reset = false)
  {
    echo $this->as_html ($reset);
  }

  /**
   * Return navigator as HTML.
   * @param bool $reset If true, the pager's content will be regenerated even if it already exists.
   * @return string
   */
  public function as_html ($reset = false)
  {
    if ($reset || ! isset ($this->_output))
    {
      $this->_generate ();
    }
    return $this->_output;
  }

  /**
   * Return the number of the first item on the page.
   * @see last_item_index()
   * @return integer
   */
  public function first_item_index ()
  {
    return $this->_page_size * ($this->page_number - $this->page_offset - 1) + 1;
  }

  /**
   * Return the number of the first item on the page.
   * @see first_item_index()
   * @return integer
   */
  public function last_item_index ()
  {
    return min ($this->first_item_index () + $this->_page_size - 1, $this->num_items ());
  }

  /**
   * Number of items in the list.
   * @see num_pages()
   * @return integer
   */
  public function num_items ()
  {
    return $this->_item_count;
  }

  /**
   * Number of pages needed to show the list.
   * @see num_items()
   * @return integer
   */
  public function num_pages ()
  {
    return $this->_count;
  }

  /**
   * Return the URL for the requested page number.
   * @param integer $page_num
   * @return string
   * @access private
   */
  protected function _make_page_link ($page_num)
  {
    $this->_url->replace_argument ($this->page_number_var_name, $page_num);
    $Result = $this->_url->as_html ();
    if (isset ($this->page_anchor))
    {
      $Result .= '#' . $this->page_anchor;
    }
    return $Result;
  }

  /**
   * Generate the navigator text to an internal buffer.
   * @access private
   */
  protected function _generate ()
  {
    $this->_output = '';

    if ($this->_count > 1 || $this->show_single_page)
    {
      $this->_output = '<ul class="menu-items buttons">';
      $this->_url = new URL ($this->page_link);

      if ($this->_count > 1)
      {
        $many_pages = $this->_count > $this->pages_to_show;

        // put in the first page if there are more pages than
        // can be displayed

        if ($this->show_first_and_last && $many_pages)
        {
          if ($this->page_number > 1)
          {
            $this->_output .= "<li><a class=\"button\" title=\"First Page\" href=\"" . $this->_make_page_link (1) . "\">" . $this->_get_button_content('go_to_first') . "</a></li>";
          }
          else if ($this->show_disabled_buttons)
          {
            $this->_output .= '<li><span class="button disabled">' . $this->_get_button_content('go_to_first_disabled') . "</span></li>";
          }
        }

        // put in the previous page, if necessary

        if ($this->page_number > 1)
        {
          $this->_output .= "<li><a class=\"button\" title=\"Previous Page\" href=\"" . $this->_make_page_link ($this->page_number - 1) . "\">" . $this->_get_button_content('go_to_previous') . "</a>";
        }
        else if ($this->show_disabled_buttons)
        {
          $this->_output .= '<li><span class="button disabled">' . $this->_get_button_content('go_to_previous_disabled') . "</span>";
        }

        $this->_output .= $this->begin_block;

        // make the list of numbers

        if ($this->page_number <= $this->pages_to_show)
        {
          $first_page = 1;
        }
        else
        {
          $first_page = min ($this->_count - $this->pages_to_show + 1, $this->page_number - floor ($this->pages_to_show / 2));
        }

        if ($this->_count < $this->pages_to_show)
        {
          $last_page = $this->_count;
        }
        else
        {
          $last_page = $first_page + $this->pages_to_show - 1;
        }

        for ($index = $first_page; $index <= $last_page; $index++)
        {
          $page_text = $index + $this->page_offset;
          if ($index == $this->page_number)
          {
            $this->_output .= "<li><span class=\"button selected\">$page_text</span></li>";
          }
          else
          {
            $this->_output .= "<li><a class=\"button\" href=\"" . $this->_make_page_link ($index) . "\">$page_text</a></li>";
          }

          if ($index < $last_page)
          {
            $this->_output .= $this->separator;
          }
        }

        $this->_output .= $this->end_block;

        // put in the next page, if necessary

        if ($this->page_number < $this->_count)
        {
          $this->_output .= "<li><a class=\"button\" title=\"Next Page\" href=\"" . $this->_make_page_link ($this->page_number + 1) . "\">" . $this->_get_button_content('go_to_next') . "</a></li>";
        }
        else if ($this->show_disabled_buttons)
        {
          $this->_output .= '<li><span class="button disabled">' . $this->_get_button_content('go_to_next_disabled') . "</span></li>";
        }

        if ($this->show_first_and_last && $many_pages)
        {
          if ($this->page_number < $this->_count)
          {
            $this->_output .= "<li><a class=\"button\" title=\"Last Page\" href=\"" . $this->_make_page_link ($this->_count) . "\">" . $this->_get_button_content('go_to_last') . "</a></li>";
          }
          else
          {
            $this->_output .= '<li><span class="button disabled">' . $this->_get_button_content('go_to_last_disabled') . "</span></li>";
          }


          if ($this->show_total)
          {
            $this->_output .= "<li><span class=\"page-total\"> ($this->page_number of $this->_count $this->entry_type)</span></li>";
          }
        }
      }
      else if ($this->show_single_page)
      {
        $this->_output .= "<li><span class=\"button selected\">1</span></li>";
      }

      $this->_output .= '</ul>';
    }
  }

  protected function _get_button_content($type)
  {
    if ($this->use_icons_for_buttons)
    {
      switch ($type)
      {
        case 'go_to_first':
        case 'go_to_first_disabled':
          $text = 'First page';
          break;
        case 'go_to_previous':
        case 'go_to_previous_disabled':
          $text = 'Previous page';
        break;
        case 'go_to_next':
        case 'go_to_next_disabled':
          $text = 'Next page';
        break;
        case 'go_to_last':
        case 'go_to_last_disabled':
          $text = 'Last page';
      }

      return $this->context->resolve_icon_as_html ('{icons}buttons/' . $type, Sixteen_px, $text);
    }
    else
    {
      switch ($type)
      {
        case 'go_to_first':
        case 'go_to_first_disabled':
          return '|&lt;';
        case 'go_to_previous':
        case 'go_to_previous_disabled':
          return '&lt;';
        case 'go_to_next':
        case 'go_to_next_disabled':
          return '&gt;';
        case 'go_to_last':
        case 'go_to_last_disabled':
          return '&gt;|';
      }
    }
  }

  /**
   * Rendered navigator. Created by 'generate'.
   * @see generate()
   * @access private
   */
  protected $_output;

  /**
   * How many objects per page?
   * @var integer
   * @access private
   */
  protected $_page_size;

  /**
   * How many pages are needed to display all objects?
   * @see set_ranges()
   * @var integer
   * @access private
   */
  protected $_count;

  /**
   * Number of objects to navigate.
   * @see set_ranges()
   * @var integer
   * @access private
   */
  protected $_item_count;

  /**
   * Used internally to generate links.
   * @var URL
   * @access private
   */
  protected $_url;
}