<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.6.0
 * @since 2.5.0
 * @package webcore
 * @subpackage forms-core
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
require_once ('webcore/forms/form.php');

/**
 * Basic HTML forms handling.
 * Manages a list of {@link FIELD}s to validate and display controls.
 * @package webcore
 * @subpackage forms-core
 * @abstract
 */
abstract class PREVIEWABLE_FORM extends FORM
{
  /**
   * Is previewing enabled?
   * This class provides a framework for previewing, but disables it by default. Forms that implement
   * the abstract methods for previewing can enable it.
   * @var boolean
   */
  public $preview_enabled = false;

  /**
   * Show the previews before the form?
   * @var boolean
   */
  public $show_previews_first = true;

  /**
   * @param CONTEXT $context Attach to this object.
   */
  public function __construct ($context)
  {
    $this->_form_based_field_names [] = 'previewing';

    parent::__construct ($context);
  }

  /**
   * Is this form in preview mode?
   * @return boolean
   */
  public function previewing ()
  {
    $this->load_from_request ();
    return $this->value_for ($this->form_based_field_name ('previewing'));
  }

  /**
   * Add this object to be previewed in the form.
   * Uses {@link FORM_PREVIEW_SETTINGS} to store these settings.
   * @param STORABLE $obj
   * @param string $title
   * @param boolean $visible
   */
  public function add_preview ($obj, $title, $visible = true)
  {
    $settings = $this->_make_preview_settings ($obj);
    $settings->object = $obj;
    $settings->title = $title;
    $settings->visible = $visible;

    $this->_previews [] = $settings;
  }

  /**
   * Draw the form itself.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_form ($renderer)
  {
    if ($this->show_previews_first)
    {
      $this->_draw_previews ();
    }

    parent::_draw_form ($renderer);

    if (! $this->show_previews_first)
    {
      $this->_draw_previews ();
    }
  }

  /**
   * Displays objects in DHTML preview blocks.
   * Objects can be added to the preview list so that they are rendered before the form.
   */
  protected function _draw_previews ()
  {
    if (isset ($this->_previews) && sizeof ($this->_previews))
    {
      foreach ($this->_previews as $preview)
      {
        $preview->display ();
      }
    }
  }

  /**
   * Set the internal object of the form.
   * If the object is being created or cloned, it is not set by default.
   * @param object $obj
   * @param string $load_action
   * @access private
   */
  protected function _set_object ($obj, $load_action)
  {
    $this->_object = $obj;
  }

  /**
   * Executes the form for an object.
   * @param STORABLE $obj
   * @param string $load_action
   * @access private
   */
  protected function _process ($obj, $load_action)
  {
    if ($this->previewing ())
    {
      if (($load_action != Form_load_action_default) && $obj->exists ())
      {
        $this->_set_object ($obj, $load_action);
      }
      $this->validate ($obj);

      if (sizeof ($this->_errors) == 0)
      {
        /* Store the data loaded from the request to the object and add it as a preview. */

        $this->_store_to_object ($obj);
        $this->add_preview ($obj, $this->_preview_title ($obj));
      }
      
      $this->_apply_all_data ($obj, $load_action);
    }
    else
    {
      parent::_process ($obj, $load_action);
    }
  }

  /**
   * Store the form's values to this object.
   * @param STORABLE $obj
   * @access private
   * @abstract
   */
  protected abstract function _store_to_object ($obj);

  /**
   * Make a renderer for this form.
   * This is deferred to the {@link CONTEXT} on which the form is based, so that the user can customize
   * form rendering from one spot.
   * @return FORM_RENDERER
   */
  public function make_renderer ()
  {
    $Result = parent::make_renderer ();
    $Result->preview_enabled = $this->preview_enabled;
    return $Result;
  }

  /**
   * Return a preview for the given object.
   * @param STORABLE $obj
   * @return FORM_PREVIEW_SETTINGS
   * @access private
   * @abstract
   */
  protected abstract function _make_preview_settings ($obj);

  /**
   * Title for a previewed object.
   * @param object $obj
   * @return string
   * @abstract
   */
  protected abstract function _preview_title ($obj);

  /**
   * List of objects to render for preview.
   * If the object being edited is attached to another, it is attached as a 'preview' to the form.
   * If the user has elected to preview changes on the object being edited, it is also displayed as
   * a preview.
   * @var FORM_PREVIEW_SETTINGS[]
   * @see FORM_PREVIEW_SETTINGS
   */
  protected $_previews;
}

/**
 * Forms that use an id as foreign key.
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
 * @since 2.5.0
 * @abstract
 */
abstract class PREVIEWABLE_ID_BASED_FORM extends PREVIEWABLE_FORM
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new INTEGER_FIELD ();
    $field->id = 'id';
    $field->caption = 'ID';
    $field->min_value = 1;
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('id', read_var ('id'));
  }

  /**
   * Load form fields from this object.
   * @param object $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    if (isset ($obj->id))
    {
      $this->set_value ('id', $obj->id);
    }
  }
}

/**
 * Represents an object to preview in a form.
 * @version 3.6.0
 * @since 2.5.0
 * @package webcore
 * @subpackage forms-core
 * @abstract
 */
abstract class FORM_PREVIEW_SETTINGS extends WEBCORE_OBJECT
{
  /**
   * Title to display before the preview.
   * This is always display, even when the preview is hidden.
   * @var string
   */
  public $title;

  /**
   * Is the preview initially visible?
   * @var boolean
   */
  public $visible;

  /**
   * Display this object in the preview.
   * @var STORABLE
   */
  public $object;

  /**
   * @param FORM $form Attach to this object.
   */
  public function __construct ($form)
  {
    parent::__construct ($form->context);

    $this->_form = $form;
  }

  /**
   * Render the preview in the form.
   */
  public function display ()
  {
    $layer = $this->context->make_layer ('obj_' . uniqid (rand ()));
    $layer->visible = $this->visible;
?>
    <h3><?php $layer->draw_toggle (); echo ' ' . $this->title; ?></h3>
<div class="preview hidden-layer">
  <?php $layer->start (); ?>
  <div class="preview-body">
    <?php $this->_display (); ?>
  </div>
  <div class="clear-both"></div>
  <?php $layer->finish (); ?>
</div>
<?php
  }

  protected abstract function _display();
}

?>