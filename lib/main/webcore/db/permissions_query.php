<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/db/query.php');

/**
 * Return security {@link PERMISSIONS} objects.
 * @package webcore
 * @subpackage db
 * @version 3.3.0
 * @since 2.2.1
 */
class PERMISSIONS_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'perm';

  /**
   * Name of the SQL field for the ID in the "main" table.
   * @var string
   */
  public $id = 'ref_id';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_select = 'perm.*';
    $this->_order = 'kind DESC, importance DESC';
    $this->_tables = "{$app->table_names->folder_permissions} perm";
    $this->type = All;
  }

  /**
   * Specify which type of permissions to retrieve.
   * Can be {@link Privilege_kind_anonymous}, {@link Privilege_kind_registered},
   * {@link Privilege_kind_group} or {@link Privilege_kind_user}.
   * @var string
   */
  public function set_kind ($kind)
  {
    $this->_invalidate ();
    $this->_kind = $kind;
  }

  /**
   * @return PERMISSIONS
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_PERMISSIONS', 'webcore/sys/permissions.php');
    return new $class_name ($this->app);
  }

  /**
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    if (isset ($this->_kind))
    {
      $this->_calculated_restrictions [] = "perm.kind = '$this->_kind'";
    }
  }

  /**
   * @var string
   * @access private
   */
  protected $_kind;
}

/**
 * Return security {@link PERMISSIONS} objects for a {@link FOLDER}.
 * @package webcore
 * @subpackage db
 * @version 3.3.0
 * @since 2.2.1
 */
class FOLDER_PERMISSIONS_QUERY extends PERMISSIONS_QUERY
{
  /**
   * @param FOLDER $folder Retrieve permissions for this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);
    $this->_folder = $folder;
  }

  /**
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = "folder_id = {$this->_folder->permissions_id}";
  }
}

?>