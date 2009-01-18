<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package polls
 * @subpackage sys
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

require_once ('webcore/sys/application.php');

class POLL_APPLICATION extends APPLICATION
{
  function POLL_APPLICATION (&$page)
  {
    APPLICATION::APPLICATION ($page);
    
    $this->set_path (Folder_name_application, '{pages}/polls/');    

    $this->table_names->questions = 'poll_questions';
    $this->table_names->answers = 'poll_answers';

    $this->storage->set_path ('/');
  }

  function poll_query ()
  {
    include_once ('polls/db/poll_query.php');
    return new POLL_QUERY ($this);
  }
}

?>