<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
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

/** */
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Base class for queries that return {@link COMMENT}s.
 * This class returns comments from a single folder.
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 */
class COMMENT_QUERY extends OBJECT_IN_SINGLE_FOLDER_QUERY
{
  /**
   * @return COMMENT
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('COMMENT', 'webcore/obj/comment.php');
    return new $class_name ($this->app);
  }
  
  /**
   * Perform any setup needed on each returned object.
   * Create, prepare and set the entry for each comment.
   * @see _make_entry()
   * @see _prepare_entry()
   * @param COMMENT $obj
   * @access private
   */
  protected function _prepare_object ($obj)
  {
    parent::_prepare_object ($obj);
    $entry = $this->_make_entry ();
    $this->_prepare_entry ($entry);
    $obj->set_entry ($entry);
  }

  /**
   * Make the entry associated with a comment.
   * @return ENTRY
   * @access private
   */
  protected function _make_entry ()
  {
    $class_name = $this->app->final_class_name ('ENTRY', 'webcore/obj/entry.php');
    return new $class_name ($this->app);
  }

  /**
   * Set properties for the entry associated with a comment.
   * This is usually just the bare minimum of properties needed to display a
   * link to the entry. This avoids retrieving all the entry data when only the
   * link needs to be displayed (since this query shows comments, not full
   * entries).
   * @param ENTRY $entry The entry whose properties should be set.
   * @access private
   */
  protected function _prepare_entry ($entry)
  {
    $entry->set_parent_folder ($this->_folder);
    $entry->id = $this->db->f ('entry_id');
  }

  /**
   * @param COMMENT $parent
   * @param COMMENT $obj
   * @access private
   */
  protected function _obj_connect_to_parent ($parent, $obj)
  {
    $parent->add_comment ($obj);
  }

  /**
   * @param COMMENT $obj
   * @access private
   */
  protected function _obj_set_sub_objects_cached ($obj)
  {
    $obj->set_comments_cached (true);
  }

  /**
   * @return array[COMMENT]
   * @param COMMENT $obj
   * @access private
   */
  protected function _obj_sub_objects ($obj)
  {
    return $obj->sub_comments ();
  }

  /**
   * @var ENTRY
   * @access private
   */
  protected $_entry = null;

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_comment;
}

?>