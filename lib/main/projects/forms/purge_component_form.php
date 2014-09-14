<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.6.0
 * @since 1.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/forms/purge_form.php');

/**
 * Handles deletion of {@link COMPONENT}s.
 * @package projects
 * @subpackage forms
 * @version 3.6.0
 * @since 1.7.0
 */
class PURGE_COMPONENT_FORM extends PURGE_OBJECT_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * @param APPLICATION $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'sub_history_item_publication_state';
    $field->caption = 'Notifications';
    $field->add_value (History_item_needs_send);
    $field->add_value (History_item_silent);
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'replacement_component_id';
    $field->caption = 'Replace with';
    $field->description = 'Replace all references with this component.';
    $this->add_field ($field);
  }

  /**
   * Delete the given object.
   * @param COMPONENT $obj
   * @access private
   */
  public function commit ($obj)
  {
    $options = new COMPONENT_PURGE_OPTIONS ();
    $options->sub_history_item_publication_state = $this->value_for ('sub_history_item_publication_state');
    $options->replacement_component_id = $this->value_for ('replacement_component_id');
    $obj->purge ($options);
  }

  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('sub_history_item_publication_state', History_item_silent);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_row ('', 'Are you sure you want to purge ' . $this->_object->title_as_link () . '?');

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item ('Publish branch only', History_item_silent, 'Generate a single notification indicating that the branch was purged.');
    $props->add_item ('Publish all', History_item_needs_send, 'Generate individual notifications for affected jobs and changes.');
    $renderer->draw_radio_group_row ('sub_history_item_publication_state', $props);

    $folder = $this->_object->parent_folder ();
    $component_query = $folder->component_query ();
    $other_comps = $component_query->objects_at_ids ($this->_object->id, true);

    $props = $renderer->make_list_properties ();
    $props->add_item ('[None]', 0);
    foreach ($other_comps as $comp)
    {
      $props->add_item ($comp->title_as_plain_text (), $comp->id);
    }
    $renderer->draw_drop_down_row ('replacement_component_id', $props);

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->draw_text_row ('', '*Purging an branch removes all connections to it and permanently removes it from the database.', 'notes');

    $renderer->finish ();
  }
}

?>