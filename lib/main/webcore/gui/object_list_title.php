<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.1
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
 * Formats the subject line for multiple objects.
 * Handles n object types at once and generates a subject describing this list. Uses
 * one {@link OBJECT_LIST_TITLE_ITEM} per object type added. Used by a
 * {@link PUBLISHER_MESSAGE} to generate the subject of an email.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.1
 * @access private
 */
class OBJECT_LIST_TITLE extends WEBCORE_OBJECT
{
  /**
   * Number of objects of all types.
   * @var integer
   */
  public $num_objects = 0;

  /**
   * Add an object to the subject.
   * @param WEBCORE_OBJECT $obj
   */
  function add_object ($obj)
  {
    $this->num_objects++;
    $type_info = $obj->type_info ();
    if (! isset ($this->_items [$type_info->id]))
    {
      $item = new OBJECT_LIST_TITLE_ITEM ();
      $item->type_info = $type_info;
      $this->_items [$type_info->id] = $item;
    }

    $this->_items [$type_info->id]->num_objects++;
  }

  /**
   * Use a fixed text for the subject.
   * Any added items are ignored and not used to generate the subject.
   * @param string $text
   */
  function set_text ($text)
  {
    $this->_text = $text;
  }

  /**
   * Return the subject as text
   * If {@link set_text()} was not called, all objects added with {@link add_object()}
   * are combined into a subject line.
   */
  function as_text ()
  {
    if ($this->_text)
    {
      return $this->_text;
    }
    else
    {
      foreach ($this->_items as $id => $item)
        $item_texts [] = $item->as_text ();
      return join ('/', $item_texts);
    }
  }

  /**
   * Used as the subject, if set.
   * @var string
   * @access private
   */
  protected $_text = '';
  /**
   * @var array [OBJECT_LIST_TITLE_ITEM]
   * @access private
   */
  protected $_items;
}

/**
 * Tracks number objects of a single type.
 * Used by an {@link OBJECT_LIST_TITLE} to generate a subject for a
 * {@link PUBLISHER_MESSAGE}.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.1
 * @access private
 */
class OBJECT_LIST_TITLE_ITEM
{
  /**
   * Descriptor for the objects in this item.
   * @var TYPE_INFO
   */
  public $type_info;
  /**
   * Number of objects in this item.
   * @var integer
   */
  public $num_objects = 0;

  function as_text ()
  {
    if ($this->num_objects > 0)
    {
      if ($this->num_objects > 1)
      {
        return $this->num_objects . ' ' . $this->type_info->plural_title;
      }


      return $this->num_objects . ' ' . $this->type_info->singular_title;
    }
  }
}

?>