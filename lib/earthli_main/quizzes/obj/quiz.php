<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package quiz
 * @subpackage obj
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

require_once ('webcore/obj/unique_object.php');

class QUIZ extends UNIQUE_OBJECT
{
  function load (&$db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->description = $db->f ('description');
    $this->results_description = $db->f ('results_description');
  }

  function &questions ()
  {
    if (! isset ($this->_questions))
    {
      $this->_questions = array ();
        // set the array so the condition above is satisfied whether or not there are questions

      $this->db->query ("SELECT id, description, correct_answer FROM quiz_questions WHERE quiz_id = $this->id ORDER BY id");
      while ($this->db->next_record ())
      {
        $q = null;
        $q->id = $this->db->f ('id');
        $q->correct_answer = $this->db->f ('correct_answer');
        $q->description = $this->db->f ('description');
        $this->_questions [] = $q;
      }

      $i = 0;
      $c = sizeof ($this->_questions);
      while ($i < $c)
      {
        $q =& $this->_questions [$i];
        $this->db->query ("SELECT id, description FROM quiz_answers WHERE question_id = $q->id");
        while ($this->db->next_record ())
        {
          $ans = null;
          $ans->id = $this->db->f ('id');
          $ans->description = $this->db->f ('description');
          $q->answers [] = $ans;
        }
        $i++;
      }
    }

    return $this->_questions;
  }
}

?>