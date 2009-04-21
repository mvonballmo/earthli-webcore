<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.6.0
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

/***/
require_once ('webcore/obj/webcore_object.php');

/**
 * Highlights PHP source.
 * For PHP versions 4.3 and higher, {@link $show_line_numbers} and {@link
 * $link_functions} control advanced output options not available with the
 * PHP function {@link PHP_MANUAL#highlight_file}.
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.6.0
 * @access private
 */
class HIGHLIGHTER extends WEBCORE_OBJECT
{
  /**
   * Generates line numbers for each source line.
   * @var boolean
   */
  public $show_line_numbers = true;

  /**
   * Links PHP functions to the PHP web site.
   * @var boolean
   */
  public $link_functions = true;

  /**
   * Syntax-highlight the contents of the current url.
	 * @see file_as_html()    
   * @return  string
   */
  public function current_as_html ()
  {
    $this->file_as_html (url_to_file_name ($this->env->url (Url_part_no_args)));
  }

  /**
   * Syntax-highlight the contents of the file as PHP.
   * Use {@link url_to_file_name()} to convert a URL to a local path.
   * @see current_as_html()
   * @param string $file_name Fully-resolved local path.
   * @return string
   */
  public function file_as_html ($file_name)
  {
    if (function_exists ('token_get_all'))
    {
      include_once ('third_party/aidan.dotgeek.org/PHP_Highlight.php');
      $highlighter = new PHP_Highlight ();
      $highlighter->link_functions = $this->link_functions;
      $highlighter->loadFile ($file_name);
      return $highlighter->toHtml (true, $this->show_line_numbers);
    }
    else
    {
      ob_start ();
        highlight_file ($file_name);
        $Result = ob_get_contents ();
      ob_end_clean ();
      return $Result;
    }
  }
}

?>