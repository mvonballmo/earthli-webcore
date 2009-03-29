<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Return {@link ATTACHMENT}s for an {@link ATTACHMENT_HOST}.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.5.0
 */
class ATTACHMENT_QUERY extends OBJECT_IN_SINGLE_FOLDER_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'att';

  /**
   * @param ATTACHMENT_HOST $host
   */
  function ATTACHMENT_QUERY ($host)
  {
    $this->_host = $host;
    $folder = $host->parent_folder ();
    OBJECT_IN_SINGLE_FOLDER_QUERY::OBJECT_IN_SINGLE_FOLDER_QUERY ($folder);
  }

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('att.*');
    $this->set_order ('att.time_created');
    $this->set_table ($this->app->table_names->attachments . ' att');
    $this->add_table ($this->_host->_table_name () . ' host', 'att.object_id = host.id');
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = 'att.object_id = ' . $this->_host->id;
  }

  /**
   * @return ATTACHMENT
   * @access private
   */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('ATTACHMENT', 'webcore/obj/attachment.php');
    $Result = new $class_name ($this->app);
    $Result->set_host ($this->_host);
    return $Result;
  }

  /**
   * Retrieve attachments for this object.
   * @var HOST
   * @access private
   */
  protected $_host;
  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_attachment;
}
?>