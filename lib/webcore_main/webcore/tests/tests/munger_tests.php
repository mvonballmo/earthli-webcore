<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/init.php');
require_once ('webcore/tests/tests/munger_base_tests.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class MUNGER_TEST_TASK extends MUNGER_BASE_TEST_TASK
{
  function _run_tests ()
  {
    parent::_run_tests ();
    $this->_run_token_tests ();
    $this->_run_attribute_tests ();
    $this->_run_validator_tests ();
    $this->_run_plain_text_tests ();
    $this->_run_html_tests ();
  }

  function _run_token_tests ()
  {
    $this->_test_tokens ('<a href="/earthli/index.php">home</a> <p>This is the home page.</p> Hello. I think 8 > 5 && 5 < 8.', 8);
    $this->_test_tokens ('<a href="/earthli/index.php">home</a> <p>This is the home page.</p> Hello. I think 8 > 5 <a href="whatever.php">&&</a> 5 < 8.', 12);
    $this->_test_tokens ('<<<<<', 3);
    $this->_test_tokens ('<<f<<s', 4);
    $this->_test_tokens ('<<<<<<f<<s<<G<<4<<<e><', 13);
    $this->_test_tokens ('<<<f><<s', 4);
    $this->_test_tokens ("<ul>\n<ul>\n<a href=\"/earthli/index.php\">home</a>\n</ul>\n <p>This is the home page.</p> Hello. I think 8 > 5 <a href=\"whatever.php\">&&</a> 5 < 8.\n</ul>\n\n", 20);
    $this->_test_tokens ("", 1);
    $this->_test_tokens ("This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page. This is the home page.", 1);
    $this->_test_tokens ("This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ). This is the home page (where 8 < 5 ).", 1);
    $this->_test_tokens ("This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ). This is the home page (where 8<5 ).", 1);
  }

  function _run_attribute_tests ()
  {
    $this->_test_attributes ('<a href="/earthli/index.php">', 1);
    $this->_test_attributes ('<a href="/earthli/index.php" title=" This is my title. ">', 2);
    $this->_test_attributes ('<a    href="/earthli/index.php"    >', 1);
    $this->_test_attributes ("<a\nhref=\"/earthli/index.php\"\ntitle=\"This is the &quot;title&quot;...\n\n\t\t\"\n>", 2);
  }

  function _run_validator_tests ()
  {
    $this->_validator = new MUNGER_DEFAULT_TEXT_VALIDATOR ();
    $this->_run_validator_test ("<span class=\"notes\">Test</span>", 0);
    $this->_run_validator_test ("</div>", 1);
    $this->_run_validator_test ("</img>", 1);
    $this->_run_validator_test ("Try <div><n>Bla</div> and no error occurs.", 3);
    $this->_run_validator_test ("<div class=\"notes\" align=\"center\" width=\"100px\">Test</span>", 2);
    $this->_run_validator_test ("<div class=\"notes\" align=\"center\" width=\"100px\">Test</div>", 0);
    $this->_run_validator_test ("\r\nTesting headings.\r\n\r\n\r\nTesting headings.\r\n\r\n<h level=\"1\"><b style=\"width: 50px\">H1 heading</h>\r\n\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\n\r\n<h>Normal title</h></p>\r\n\r\nHere's some text under this heading (level 3).\r\n\r\n<h level=\"high\">Bogus heading</h>\r\nHere's some text under this heading\r\nHere's some text under this heading\r\nHere's some text under this heading\r\n\r\n<h level=\"1\"><b styl=\"width: 50px\">H1 heading</h>\r\n\r\nHere's some text under this heading\r\n\r\n<h>Normal title</h></p>\r\n\r\nHere's some text under this heading (level 3).\r\n\r\n<h level=\"high\">Bogus heading</h>\r\n\r\n\r\nHere's some text under this heading (level 3).\r\nHere's some text under this heading (level 3).\r\nHere's some text under this heading (level 3).\r\nHere's some text under this heading (level 3).\r\nHere's some text under this heading (level 3).\r\n\r\n\r\n<h>Multi-line\r\nheading</h2>\r\n\r\n", 8);
    $this->_run_validator_test ("<bq quote_style=\"none\">Content</bq>\r\n<bq quote_style=\"single\">Content</bq>\r\n<bq quote_style=\"multiple\">Content</bq>", 0);
  }

  function _run_plain_text_tests ()
  {
    include('webcore/tests/tests/plain_text_munger_tests.php');
  }

  function _run_html_tests ()
  {
    include('webcore/tests/tests/html_munger_tests.php');
  }
}

?>