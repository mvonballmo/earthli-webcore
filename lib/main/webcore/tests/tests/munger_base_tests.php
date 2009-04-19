<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.1.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/util/html_munger.php');
require_once ('webcore/util/plain_text_munger.php');
require_once ('webcore/util/munger_validator.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.1.0
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
      echo "<p>Congratulations! All [$this->_num_tests] tests have completed successfully.</p>";
    }
    else
    {
      echo "<p class=\"error\">Oops! You failed [$this->_num_errors] of [$this->_num_tests] tests.</p>";
    }

    echo $errors;
  }

  protected function _run_beta_tests ()
  {
    $this->_validator = new MUNGER_DEFAULT_TEXT_VALIDATOR ();
//    $this->_munger = new PLAIN_TEXT_MUNGER ();
    $this->_munger = new HTML_TEXT_MUNGER ();

//$s = <<<EOD
//EOD;

$s = "'Single-quoted', \"double-quoted\",
'<a href=\"test.html\">Test</a>', \"<a href=\"test.html\">Test</a>\",
'<box>Test</box>', \"<box>Test</box>\"
'Single-quoted',
\"double-quoted\",
\t'Single-quoted',
\t\"double-quoted\",
('Single-quoted'),
(\"double-quoted\"),
['Single-quoted'],
[\"double-quoted\"],
{'Single-quoted'},
{\"double-quoted\"},
<'Single-quoted'>,
<\"double-quoted\">,
='Single-quoted',
=\"double-quoted\",
5'9\",
'5'9\"',
\"5'9\"\",
\$5'000'000'000,00,
'\$5'000'000'000,00',
\"\$5'000'000'000,00\",
500'000,
'500'000',
\"500'000\",
don't
'don't',
\"don't\",
'\"nested\"'
\"'nested'\"
";

//$s = "
//'5'9\"',
//";

$result = <<<EORESULT
EORESULT;

    $this->_munger->max_visible_output_chars = 0;

//    $this->_generate_validation ($s);
//    $this->_generate_test ($s);
    $this->_run_munger_test ($s, $result);
//    $this->_run_validator_test ($s, 3);

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

  protected function _run_munger_test ($input, $expected)
  {
    $this->_num_tests += 1;

    $this->page->root_url = '/earthli/tests/';
    $output = $this->_munger->transform ($input, $this->page);
    
    if ((strcmp($output, $expected) != 0))
    {
      $this->_num_errors += 1;
      echo "<hr style=\"clear: both\">\n";
      echo "<div class=\"error\">Error detected</div>";
//      $this->_generate_test ($input);
    }
    elseif ($this->show_munger_stats)
    {
      echo "<hr style=\"clear: both\">\n";
    }
    elseif ($this->show_html_output)
    {
      echo $output;
    }

    if ((strcmp($output, $expected) != 0) || $this->show_munger_stats)
    {
      echo "<p>[" . get_class ($this->_munger) . "]: Force pars = [" . $this->_munger->force_paragraphs . "], Max chars = [" . $this->_munger->max_visible_output_chars . "], Break word = [" . $this->_munger->break_inside_word . "]</p>\n\n";
//      echo "<h4>Input</h4>\n\n";
//      echo "[$input]\n\n";
//      echo "<h4>Expected output</h4>\n\n[";
//      echo "$expected";
//      echo "]\n\n<h4>Actual output</h4>\n\n[";
//      echo "$output]\n\n";
            
      $expected_file = "d:\\expected.txt";
      $actual_file = "d:\\actual.txt";
      write_text_file($expected_file, $expected);
      write_text_file($actual_file, $output);
//      
    }
  }
}

?>