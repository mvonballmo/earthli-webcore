<?php


/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package quiz
 * @subpackage forms
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

require_once ('webcore/forms/form.php');

class QUIZ_FORM extends ID_BASED_FORM
{
  var $name = 'quiz_form';
  var $button = 'Grade Answers';

  function QUIZ_FORM (&$quiz)
  {
    ID_BASED_FORM::ID_BASED_FORM ($quiz->context);

    $this->quiz =& $quiz;
    $questions =& $quiz->questions ();
    $field = null;

    foreach ($questions as $q)
    {
      unset ($field);  // kill the reference here
      $field = new INTEGER_FIELD ();
      $field->id = "question_$q->id";
      $field->title = "Question $q->id";
      $this->add_field ($field);
    }
  }

  /**
   * Read in values from the {@link $method} array.
   * @access private
   */
  function _load_from_request ()
  {
    parent::_load_from_request ();

    $this->num_answered = 0;
    $this->num_correct = 0;
    $questions =& $this->quiz->questions ();
    $this->num_questions = sizeof ($questions);

    foreach ($questions as $q)
    {
      $user_answer = $this->value_for ("question_$q->id");
      if ($user_answer != 0)
      {
        $this->num_answered++;
        if ($user_answer == $q->correct_answer)
          $this->num_correct++;
      }
    }
  }

  /**
   * Draw this form.
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $questions =& $this->quiz->questions ();
    $submitted = $this->submitted ();

    $munger = $this->context->html_text_formatter ();

?>
<p style="text-align: center" class="notes"><?php echo $munger->transform($this->quiz->description, $this->context); ?></p>
<?php
    if ($submitted)
    {
?>
<p style="text-align: center">You answered <span class="field"><?php echo $this->num_answered; ?></span> out of <span class="field"><?php echo $this->num_questions; ?></span> question(s).</p>
<p style="text-align: center">You got <span class="field"><?php echo $this->num_correct; ?></span> right and <span class="field"><?php echo $this->num_answered - $this->num_correct; ?></span> wrong.</p>
<p style="text-align: center">Correct answers are <span class="cell-highlight">highlighted</span>.</p>
<?php
      echo $this->quiz->results_description;
?>
<?php
    }
?>
<ol>
<?php
    foreach ($questions as $q)
    {
      $q_num = $q->id;
?>
  <li style="margin-bottom: 2em">
<p><?php echo $munger->transform($q->description, $this->context); ?></p>
  <?php
      if ($submitted)
      {
        $user_answer = $this->value_for ("question_$q->id");
        $correct_answer = $q->correct_answer;
  ?>
    <p><span class="field">[<?php
        if (! $user_answer)
          echo 'Not Answered';
        else if ($user_answer == $correct_answer)
          echo 'Right';
        else
          echo '<span class="error">Wrong</span>';
  ?>]</span></p>
  <?php
      }

      $answers = $q->answers;
      if (sizeof ($answers))
      {
        $props = $renderer->make_list_properties ();
        foreach ($answers as $a)
        {
          if ($submitted && ($a->id == $correct_answer))
            $title = "<span class=\"cell-highlight\">$a->description</span>";
          else
            $title = $a->description;

          $props->add_item ($title, $a->id);
        }

        echo $renderer->radio_group_as_html ("question_$q_num", $props);
?>
  </li>
<?php
      }
    }
    if (! $submitted)
    {
?>
  <p style="width: 20em; margin: auto">
    <?php echo $renderer->submit_button_as_html (); ?>
  </p>
</ol>
<?php
    }
  }
}

?>
