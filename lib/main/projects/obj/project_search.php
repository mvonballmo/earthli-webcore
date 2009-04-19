<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.7.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/obj/search.php');
require_once ('projects/forms/project_search_fields.php');

/**
 * A filter for {@link JOB}s.
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.7.0
 */
class JOB_SEARCH extends MULTI_ENTRY_SEARCH
{
  /**
   * @var string
   */
  public $type = 'job';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app, new SEARCH_JOB_FIELDS ($app));
  }
}

/**
 * A filter for {@link CHANGE}s.
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.7.0
 */
class CHANGE_SEARCH extends MULTI_ENTRY_SEARCH
{
  /**
   * @var string
   */
  public $type = 'change';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app, new SEARCH_CHANGE_FIELDS ($app));
  }
}

?>