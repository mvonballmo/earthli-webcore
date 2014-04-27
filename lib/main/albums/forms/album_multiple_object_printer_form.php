<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/forms/multiple_object_printer_form.php');

/**
 * Print objects from an {@link ALBUM}.
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class ALBUM_MULTIPLE_OBJECT_PRINTER_FORM extends MULTIPLE_OBJECT_PRINTER_FORM
{
  /**
   * @param ALBUM $folder Print objects from this album.
   */
  public function __construct ($folder)
  {
    parent::__construct($folder);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_pictures';
    $field->caption = 'Pictures';
    $field->description = 'Show pictures with journal entries.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'resize_pictures';
    $field->caption = '';
    $field->description = 'Resize pictures according to album rules.';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('show_pictures', 1);
    $this->set_value ('resize_pictures', 1);
    $this->set_value ('show_comments', Print_comments_off);
  }

  /**
   * Hook to allow descendants to use the default form layout, but add print options.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_print_options ($renderer)
  {
    parent::_draw_print_options ($renderer);
    $renderer->draw_check_box_row ('show_pictures');
    $renderer->draw_check_box_row ('resize_pictures');
  }
}
?>