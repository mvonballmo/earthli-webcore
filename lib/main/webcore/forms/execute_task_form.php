<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
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
require_once ('webcore/forms/form.php');

/**
 * Show options for and execute {@link TASK}s.
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
 * @since 2.7.0
 */
class EXECUTE_TASK_FORM extends FORM
{
  /**
   * @var string
   */
  public $name = 'task_form';

  /**
   * @var string
   * @access private
   */
  public $method = 'get';

  /**
   * @var string
   */
  public $button = 'Run';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/ship';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TEXT_FIELD ();
    $field->id = 'test_name';
    $field->title = 'Test Name';
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'testing';
    $field->title = 'Test Run Only';
    $field->description = 'Runs through without actually changing anything; databases are untouched and mails are not sent.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'console';
    $field->title = 'Emulate Command-Line';
    $field->description = 'Configure the environment as if it was <strong>not</strong> running on an HTTP server.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'verbose';
    $field->title = 'Verbose';
    $field->description = 'Show database queries and other details. Use "debug" to show all messages.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'stop_on_error';
    $field->title = 'Stop on Error';
    $field->description = 'Abort execution of the task if an error occurs; turn this off to ignore spurious errors.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'database';
    $field->title = 'Show Database Output';
    $field->description = 'Shows queries executed against the database.';
    $this->add_field ($field);

    $field = $this->field_at ('debug');
    $field->visible = true;
    $field->description = 'Show all debugging output from all sub-systems. Similar to "verbose".';
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('verbose', false);
    $this->set_value ('testing', false);
    $this->set_value ('stop_on_error', true);
    $this->set_value ('debug', false);
    $this->set_value ('database', false);
    $this->set_value ('console', false);
  }

  /**
   * Load initial properties from this branch.
   * @param TASK $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('verbose', $obj->verbose);
    $this->set_value ('testing', $obj->testing);
    $this->set_value ('stop_on_error', $obj->stop_on_error);
    $this->set_value ('debug', $obj->log_debug);
    $this->set_value ('database', $obj->log_database);
    $this->set_value ('console', $obj->run_as_console);
  }

  /**
   * Execute the form.
   * @param TASK $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->verbose = $this->value_for ('verbose');
    $obj->testing = $this->value_for ('testing');
    $obj->stop_on_error = $this->value_for ('stop_on_error');
    $obj->log_debug = $this->value_for ('debug');
    $obj->log_database = $this->value_for ('database');
    $obj->run_as_console = $this->value_for ('console');
    $obj->owns_page = false;
    $obj->execute ();
  }

  /**
   * Add boolean fields to the check boxes.
   * @param FORM_LIST_PROPERTIES $props
   * @access private
   */
  protected function _add_boolean_options ($props)
  {
    $props->add_item ('testing', 1);
    $props->add_item ('stop_on_error', 1);
    $props->add_item ('verbose', 1);
    $props->add_item ('debug', 1);
    $props->add_item ('database', 1);
    $props->add_item ('console', 1);
  }

  /**
   * Draw task option controls.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->item_class = 'field';
    $this->_add_boolean_options ($props);
    $renderer->draw_check_boxes_row ('Options', $props);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $this->_draw_options ($renderer);

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->finish ();
  }
}

?>