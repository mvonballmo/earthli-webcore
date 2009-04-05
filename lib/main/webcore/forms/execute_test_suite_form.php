<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.7.1
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
require_once ('webcore/forms/form.php');

/**
 * Offers a list of tests to execute from a {@link TEST_SUITE}.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.7.1
 */
class EXECUTE_TEST_SUITE_FORM extends FORM
{
  /**
   * @var string
   */
  public $button = 'Load';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/ship';
  
  /**
   * @param CONTEXT $context
   */
  public function EXECUTE_TEST_SUITE_FORM ($context)
  {
    FORM::FORM ($context);

    $field = new TEXT_FIELD ();
    $field->id = 'test_name';
    $field->title = 'Test';
    $field->min_value = 0;
    $this->add_field ($field);
  }
  
  /**
   * @param TEST_SUITE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $names = $obj->test_names ();
    if (! empty ($names)) 
      $this->set_value ('test_name', $names [0]);
  }
  
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    
    $props = $renderer->make_list_properties ();
    $names = $this->_object->test_names ();
    if (! empty ($names))
    {
      reset ($names);
      do
      {
        $props->add_item (current ($names), current ($names));
      }
      while (next ($names) !== false);
    }
    
    $renderer->draw_radio_group_row ('test_name', $props);
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    
    $renderer->finish ();
  }
}

?>