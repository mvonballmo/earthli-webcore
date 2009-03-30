<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.2.1
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
 * Controls page title display.
 * Manages a list of objects that indicate where within an application a user is.
 * By default, it displays: prefix group object[s] subject suffix.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.2.1
 */
class PAGE_TITLE extends WEBCORE_OBJECT
{
  /**
   * Separates individual pieces of the title.
   * @var string
   */
  public $separator = ' &gt; ';

  /**
   * Starts every page title.
   * @var string
   */
  public $prefix = '..:: ';

  /**
   *  Finishes every page title.
   * @var string
   */
  public $suffix = ' ::..';

  /**
   * The main area of the page.
   * @var string
   */
  public $group = '';

  /**
   * The specific intent of the page.
   * @var string
   */
  public $subject = '';

  /**
   * Add an object to the page context.
   * @param NAMED_OBJECT $obj
   */
  public function add_object ($obj)
  {
    $this->_objects [] = $obj;
  }
  
  /**
   * Return the rendered page title as text.
   * @return string
   */
  public function as_text ()
  {
    $opts = $this->context->text_options;
    if ($this->group)
    {
      $pieces [] = $opts->convert_to_html_entities ($this->group);
    }

    if (isset ($this->_objects) && sizeof ($this->_objects))
    {
      foreach ($this->_objects as $obj)
      {
        $titles [] = $opts->convert_to_html_entities ($obj->title_as_plain_text ());
      }
      $objects = join ($this->separator, $titles);
      if (isset ($objects))
      {
        $pieces [] = $objects;
      }
    }

    if ($this->subject)
    {
      $pieces [] = $opts->convert_to_html_entities ($this->subject);
    }

    if (isset ($pieces))
    {
      $Result = $this->prefix . join ($this->separator, $pieces) . $this->suffix;
    }
    else
    {
      $Result = $this->prefix . $this->suffix;
    }
      
    return $Result; 
  }

  /**
   * Render the page title.
   * Generally called from a PAGE_RENDERER between HTML title tags.
   * @see PAGE_RENDERER
   */
  public function display ()
  {
    echo $this->as_text ();
  }

  /**
   * List of objects to display in the title.
   * The renderer will show these objects' titles in added order.
   * @var array [NAMED_OBJECT]
   * @access private
   */
  protected $_objects;
}

?>