<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
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
require_once ('webcore/forms/auditable_form.php');
require_once ('projects/obj/release_updater.php');

/**
 * Ships a {@link RELEASE}.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class SHIP_RELEASE_FORM extends AUDITABLE_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->show_previews_first = false;

    $field = new INTEGER_FIELD ();
    $field->id = 'state';
    $field->caption = 'Action';
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'sub_history_item_publication_state';
    $field->caption = 'Notifications';
    $field->add_value (History_item_needs_send);
    $field->add_value (History_item_silent);
    $this->add_field ($field);
  }

  /**
   * Store the form's values for this change.
   * @param RELEASE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);
    
    switch ($this->value_for ('state'))
    {
    case Testing:
      $obj->test (Defer_database_update);
      break;
    case Shipped:
      $obj->ship (Defer_database_update);
      break;
    case Locked:
      $obj->lock (Defer_database_update);
      break;
    }
  }

  /**
   * @param RELEASE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('state', Locked);
    $this->set_value ('sub_history_item_publication_state', History_item_silent);
    $this->set_value ('publication_state', History_item_default);
    $this->set_visible ('publication_state', false);
    $this->add_preview ($obj, 'Ship Release details');
  }

  /**
   * Delete the given object.
   * @param RELEASE $obj
   * @access private
   */
  public function commit ($obj)
  {
    parent::commit ($obj);

    include_once ('projects/obj/release_updater.php');
    $committer = new RELEASE_SHIPPER ($this->_object);
    $committer->apply ($this->value_for ('sub_history_item_publication_state'));
  }

  /**
   * Return a preview for the given object.
   * @param STORABLE $obj
   * @return FORM_PREVIEW_SETTINGS
   * @access private
   */
  protected function _make_preview_settings ($obj)
  {
    return new SHIP_RELEASE_PREVIEW_SETTINGS ($this->context);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_row ('', 'Are you sure you want to ship ' . $this->_object->title_as_link () . '?');

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item ($this->app->resolve_icon_as_html ('{app_icons}statuses/working', ' ', '16px') . ' Release to testing', Testing, 'Feature-complete internal release.');
    $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/ship', ' ', '16px') . ' Ship', Shipped, 'Jobs and changes can still be added and removed.');
    $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/locked', ' ', '16px') . ' Lock', Locked, 'Changes and jobs cannot be added or removed.');
    $renderer->draw_radio_group_row ('state', $props);

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item ('Publish release only', History_item_silent, 'Generate a single notification indicating that the release has shipped.');
    $props->add_item ('Publish all', History_item_needs_send, 'Generate individual notifications for affected jobs and changes.');
    $renderer->draw_radio_group_row ('sub_history_item_publication_state', $props);

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
}

/**
 * Represents an object to preview in a form.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class SHIP_RELEASE_PREVIEW_SETTINGS extends UPDATE_RELEASE_PREVIEW_SETTINGS
{
  /**
   * Render the preview for this object.
   */
  protected function _display ()
  {
    $class_name = $this->app->final_class_name ('RELEASE_SHIPPER', 'projects/obj/release_updater.php');

    /** @var RELEASE_SHIPPER $committer */
    $committer = new $class_name ($this->object);
?>
  <div class="text-flow">
  <p>Shipping this release will make the following changes (scroll down or hide this preview to accept).</p>
<?php
    $replacement = $committer->replacement_release ();
    if (isset ($replacement))
    {
      $text = 'These jobs are still open and will be <span class="field">moved</span> to ' . $replacement->title_as_link ();
    }
    else
    {
      $text = 'These jobs are still open and will be <span class="field">removed from </span> this release.';
    }

    $status = $committer->status_map->to_status ();

    $this->_draw_section ('change(s)', 'These changes will be <span class="field">assigned to</span> this release.', $committer->change_query ());
    $this->_draw_section ('open job(s)', $text, $committer->open_job_query ());
    $this->_draw_section ('closed job(s)', 'These jobs are closed and will be <span class="field">assigned to</span> this release.', $committer->closed_job_query ());
    $this->_draw_section ('job(s) will change status', 'These jobs will have their status changed to <span class="field">' . $status->icon_as_html () . ' ' . $status->title . '</span>.', $committer->remapped_job_query ());

?>
  </div>
<?php
    if (! $this->_objects_displayed)
    {
?>
  <p class="notes">There are no jobs or changes that will be affected by shipping this release.</p>
<?php
     }
  }
}

?>