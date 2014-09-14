<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('webcore/forms/content_object_form.php');

/**
 * Base form for {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class OBJECT_IN_FOLDER_FORM extends CONTENT_OBJECT_FORM
{
  var $description_control_css_class = '';

  /**
   * @param FOLDER $folder Object is created/edited in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;

    $field = new BOOLEAN_FIELD ();
    $field->id = 'is_visible';
    $field->caption = 'Visible';
    $field->description = 'Show this item to non-admin users.';
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);

    if ($this->visible ('is_visible'))
    {
      // if this field is displayed, then use it's value.

      if ($this->value_for ('is_visible'))
      {
        $obj->show (false);
      }
      else
      {
        $obj->hide (false);
      }
    }
  }

  /**
   * Load initial properties from this object.
   * @param OBJECT_IN_FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('is_visible', $obj->visible ());
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('is_visible', true);
    $this->set_visible ('is_visible', $this->_visible_allowed ());
  }

  /**
   * Configure the history item's properties.
   * Prevents history items for invisible objects from queuing when the user hasn't expressed a preference.
   * 
   * @param OBJECT_IN_FOLDER $obj The object to be stored.
   * @param HISTORY_ITEM $history_item
   * @access private
   */
  protected function _adjust_history_item ($obj, $history_item)
  {
    parent::_adjust_history_item ($obj, $history_item);
    $pub_state = $this->value_for ('publication_state');
    if ($pub_state == History_item_default)
    {
      if ($obj->visible())
      {
        $history_item->publication_state = History_item_queued;
      }
      else 
      {
        $history_item->publication_state = History_item_silent;
      }
    }
  }

  /**
   * Can the current user see invisible objects of this type?
   * @return boolean
   * @access private
   */
  protected function _visible_allowed ()
  {
    return $this->login->is_allowed ($this->_privilege_set, Privilege_view_hidden, $this->_folder);
  }

  /**
   * Return true if there are options to be drawn by {@link _draw_options()}.
   * @return boolean 
   * @access private
   */
  protected function _has_options ()
  {
    return $this->visible ('is_visible'); 
  }
    
  /**
   * Draw options between the title and description.
   * This is a good place for controls that add content to the description
   * (toolbars, etc.).
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    $renderer->draw_check_box_row ('is_visible');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_text_box_row ('description', $this->description_control_css_class);
    if ($this->_has_options ())
    {
      $this->_draw_options ($renderer);
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
  protected $_privilege_set;

  /**
   * Folder containing the object being edited/created.
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}

/**
 * Base form for {@link ATTACHMENT_HOST} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.7.1
 */
class ATTACHMENT_HOST_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param FOLDER $folder Object is created/edited in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new TEXT_FIELD ();
    $field->id = 'attachments';
    $field->caption = 'Attachments';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'alignments';
    $field->caption = 'Alignments';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'sizes';
    $field->caption = 'Sizes';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'caption_modes';
    $field->caption = 'Caption Modes';
    $this->add_field ($field);
  }
  
  /**
   * @see ATTACHMENT
   * @return ATTACHMENT[]
   * @access private
   */
  protected function _attachments ()
  {
    if (! isset ($this->_attachments))
    {
      if ($this->object_exists ())
      {
        $attachment_query = $this->_object->attachment_query ();
        $this->_attachments = $attachment_query->objects ();
      }
      else
      {
        $this->_attachments = array ();
      }
    }
    return $this->_attachments;
  }
  
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('caption_modes', 'caption');
  }
  
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_attachment_selector ($renderer)
  {
    $attachments = $this->_attachments ();
    if (sizeof ($attachments))
    {
      $props = $renderer->make_list_properties ();
      $props->css_class = 'small';
      foreach ($attachments as $att)
      {
        $props->add_item ($att->title_as_plain_text (), $att->file_name);
      }
      $attachment_control = $renderer->drop_down_as_html ('attachments', $props);
      
      $props = $renderer->make_list_properties ();
      $props->add_item ('None', 'none');
      $props->add_item ('Left', 'left');
      $props->add_item ('Left (alone)', 'left-column');
      $props->add_item ('Center', 'center');
      $props->add_item ('Right', 'right');
      $props->add_item ('Right (alone)', 'right-column');
      $props->css_class = 'small';
      $alignments = $renderer->drop_down_as_html ('alignments', $props);
      
      $props = $renderer->make_list_properties ();
      $props->add_item ('Thumbnail', 'thumbnail');
      $props->add_item ('25%', '25');
      $props->add_item ('50%', '50');
      $props->add_item ('75%', '75');
      $props->add_item ('Full-size', '100');
      $props->css_class = 'small';
      $sizes = $renderer->drop_down_as_html ('sizes', $props);
        
      $props = $renderer->make_list_properties ();
      $props->add_item ('No text', 'none');
      $props->add_item ('Caption', 'caption');
      $props->add_item ('Tooltip', 'tooltip');
      $props->add_item ('Both', 'both');
      $props->css_class = 'small';
      $caption_modes = $renderer->drop_down_as_html ('caption_modes', $props);
      
      $renderer->start_row ('Attachments');
        echo $attachment_control;
        echo $alignments;
        echo $sizes;
        echo $caption_modes;
        echo $renderer->javascript_button_as_html ('Add', 'on_insert_attachment ()');
      $renderer->finish_row ();
    }
  }
  
  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
    
    $atts = $this->_attachments ();
    if (sizeof ($atts))
    {
?>
    var attachments = Array ();
    var types = Array ();
<?php
      foreach ($atts as $att)
      {
        $f = $att->title_formatter ();
        $f->max_visible_output_chars = 100;
        echo '    attachments ["' . $att->file_name . '"] = "' . str_replace ('"', '\\"', $att->title_as_plain_text ($f)) . "\";\n";
        echo '    types["' . $att->file_name . '"] = "' . $att->mime_type . "\";\n";
      }
    }
