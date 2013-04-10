<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.4.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/forms/multiple_object_printer_form.php');

/**
 * Print objects from a {@link PROJECT}.
 * @package projects
 * @subpackage forms
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_MULTIPLE_OBJECT_PRINTER_FORM extends MULTIPLE_OBJECT_PRINTER_FORM
{
  /**
   * @param PROJECT $folder Project from which to print objects.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_changes';
    $field->title = 'Show Changes';
    $field->description = 'Show changes under each selected job.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_files';
    $field->title = 'Show Files';
    $field->description = 'Show files associated with a change.';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('show_changes', 0);
    $this->set_value ('show_files', 0);
  }

  /**
   * Hook to allow descendants to use the default form layout, but add print options.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_print_options ($renderer)
  {
    parent::_draw_print_options ($renderer);
    $renderer->draw_separator ();
    $renderer->draw_check_box_row ('show_changes');
    $renderer->draw_check_box_row ('show_files');
  }
}

?>