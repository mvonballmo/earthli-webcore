<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.7.0
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
require_once ('webcore/forms/execute_task_form.php');

/**
 * Show options for and execute {@link PUBLISHER_TASK}s.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.7.0
 */
class EXECUTE_PUBLISHER_TASK_FORM extends EXECUTE_TASK_FORM
{
  public $button = 'Publish';

  /**
   * @var string
   */
  public $button_icon = '{icons}indicators/published';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'preview';
    $field->title = 'Preview Mails';
    $field->description = 'Show the generated mails in the task output. Use with care &mdash; output can be quite large.';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('preview', false);
  }

  /**
   * Load initial properties from this branch.
   * @param TASK $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('preview', $obj->preview);
  }

  /**
   * Execute the form.
   * @param TASK $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->preview = $this->value_for ('preview');
    parent::commit ($obj);
  }

  /**
   * Add boolean fields to the check boxes. 
   * @param FORM_LIST_PROPERTIES $props
   * @access private
   */
  protected function _add_boolean_options ($props)
  {
    parent::_add_boolean_options ($props);
    $props->add_item ('preview', 1);    
  }
}

?>