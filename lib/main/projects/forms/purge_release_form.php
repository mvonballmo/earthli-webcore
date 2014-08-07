<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
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
require_once ('projects/obj/release_updater.php');

/**
 * Handles deletion of {@link RELEASE} objects.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class PURGE_RELEASE_FORM extends PURGE_OBJECT_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * Show the previews before the form?
   * @var boolean
   */
  public $show_previews_first = true;

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
  }

  /**
   * Delete the given object.
   * @param RELEASE $obj
   * @access private
   */
  public function commit ($obj)
  {
    $options = new PURGE_OPTIONS ();
    $options->sub_history_item_publication_state = $this->value_for ('sub_history_item_publication_state');
    $obj->purge ($options);
  }

  /**
   * @param RELEASE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('sub_history_item_publication_state', History_item_silent);
    $this->_previews = array();
    $this->add_preview ($obj, 'Purge Release details', true);
  }

  /**
   * Return a preview for the given object.
   * @param STORABLE $obj
   * @return FORM_PREVIEW_SETTINGS
   * @access private
   */
  protected function _make_preview_settings ($obj)
  {
    return new PURGE_RELEASE_PREVIEW_SETTINGS ($this);
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
    $props->add_item ('Publish branch only', History_item_silent, 'Generate a single notification indicating that the release was purged.');
    $props->add_item ('Publish all', History_item_needs_send, 'Generate individual notifications for affected jobs and changes.');
    $renderer->draw_radio_group_row ('sub_history_item_publication_state', $props);

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->draw_text_row ('', '*Purging an release removes all connections to it and permanently removes it from the database.', 'notes');

    $renderer->finish ();
  }
}

/**
 * Represents an object to preview in a form.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.9.0
 */
class PURGE_RELEASE_PREVIEW_SETTINGS extends UPDATE_RELEASE_PREVIEW_SETTINGS
{
  /**
   * Render the preview for this object.
   */
  protected function _display ()
  {
    $class_name = $this->app->final_class_name ('RELEASE_PURGER', 'projects/obj/release_updater.php');
    /** @var $committer RELEASE_PURGER */
    $committer = new $class_name ($this->object);
?>
  <p>Purging this release will make the following changes (scroll down or hide this preview to accept).</p>
<?php
    $replacement = $committer->replacement_release ();
    if (isset ($replacement))
    {
      $text = '<span class="field">moved to</span> ' . $replacement->title_as_link ();
    }
    else
    {
      $text = '<span class="field">removed from</span> this release.';
    }
      
    $this->_draw_section ('change(s)', 'These changes will be ' . $text, $this->object->change_query ());
    $this->_draw_section ('job(s)', 'These job will be ' . $text, $this->object->job_query ());
    
    if (! $this->_objects_displayed)
    {
?>
  <p class="notes">No jobs or changes will be affected by purging this release.</p>
<?php
    }
  }
}

?>