?>

    function on_insert_attachment ()
    {
      f = <?php echo $this->js_form_name (); ?>;
      caption_text = attachments [f.attachments.value].replace (/"/g, "'");
      alignment = f.alignments.value;
      size = f.sizes.value;
      caption_mode = f.caption_modes.value;
      
      text = '';
      if (caption_mode == "caption")
      {
        text = ' caption="' + caption_text + '"';
      }
      else if (caption_mode == "tooltip")
      {
        text = ' title="' + caption_text + '"';
      }
      else if (caption_mode == "both")
      {
        text = ' caption="' + caption_text + '" title="' + caption_text + '"';
      }
      
      if (size == "thumbnail")
      {
        text_to_insert = '<img attachment="' + f.attachments.value + '" align="' + alignment + '"' + text + '>';
      }
      else
      {
        if (size == "100")
        {
          text_to_insert = '<img src="{att_link}' + f.attachments.value + '" align="' + alignment + '"' + text + '>';
        }
        else   
        {   
          text_to_insert = '<img src="{att_link}' + f.attachments.value + '" href="{att_link}' + f.attachments.value + '" align="' + alignment + '"' + text + ' scale="' + size + '%">';
        }
      }
      insert_text (<?php echo $this->js_form_name (); ?>.description, text_to_insert);
    }
<?php
  }
  
  /**
   * Return true if there are options to be drawn by {@link _draw_options()}.
   * @return boolean 
   * @access private
   */
  protected function _has_options ()
  {
    return parent::_has_options () || ($this->object_exists () && sizeof ($this->_attachments ())); 
  }
    
  /**
   * Draw options between the title and description.
   * This is a good place for controls that add content to the description
   * (toolbars, etc.).
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    $this->_draw_attachment_selector ($renderer);
    parent::_draw_options ($renderer);
  }
}

/**
 * Base form for {@link ENTRY} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class ENTRY_FORM extends ATTACHMENT_HOST_FORM
{
  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_entry;
}

/**
 * Base form for {@link DRAFTABLE_ENTRY} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class DRAFTABLE_ENTRY_FORM extends ENTRY_FORM
{
  /**
   * @param FOLDER $folder Object is created/edited in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'draft';
    $field->caption = 'Draft';
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'quick_save';
    $field->caption = 'Quick Save';
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * New object is being created, load default values.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('draft', true);
    $this->set_value ('is_visible', true);
    $this->set_visible ('is_visible', false);
  }

  /**
   * Load initial properties from the object.
   * @param DRAFTABLE_ENTRY $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('draft', $obj->unpublished () || $this->cloning ());
    if ($obj->unpublished ())
    {
      $this->set_value ('is_visible', true);
      $this->set_visible ('is_visible', false);
    }
    else
    {
      $this->set_visible ('is_visible', true);
    }
  }

  /**
   * Store the form's values to this article.
   * @param DRAFTABLE_ENTRY $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);

    if ($this->value_for ('draft'))
    {
      $obj->state = Draft;
    }
  }

  /**
   * Execute the form.
   * @param AUDITABLE $obj
   * @access private
   */
  public function commit ($obj)
  {
    if ($this->object_exists () && $this->value_for ('quick_save'))
    {
      $obj->store ();
    }
    else
    {
      parent::commit ($obj);
    }
  }

  /**
   * Add drafting to the renderer, if allowed.
   * @return FORM_RENDERER
   */
  public function make_renderer ()
  {
    $Result = parent::make_renderer ();
    $Result->drafts_enabled = $this->value_for ('draft');
    if ($Result->drafts_enabled)
    {
      $this->button = 'Publish';
    }
    return $Result;
  }
}

?>