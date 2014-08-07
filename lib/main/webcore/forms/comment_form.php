<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Update or create a {@link COMMENT}.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.2.1
 */
class COMMENT_FORM extends ATTACHMENT_HOST_FORM
{
  /**
   * @param FOLDER $folder Object belongs in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    // make title optional for comments

    $field = new INTEGER_FIELD ();
    $field->id = 'kind';
    $field->caption = 'Kind';
    $field->min_value = 0;
    $field->max_value = 255;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'parent_id';
    $field->caption = 'Parent Id';
    $field->min_value = 0;
    $field->visible = false;
    $this->add_field ($field);

    $field = $this->_fields ['title'];
    $field->required = false;
  }

  /**
   * Load initial properties from this comment.
   * @param COMMENT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('parent_id', $obj->parent_id);

    if (! isset ($obj->kind))
    {
      $this->set_value ('kind', 1);
    }
    else
    {
      $this->set_value ('kind', $obj->kind);
    }

    /* When updating a comment, do not publish by default */
    $this->set_value ('publication_state', History_item_silent);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('parent_id', read_var ('parent_id', 0));
    $this->set_value ('kind', 1);
  }

  /**
   * Called after fields are validated.
   * @param COMMENT $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->value_for ('title') && ! $this->value_for ('description'))
    {
      $this->record_error ('title', 'Please provide a title or description.');
    }
  }

  /**
   * Store the form's values to this comment.
   * @param COMMENT $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->kind = $this->value_for ('kind');
    $obj->parent_id = $this->value_for ('parent_id');

    parent::_store_to_object ($obj);
  }

  /**
   * Return true to use integrated captcha verification.
   * @return boolean
   */
  protected function _captcha_enabled ()
  {
    return $this->login->is_anonymous ();
  }
  
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $renderer->draw_text_line_row ('title');

    if ($this->_has_options ())
    {
      $this->_draw_options ($renderer);
    }

    $icons = $this->app->display_options->comment_icons ();
    if (sizeof ($icons))
    {
      $props = $renderer->make_list_properties ();
      $props->items_per_row = 8;
      $i = 0;

      foreach ($icons as $icon)
      {
        $i += 1;
        $props->add_item ($icon->icon_as_html (Fifteen_px), $i);
      }

      $renderer->draw_radio_group_row ('kind', $props);
    }

    $renderer->draw_text_box_row ('description', 'medium-height');

    if ($this->_captcha_enabled ())
    {
      $this->_draw_captcha_controls ($renderer);
    }

    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_comment;
}