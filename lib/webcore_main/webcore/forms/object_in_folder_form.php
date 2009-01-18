<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/forms/content_object_form.php');

/**
 * Base form for {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.5.0
 */
class OBJECT_IN_FOLDER_FORM extends CONTENT_OBJECT_FORM
{
  /**
   * @param FOLDER &$folder Object is created/edited in this folder.
   */
  function OBJECT_IN_FOLDER_FORM (&$folder)
  {
    CONTENT_OBJECT_FORM::CONTENT_OBJECT_FORM ($folder->app);
    $this->_folder = $folder;

    $field = new BOOLEAN_FIELD ();
    $field->id = 'is_visible';
    $field->title = 'Visible';
    $field->description = 'Show this item to non-admin users.';
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this object.
    * @param OBJECT_IN_FOLDER &$obj
    * @access private
    */
  function _store_to_object (&$obj)
  {
    parent::_store_to_object ($obj);

    if ($this->visible ('is_visible'))
      // if this field is displayed, then use it's value.
    {
      if ($this->value_for ('is_visible'))
        $obj->show (FALSE);
      else
        $obj->hide (FALSE);
    }
  }

  /**
   * Load initial properties from this object.
   * @param OBJECT_IN_FOLDER &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('is_visible', $obj->visible ());
    $this->set_visible ('is_visible', $this->_visible_allowed ());
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('is_visible', TRUE);
    $this->set_visible ('is_visible', $this->_visible_allowed ());
  }

  /**
   * Can the current user see invisible objects of this type?
   * @return boolean
   * @access private
   */
  function _visible_allowed ()
  {
    return $this->login->is_allowed ($this->_privilege_set, Privilege_view_hidden, $this->_folder);
  }

  /**
   * Return true if there are options to be drawn by {@link _draw_options()}.
   * @return boolean 
   * @access private
   */
  function _has_options ()
  {
    return $this->visible ('is_visible'); 
  }
    
  /**
   * Draw options between the title and description.
   * This is a good place for controls that add content to the description
   * (toolbars, etc.).
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_options (&$renderer)
  {
    $renderer->draw_check_box_row ('is_visible');
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_separator ();
    if ($this->_has_options ())
    {
      $this->_draw_options ($renderer);
      $renderer->draw_separator ();
    }
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer, FALSE);
    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set;
  /**
   * Folder containing the object being edited/created.
   * @var FOLDER
   * @access private
   */
  var $_folder;
}

/**
 * Base form for {@link ATTACHMENT_HOST} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.1
 */
class ATTACHMENT_HOST_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param FOLDER &$folder Object is created/edited in this folder.
   */
  function ATTACHMENT_HOST_FORM (&$folder)
  {
    OBJECT_IN_FOLDER_FORM::OBJECT_IN_FOLDER_FORM ($folder);

    $field = new TEXT_FIELD ();
    $field->id = 'attachments';
    $field->title = 'Attachments';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'alignments';
    $field->title = 'Alignments';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'sizes';
    $field->title = 'Sizes';
    $this->add_field ($field);
  }
  
  /**
   * @see ATTACHMENT
   * @return array[ATTACHMENT]
   * @access private
   */
  function _attachments ()
  {
    if (! isset ($this->_attachments))
    {
      if ($this->object_exists ())
      {
        $attachment_query = $this->_object->attachment_query ();
        $this->_attachments = $attachment_query->objects ();
      }
      else
        $this->_attachments = array ();
    }
    return $this->_attachments;
  }
  
  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_attachment_selector (&$renderer)
  {
    $atts = $this->_attachments ();
    if (sizeof ($atts))
    {
      $props = $renderer->make_list_properties ();
      foreach ($atts as $att)
        $props->add_item ($att->title_as_plain_text (), $att->file_name);
      $attachments = $renderer->drop_down_as_html ('attachments', $props);
      
      $props = $renderer->make_list_properties ();
      $props->add_item ('Left', 'left');
      $props->add_item ('Left (alone)', 'left-column');
      $props->add_item ('Center', 'center');
      $props->add_item ('Right', 'right');
      $props->add_item ('Right (alone)', 'right-column');
      $props->add_item ('None', 'none');
      $alignments = $renderer->drop_down_as_html ('alignments', $props);
      
      $props = $renderer->make_list_properties ();
      $props->add_item ('Thumbnail', 'thumbnail');
      $props->add_item ('25%', '25');
      $props->add_item ('50%', '50');
      $props->add_item ('75%', '75');
      $props->add_item ('Full-size', '100');
      $sizes = $renderer->drop_down_as_html ('sizes', $props);
        
      $renderer->start_row ('Attachments');
        echo $attachments . '&nbsp;';
        echo $alignments . '&nbsp;';
        echo $sizes;
        echo $renderer->javascript_button_as_html ('Add', 'on_insert_attachment ()');
      $renderer->finish_row ();
      $browser = $this->env->browser ();
      if ($browser->supports (Browser_DOM_2))
        $renderer->draw_text_row (' ', 'Insert image/media tag for the selected attachment.', 'notes');
      else
        $renderer->draw_text_row (' ', 'Append image/media tag for the selected attachment (scroll to end of text).', 'notes');
    }
  }
  
