<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.2.1
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
require_once ('webcore/cmd/commands.php');

class SELECTION_COMMANDS extends COMMANDS
{
  /**
   * @param FORM $form The form to which these commands belong.
   * @param string $field_name The name of the field to select/de-select.
   */
  public function __construct($form, $field_name)
  {
    $this->assert(isset ($form), "[form] cannot be empty.", '__construct', 'SELECTION_COMMANDS');

    parent::__construct($form->context);

    $ctrl_name = $form->js_name($field_name);

    $command = $this->app->make_command();
    $command->id = 'select_all';
    $command->caption = 'Select all';
    $command->link = '#';
    $command->executable = true;
    $command->on_click = "select_all ($ctrl_name)";
    $this->append($command);

    $command = $this->app->make_command();
    $command->id = 'select_none';
    $command->caption = 'Select none';
    $command->link = '#';
    $command->executable = true;
    $command->on_click = "select_none ($ctrl_name)";
    $this->append($command);
  }
}