<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.7.1
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Manages supported files types.
 * This abstract interface can be implemented to read supported file type information
 * from various sources.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.7.1
 * @abstract
 */
abstract class TEST_SUITE extends WEBCORE_OBJECT
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->_load ();
  }
  
  /**
   * @return TEST_TASK
   * @param string $name
   */
  public function test_task_at_name ($name)
  {
    if (! empty ($this->_tests [$name]))
    {
      $class_file_name = $this->_tests [$name];
      include_once ($class_file_name);
      if (class_exists ($name))
      {
        return new $name ($this->page->make_application (Test_harness_application_id));
      }
    }
    
    return null;
  }
  
  /**
   * Return just the titles of the tests.
   * Used to display the available tests. Use {@link test_task_at_index()} to
   * get an instance of a test.
   * @return array[string]
   */
  public function test_names ()
  {
    if (! empty ($this->_tests))
    {
      return array_keys ($this->_tests);
    }

    return array ();
  }

  /**
   * Load tests.
   * @access private
   * @abstract
   */
  protected abstract function _load ();

  /**
   * Map of test classes to their file names.
   * @var array[string,string]
   * @access private
   */
  protected $_tests;
}

/**
 * Loads tests from an INI file.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.7.1
 */
class INI_TEST_SUITE extends TEST_SUITE
{
  /**
   * Load tests.
   * @access private
   * @abstract
   */
  protected function _load ()
  {
    $file_name = $this->context->config_file_name ('tests.ini');
    if ($file_name)
    {
      $config = parse_ini_file ($file_name, true);

      $this->_tests = read_array_index ($config, 'tests');
    }
  }
}

?>