  /**
   * @access private
   */
  function _draw_scripts ()
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
      if (size == "thumbnail")
        text_to_insert = '<img attachment="' + f.attachments.value + '" align="' + alignment + '" class="frame" caption="' + caption_text + '">';
      else
      {
        if (size == "100")
          text_to_insert = '<img src="{att_link}' + f.attachments.value + '" align="' + alignment + '" class="frame" caption="' + caption_text + '">';
        else      
          text_to_insert = '<img src="{att_link}' + f.attachments.value + '" href="{att_link}' + f.attachments.value + '" align="' + alignment + '" class="frame" caption="' + caption_text + '" scale="' + size + '%">';
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
  function _has_options ()
  {
    return parent::_has_options () || ($this->object_exists () && sizeof ($this->_attachments ())); 
  }
    
  /**
   * Draw options between the title and description.
   * This is a good place for controls that add content to the description
   * (toolbars, etc.).
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_options (&$renderer)
  {
    parent::_draw_options ($renderer);
    $this->_draw_attachment_selector ($renderer);
  }
}

/**
 * Base form for {@link ENTRY} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.5.0
 */
class ENTRY_FORM extends ATTACHMENT_HOST_FORM
{
  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set = Privilege_set_entry;
}

/**
 * Base form for {@link DRAFTABLE_ENTRY} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.5.0
 */
class DRAFTABLE_ENTRY_FORM extends ENTRY_FORM
{
  /**
   * @param CONTEXT &$context Attach to this object.
   */
  function DRAFTABLE_ENTRY_FORM (&$context)
  {
    ENTRY_FORM::ENTRY_FORM ($context);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'draft';
    $field->title = 'Draft';
    $field->visible = FALSE;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'quick_save';
    $field->title = 'Quick Save';
    $field->visible = FALSE;
    $this->add_field ($field);
  }

  /**
   * New object is being created, load default values.
   */
  function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('draft', TRUE);
    $this->set_value ('is_visible', TRUE);
    $this->set_visible ('is_visible', FALSE);
  }

  /**
   * Load initial properties from the object.
   * @param DRAFTABLE_ENTRY &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('draft', $obj->unpublished ());
    if ($obj->unpublished ())
    {
      $this->set_value ('is_visible', TRUE);
      $this->set_visible ('is_visible', FALSE);
    }
  }

  /**
   * Load initial properties from the object, but store as a new object.
   * @param DRAFTABLE_ENTRY &$obj
   */
  function load_from_clone (&$obj)
  {
    $obj->time_published->clear ();
    $obj->publisher_id = 0;
    $obj->state = Draft;
    parent::load_from_clone ($obj);
  }

  /**
   * Execute the form on a cloned object.
   * This will commit the form if it has been {@link submitted()}.
   * @param object &$obj Object being copied.
   */
  function process_clone (&$obj)
  {
    $obj->time_published->clear ();
    $obj->publisher_id = 0;
    parent::process_clone ($obj);
  }

  /**
   * Store the form's values to this article.
   * @param DRAFTABLE_ENTRY &$obj
   * @access private
   */
  function _store_to_object (&$obj)
  {
    parent::_store_to_object ($obj);

    if ($this->value_for ('draft'))
      $obj->state = Draft;
  }

  /**
   * Execute the form.
   * @param AUDITABLE &$obj
   * @access private
   */
  function commit (&$obj)
  {
    if ($this->object_exists () && $this->value_for ('quick_save'))
      $obj->store ();
    else
      parent::commit ($obj);
  }

  /**
   * Add drafting to the renderer, if allowed.
   * @return FORM_RENDERER
   */
  function make_renderer ()
  {
    $Result = parent::make_renderer ();
    $Result->drafts_enabled = $this->value_for ('draft');
    if ($Result->drafts_enabled)
      $this->button = 'Publish';
    return $Result;
  }
}

?>