<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/util/html_munger.php');
require_once ('webcore/util/plain_text_munger.php');
require_once ('webcore/util/munger_validator.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */
class MUNGER_BASE_TEST_TASK extends TEST_TASK
{
  /**
   * Shows generated HTML output, if set.
   * Allows you to validate the generated content in an HTML validator.
   * @var boolean
   */
  public $show_html_output = false;

  /**
   * Show white space for token tests.
   * @var boolean
   */
  public $show_white_space = false;

  /**
   * Show token output for token tests.
   * @var boolean
   */
  public $show_tokens = false;

  /**
   * Show output for attribute tests.
   * @var boolean
   */
  public $show_attributes = false;

  /**
   * Show statistics for the validator tests.
   * @var boolean
   */
  public $show_validator_stats = false;

  /**
   * Show statistics for the stripper tests.
   * @var boolean
   */
  public $show_stripper_stats = false;

  /**
   * Show statistics for the munger tests.
   * @var boolean
   */
  public $show_munger_stats = false;

  /**
   * @param CONTEXT $context Pass in an {@link APPLICATION} object.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->show_html_output = read_var ('show_html_output', false);
    $this->show_white_space = read_var ('show_white_space', false);
    $this->show_tokens = read_var ('show_tokens', false);
    $this->attributes = read_var ('attributes', false);
    $this->show_validator_stats = read_var ('show_validator_stats', false);
    $this->show_munger_stats = read_var ('show_munger_stats', false);
  }

  protected function _run_tests ()
  {
//    $this->_run_beta_tests ();
  }

  protected function _execute ()
  {
    $this->_num_errors = 0;
    $this->_num_tests = 0;
    ob_start ();
//      $this->_run_beta_tests ();
      $this->_run_tests ();
      $errors = ob_get_contents ();
    ob_end_clean ();

    if (! $this->_num_errors)
    {
      echo "<p>" . 
        $this->context->resolve_icon_as_html("{icons}/indicators/error", Twenty_px, "info") .
        " Congratulations! All [$this->_num_tests] tests have completed successfully.</p>";
    }
    elseif ($this->_num_ignored == $this->_num_errors)
    {
      echo "<p>" . 
        $this->context->resolve_icon_as_html("{icons}/indicators/warning", Twenty_px, "info") .
        " Congratulations! You passed [$this->_num_tests] tests; [$this->_num_ignored] errors were ignored.</p>";
    }
    else
    {
      echo "<p class=\"error\">" . 
        $this->context->resolve_icon_as_html("{icons}/indicators/error", Twenty_px, "error") .
        " You failed [$this->_num_errors] of [$this->_num_tests] tests; [$this->_num_ignored] errors were ignored.</p>";
    }

    echo $errors;
  }

  public function run_beta_tests ()
  {
    $this->_munger = new PLAIN_TEXT_MUNGER ();
    $this->_munger->right_margin = 80;

$this->_run_munger_test (
  "<dl dt_class=\"field\">\r
Afghanistan\r
<div>\r
This is a picture-perfect example of a country that benefitted from the freedom that America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as an astounding success; <a href=\"http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html\" title=\"Afghanistan's election: U.S., allies must fulfill long-term commitment\">Afghanistan's election</a> gives a brief run-down of the country's situation:
</div>
",
  "Afghanistan

   This is a picture-perfect example of a country that benefitted from the
   freedom that America oozes from it's pores as it strides through the
   benighted world, bestowing freedom and compassion with a kind paternal hand.
   Bush touted their recent elections as an astounding success; \"Afghanistan's
   election\"
   <http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html>
   (Afghanistan's election: U.S., allies must fulfill long-term commitment)
   gives a brief run-down of the country's situation:
   
"
);
    
    
  }

  protected function _generate_test ($input)
  {
    $output = $this->_munger->transform ($input);

    $input = str_replace ('"', '\"', $input);
    $input = str_replace ("\t", '\t', $input);
    $input = str_replace ("\r", '\r', $input);
    $input = str_replace ("\n", '\n', $input);
    $output = str_replace ('"', '\"', $output);
    $output = str_replace ("\t", '\t', $output);
    $output = str_replace ("\r", '\r', $output);
    $output = str_replace ("\n", '\n', $output);
    echo '$this->_munger->max_visible_output_chars = ' . $this->_munger->max_visible_output_chars . ";\n";
    echo
"\$this->_run_munger_test (
\"$input\",
\"$output\"
);
";
  }

  protected function _generate_validation ($input)
  {
    $this->_validator->validate ($input);
    $input = str_replace ('"', '\"', $input);
    $input = str_replace ("\t", '\t', $input);
    $input = str_replace ("\r", '\r', $input);
    $input = str_replace ("\n", '\n', $input);
    $expected = sizeof ($this->_validator->errors);
    echo "\$this->_run_validator_test (\"$input\", $expected);\n";
  }

  /**
   * Initialize any loggers needed for the process.
   * @access private
   */
  protected function _set_up_logging ()
  {
    parent::_set_up_logging ();
    $this->_add_log_channel (Msg_channel_munger);
  }

  protected function _test_tokens ($input, $expected_token_count)
  {
    $this->_num_tests += 1;

    $munger = new MUNGER_TOKENIZER ();
    $munger->set_input ($input);
    $count = 0;
    $token_content = '';

    while ($munger->tokens_available ())
    {
      $count += 1;
      $munger->read_next_token ();
      $token = $munger->current_token ();
      if ($this->show_tokens)
      {
        switch ($token->type)
        {
        case Munger_token_text: $type = 'Text'; break;
        case Munger_token_open_tag: $type = 'Open'; break;
        case Munger_token_close_tag: $type = 'Close'; break;
        }

        if ($this->show_white_space)
        {
          $data = str_replace ("\t", "<span class=\"disabled\">\xBB</span>\t", str_replace ("\n", "<span class=\"disabled\">\xAC</span>\n", htmlspecialchars ($token->data ())));
        }
        else
        {
          $data = htmlspecialchars ($token->data ());
        }
        $size = $token->size;

        $token_content .= "[$type][$size]: $data<br>\n";
      }
    }

    if (($count != $expected_token_count) || ($this->show_tokens))
    {
      echo "<hr style=\"clear: both\">\n";
    }

    if (($count == $expected_token_count) && ($this->show_tokens))
    {
      echo "<h4>Input</h4>";
      echo "<p>" . htmlspecialchars ($input) . "</p>\n";
    }

    if ($count != $expected_token_count)
    {
      $this->_num_errors += 1;
      echo "<div class=\"error\">Error detected</div>";
      echo "<h4>Input</h4>";
      echo "<p>" . htmlspecialchars ($input) . "</p>\n";
      echo "<p>Expected [$expected_token_count] tokens (got [$count]).</p>\n";
    }

    if (($count != $expected_token_count) || ($this->show_tokens))
    {
      echo $token_content;
    }
  }

  protected function _test_attributes ($input, $expected_prop_count)
  {
    $this->_num_tests += 1;

    $munger = new MUNGER_TOKENIZER ();
    $munger->set_input ($input);

    while ($munger->tokens_available ())
    {
      $munger->read_next_token ();
      $token = $munger->current_token ();

      if (! $token->type == Munger_token_start_tag)
      {
        $this->_num_errors += 1;
        echo "<hr style=\"clear: both\">\n";
        echo "<div class=\"error\">Error detected</div>";
        echo "<h4>Input</h4>";
        echo "<p>$input</p>\n";
        echo "<p>Expected single tag.</p>\n";
      }

      $attrs = $token->attributes ();
      $count = sizeof ($attrs);

      if ($this->show_attributes)
      {
        foreach ($attrs as $key => $value)
        {
          echo "<p>[$key]=[" . htmlspecialchars ($value) . "]</p>";
        }
      }

      if ($count != $expected_prop_count)
      {
        $this->_num_errors += 1;
        echo "<hr style=\"clear: both\">\n";
        echo "<div class=\"error\">Error detected</div>";
        echo "<h4>Input</h4>";
        echo "$input\n";
        echo "<p>Expected [$expected_prop_count] attributes (got [$count]).</p>\n";
      }
    }
  }

  protected function _run_validator_test ($input, $errors_expected)
  {
    $this->_num_tests += 1;

    $this->_validator->validate ($input);

    $errors_generated = sizeof ($this->_validator->errors);
    if ($errors_generated != $errors_expected)
    {
      $this->_num_errors += 1;
      echo "<hr style=\"clear: both\">\n";
      echo "<div class=\"error\">Error detected</div>";
    }
    elseif ($this->show_validator_stats)
    {
      echo "<hr style=\"clear: both\">\n";
    }

    if (($errors_generated != $errors_expected) || $this->show_validator_stats)
    {
      echo "<h4>Input</h4>";
      echo "$input\n";
      echo "<h4>$errors_generated errors generated, expected $errors_expected errors</h4>";
      if ($errors_generated)
      {
        foreach ($this->_validator->errors as $error)
        {
          echo "$error->message<br>\n";
        }
      }
    }
  }
  
  protected function _run_stripper_test ($input, $expected_output)
  {
    $this->_num_tests += 1;

    $output = $this->_stripper->strip ($input);
    
    if (strcmp($output, $expected_output) != 0)
    {
      $this->_num_errors += 1;
      echo "<hr style=\"clear: both\">\n";
      echo "<div class=\"error\">Error detected</div>";
    }
    elseif ($this->show_stripper_stats)
    {
      echo "<hr style=\"clear: both\">\n";
    }

    if ((strcmp($output, $expected_output) != 0) || $this->show_stripper_stats)
    {
      echo "<h4>Input</h4>\n\n";
      echo "[$input]\n\n";
      echo "<h4>Expected output</h4>\n\n[";
      echo $expected_output;
      echo "]\n\n<h4>Actual output</h4>\n\n[";
      echo "$output]\n\n";
    }
  }
  
  protected function _run_munger_test ($input, $expected, $ignore = false)
  {
    $this->_num_tests += 1;

    $this->page->root_url = '/earthli/tests/';
    $output = $this->_munger->transform ($input, $this->page);
    
    if ((strcmp($output, $expected) != 0))
    {
      $this->_num_errors += 1;
      if ($ignore)
      {
        $this->_num_ignored += 1;
      }
      else 
      {
        echo "<hr style=\"clear: both\">\n";
        echo "<div class=\"error\">Error detected</div>";
//        $this->_generate_test ($input);
      }
    }
    elseif ($this->show_munger_stats)
    {
      echo "<hr style=\"clear: both\">\n";
    }
    elseif ($this->show_html_output)
    {
      echo $output;
    }

    if (((strcmp($output, $expected) != 0) || $this->show_munger_stats) && !$ignore)
    {
      echo "<p>[" . get_class ($this->_munger) . "]: Force pars = [" . $this->_munger->force_paragraphs . "], Max chars = [" . $this->_munger->max_visible_output_chars . "], Break word = [" . $this->_munger->break_inside_word . "]</p>\n\n";
      echo "<h4>Input</h4>\n\n";
      echo "[$input]\n\n";
      echo "<h4>Expected output</h4>\n\n[";
      echo $expected;
      echo "]\n\n<h4>Actual output</h4>\n\n[";
      echo "$output]\n\n";
            
      $expected_file = "d:\\expected.txt";
      $actual_file = "d:\\actual.txt";
      write_text_file($expected_file, $expected);
      write_text_file($actual_file, $output);
      
      if ($this->stop_on_error)
      {
        $this->_abort("Halting on error");
      }
    }
  }
  
  /**
   * Number of errors encountered during the last testing run.
   *
   * @var integer
   */
  protected $_num_errors;
  
  /**
   * Number of tests in the last testing run.
   *
   * @var integer
   */
  protected $_num_tests;

  /**
   * Number of errors ignored during the last testing run.
   *
   * @var integer
   */
  protected $_num_ignored;
}

?>