<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.0
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
require_once ('webcore/forms/execute_task_form.php');

/**
 * Show options for and execute {@link MIGRATOR_TASK}s.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.0
 */
class EXECUTE_MIGRATOR_TASK_FORM extends EXECUTE_TASK_FORM
{
  var $button = 'Upgrade';
  /**
   * @var string
   */
  var $button_icon = '{icons}buttons/upgrade';

  /**
   * @param FRAMEWORK_INFO &$app Main application.
   */
  function EXECUTE_MIGRATOR_TASK_FORM (&$app)
  {
    EXECUTE_TASK_FORM::EXECUTE_TASK_FORM ($app);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'ignore_from_version';
    $field->title = 'Ignore Version in Database';
    $field->description = 'Migrate regardless of whether the database has the correct version. Use only if you know what you\'re doing.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'framework';
    $field->visible = FALSE;
    $this->add_field ($field);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('ignore_from_version', FALSE);
    $this->set_value ('framework', read_var ('framework'));
  }

  /**
   * Load initial properties from this task.
   * @param MIGRATOR_TASK &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('ignore_from_version', $obj->ignore_from_version);
    $this->set_value ('framework', read_var ('framework'));
    $this->set_enabled ('ignore_from_version', $obj->info->exists ());
  }

  /**
   * Execute the form.
   * @param TASK &$obj
   * @access private
   */
  function commit (&$obj)
  {
    $obj->ignore_from_version = $this->value_for ('ignore_from_version');
    parent::commit ($obj);
  }

  /**
   * Add boolean fields to the check boxes. 
   * @param FORM_LIST_PROPERTIES &$props
   * @access private
   */
  function _add_boolean_options (&$props)
  {
    $props->add_item ('testing', 1);    
    $props->add_item ('stop_on_error', 1);    
    $props->add_item ('verbose', 1);    
    $props->add_item ('ignore_from_version', 1);    
  }

  /**
   * Draw task option controls.
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_options (&$renderer)
  {
    if (! $this->_object->info->exists ())
    {
      $warning_message = 'Since there is no version in the database, the migrator for <span class="field">' . $this->_object->info->database_version . '</span> will be used and "Ignore Version in Database" is required.';
      $renderer->draw_text_row ($this->context->resolve_icon_as_html ('{icons}indicators/warning', 'Warning', '32px'), $warning_message, 'caution');
      $renderer->draw_separator ();
    }
    parent::_draw_options ($renderer);
  }
}

?>