<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $id = read_var ('id');

  if ($id)
  {
    $query = $App->login->all_comment_query ();
    /** @var $obj COMMENT */
    $obj = $query->object_at_id ($id);
    $sub_type = Subscribe_comment;

    if ($obj)
    {
      $Page->location->add_folder_link($obj->parent_folder());
      $Page->location->add_object_link($obj->entry());
      $Page->location->add_object_link($obj, '', '{icons}/buttons/reply');
    }
  }

  include_once ('webcore/pages/subscribe_to_object.php');