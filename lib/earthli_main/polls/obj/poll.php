<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package polls
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
require_once ('polls/obj/answer.php');

class POLL extends UNIQUE_OBJECT
{
  var $id;
  
  var $bar_height = '.75em';
  
  var $display_text;
    // text of the question
  var $voted;
    // records whether the user voted yet or not
  var $longevity;
    // number of days until a revote is allowed
  var $closed;
    // set this to > 0 to indicate the there should be no more voting

  var $_db;
    // database object
  var $_storage;
    // storage object
  var $_answer_table_name;
    // cached table names, used for queries
  var $_answers;
    // array of answer objects
  var $_count;
    // total number of answers given (not answer objects)

  function display_text_as_html ()
  {
    return $this->_text_as_html ($this->display_text);
  }

  function load (&$db)
  {
    parent::load ($db);
    $this->display_text = $db->f ("display_text");
    $this->longevity = $db->f ("longevity");
    $this->closed = $db->f ("closed");
    $this->voted = $this->app->storage->value ("poll_$this->id");
  }

  function vote ($poll_answer)
  {
    if (! $this->voted)
    {
      $this->db->logged_query ("UPDATE $this->_answer_table SET count = count + 1 WHERE id=$poll_answer");
      $this->app->storage->expire_in_n_days ($this->longevity);
      $this->app->storage->set_value ("poll_$this->id", "1");
      $this->voted = 1;
    }
  }

  function count ()
  {
    $this->_load_answers ();
    return $this->_count;
  }

  function &answers ()
  {
    $this->_load_answers ();
    return $this->_answers;
  }

  function _load_answers ()
  {
    if (! isset ($this->_answers))
    {
      if ($this->voted)
        $this->db->logged_query ("SELECT * FROM $this->_answer_table WHERE question_id=$this->id ORDER BY count desc, id");
      else
        $this->db->logged_query ("SELECT * FROM $this->_answer_table WHERE question_id=$this->id ORDER BY id");

      $this->_count = 0;
      while ($this->db->next_record ())
      {
        $a = new ANSWER ($this->app);
        $a->load ($this->db);
        $this->_answers [] = $a;
        $this->_count += $a->count;
      }
    }
  }

  /**
   * Returns true if the poll is still open.
   * Specifically, if the logged-in user has not voted in the last {@link
   * $longevity} days and it is not {@link $closed}, this returns true.
   */
  function can_vote ()
  {
    return ! $this->voted && ! $this->closed;
  }
  
  /**
   * Returns the form for displaying the poll.
   * {@link FORM::process_existing()} has already been called, but the user is
   * free to decide where and when to display the form.
   * @return POLL_FORM
   */
  function form ()
  {
    $class_name = $this->context->final_class_name ('POLL_FORM', 'polls/forms/poll_form.php');
    $Result = new $class_name ($this->context);
    $Result->process_existing ($this);
    return $Result;
  }
  
  function display_results ()
  {
?>
<div style="margin-bottom: 1em">
  <?php echo $this->display_text_as_html (); ?>
</div>
<div style="min-width: 100px">
  <?php
    $this->_load_answers ();
    
    foreach ($this->_answers as $a)
    {
      if ($this->_count)
        $width = intval ($a->count * 100 / $this->_count);
       else
        $width = 0;
  ?>
  <div class="detail">
    <?php echo $a->display_text; ?>
  </div>
  <div class="graph-background" style="margin-top: .2em; height: <?php echo $this->bar_height; ?>">
    <div class="graph-foreground" style="height: <?php echo $this->bar_height; ?>; width: <?php echo $width; ?>%">
    </div>
  </div>
  <div class="notes" style="text-align: right; margin-top: .1em; margin-bottom: .5em">
    (<?php echo $a->count; ?> votes)
  </div>
  <?php
    }
  ?>
</div>
<div class="notes" style="text-align: right">
  <?php echo $this->count (); ?> total votes
</div>
<?php
  }

  /**
   * A string representing the entire title of the object.
   * @return string
   */
  function raw_title ()
  {
    return 'Results';
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  function page_name ()
  {
    return 'index.php';
  }
}
?>