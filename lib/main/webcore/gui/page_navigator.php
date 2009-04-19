<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * @version 3.1.0
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
  public $page_anchor = 'pages';

  /**
   * Inserted between page numbers.
   * @var string
   */
  public $separator = '&nbsp;&bull;&nbsp';

  /**
   * Inserted after first/previous links and before page numbers.
   * @var string
   */
  public $begin_block = '&nbsp;';

  /**
   * Inserted after page numbers and before next/last links.
   * @var string
   */
  public $end_block = '&nbsp;';

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
   * @param CONTEXT $context
   * @param integer $num_total_objects Total number of objects that need to be displayed.
   * @param integer $num_objects_per_page Number of objects to show per page.
   */
  public function __construct ($context, $num_total_objects = 0, $num_objects_per_page = 0)
  {
    parent::__construct ($context);
    $this->separator = $context->display_options->page_separator;
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
      $this->page_number = read_var ($this->page_number_var_name, 1);
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
   */
  public function display ()
  {
    echo $this->as_html ();
  }

  /**
   * Return navigator as HTML.
   * @return string
   */
  public function as_html ()
  {
    if (! isset ($this->_output))
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
    $this->_url = new URL ($this->page_link);

    if ($this->_count > 1)
    {
      $many_pages = $this->_count > $this->pages_to_show;

      // put in the first page if there are more pages than
      // can be displayed

      if ($many_pages && ($this->page_number > 1))
      {
        $this->_output = "<a title=\"First Page\" href=\"" .
                         $this->_make_page_link (1) .
                         "\">" .
                         $this->context->resolve_icon_as_html ('{icons}buttons/go_to_first', 'First page', '16px') .
                         "</a>&nbsp;";
      }

      // put in the previous page, if necessary

      if ($this->page_number > 1)
      {
        $this->_output .= "<a title=\"Previous Page\" href=\"" .
                          $this->_make_page_link ($this->page_number - 1) .
                          "\">" .
                          $this->context->resolve_icon_as_html ('{icons}buttons/go_to_previous', 'Previous page', '16px') .
                          "</a>&nbsp;";
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
          $this->_output .= "<span class=\"selected\">$page_text</span>";
        }
        else
        {
          $this->_output .= "<a href=\"" . $this->_make_page_link ($index) . "\">$page_text</a>";
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
        $this->_output .= "&nbsp;<a title=\"Next Page\" href=\"" .
                          $this->_make_page_link ($this->page_number + 1) .
                          "\">" .
                          $this->context->resolve_icon_as_html ('{icons}buttons/go_to_next', 'Next page', '16px') .
                          "</a>";
      }

      if ($many_pages)
      {
        if ($this->page_number < $this->_count)
        {
          $this->_output .= "&nbsp;<a title=\"Last Page\" href=\"" .
                            $this->_make_page_link ($this->_count) .
                            "\">" .
                            $this->context->resolve_icon_as_html ('{icons}buttons/go_to_last', 'Last page', '16px') .
                            "</a>";
        }

        if ($this->show_total)
        {
          $this->_output .= "&nbsp($this->_count $this->entry_type)";
        }
      }
    }
    else
    {
      $this->pages = "<span class=\"selected\">1</span>";
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
?>