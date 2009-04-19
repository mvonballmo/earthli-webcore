<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.1.0
 * @since 2.2.1
 * @package webcore
 * @subpackage forms-core
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/forms/fields.php');

/**
 * Record object-level errors here.
 * Theses are dispalyed at the top of the form.
 * @see FORM::record_error()
 */
define ('Form_general_error_id', '_general');

/**
 * Load the form with default properties.
 */
define ('Form_load_action_default', 'default');
/**
 * Load the form from the given object.
 */
define ('Form_load_action_object', 'object');
/**
 * Default submit button title.
 */
define ('Form_default_button_title', 'Submit');

/**
 * Refers to the first control in a possible series.
 * {@FORM::num_errors()} has an optional second parameter, which returns the
 * number or errors associated with a particular control for a field. The {@link
 * UPLOAD_FILE_FIELD} is referenced in this way. Use this constant to refer to
 * the first control.
 */
define ('Form_first_control_for_field', 0);

/**
 * Basic HTML forms handling.
 * Manages a list of {@link FIELD}s to validate and display controls.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.2.1
 * @abstract
 */
abstract class FORM extends WEBCORE_OBJECT
{
  /**
   * Name of the form displayed in the page.
   * Commonly used with JavaScript. Only needs to be modified if more than one form
   * per page is displayed. Then this variable is also used to determine which form
   * was submitted back to the generating page.
   * @see FORM::set_name()
   * @var string
   */
  public $name = 'update_form';

  /**
   * Text of the submit button.
   * @var string
   */
  public $button = Form_default_button_title;

  /**
   * Icon for the submit button.
   * @var string
   */
  public $button_icon = '';

  /**
   * HTTP submission method.
   * Also determines from where the form reads it's submitted values. The
   * default method is 'post' because that doesn't generate the parameters to
   * the URL. Other values include 'get', which appends name=value pairs to the
   * URL (but is useful for search forms, so searches can be bookmarked) and
   * 'request', which uses the PHP request variable (which mixes get and post
   * variables into one array based on installation preferences).
   * @var string
   */
  public $method = 'post';

  /**
   * To which url is this form submitted?
   * Defaults to the current page.
   * @var string
   */
  public $action;

  /**
   * Location within the target page.
   * Some browsers do not support this feature. It will be left off if
   * {@link Browser_anchors_in_posts} is not supported.
   */
  public $action_anchor;

  /**
   * Main form container has this CSS class.
   * @var string
   */
  public $CSS_class = 'basic';

  /**
   * Always executes the form as submitted, if set.
   * @var boolean
   */
  public $assume_submitted = false;

  /**
   * Should this form set focus to its initial control?
   * @var boolean
   */
  public $allow_focus = true;

  /**
   * Handles verifying human (vs. robot) input in the form.
   * @var CAPTCHA
   */
  public $captcha;

  /**
   * @param CONTEXT $context Attach to this object.
   */
  public function FORM ($context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);

    $this->page->add_script_file ('{scripts}webcore_forms.js');

    if (! isset ($this->action))
    {
      $this->action = $this->env->url (Url_part_file_name);
    }

    $this->_form_based_field_names [] = 'submitted';

    $this->_update_name ();

    $field = new BOOLEAN_FIELD ();
    $field->id = 'debug';
    $field->title = 'Debug';
    $field->visible = false;
    $this->add_field ($field);

    if ($this->_captcha_enabled ())
    {
      $this->captcha = $this->_make_captcha ();

      $field = new INTEGER_FIELD ();
      $field->id = 'verification_answer';
      $field->title = 'Verification';
      $field->required = true;
      $field->description = 'Please answer the question above using numerals (not text). This is an anti-spam measure; we apologize for the inconvenience.';
      $this->add_field ($field);

      $field = new TEXT_FIELD ();
      $field->id = 'verification_question';
      $field->title = '';
      $field->min_length = 5;
      $field->max_length = 5;
      $field->visible = false;
      $this->add_field ($field);
    }
  }

  /**
   * Has this form been submitted?
   * If the form is never displayed and used only from a "submit handler" page, set
   * the {@link $assume_submitted} flag so it doesn't look in the request to determine
   * whether it was submitted.
   * @return boolean
   */
  public function submitted ()
  {
    $Result = $this->assume_submitted;
    if (! $Result)
    {
      $this->load_from_request ();
      $Result = $this->value_for ($this->form_based_field_name ('submitted'));
    }
    return $Result;
  }

  /**
   * @return boolean Were the contents of this form committed?
   * @see FORM::attempt_action()
   */
  public function committed ()
  {
    return $this->_committed;
  }

  /**
   * Does this form have errors?
   * @return boolean
   */
  public function has_errors ()
  {
    return sizeof ($this->_errors) > 0;
  }

  /**
   * Execute the form against the given object.
   * This call implies that the form is applied against an object, but is not
   * necessarily saving or storing it, simply processing the object. This will
   * {@link commit()} the form if it has been {@link submitted()}.
   * @param object $obj
   */
  public function process ($obj)
  {
    $this->_process ($obj, Form_load_action_default);
  }

  /**
   * Execute the form.
   * Using the form like this will commit an action not associated with a specific object.
   * This will commit the form if it has been {@link submitted()}.
   */
  public function process_plain ()
  {
    $no_obj = null; // Compiler warning
    $this->_process ($no_obj, Form_load_action_default);
  }

  /**
   * Execute the form on the new object.
   * This will commit the form if it has been {@link submitted()}.
   * @param object $obj
   */
  public function process_new ($obj)
  {
    $this->_process ($obj, Form_load_action_default);
  }

  /**
   * Execute the form on the existing object.
   * This will commit the form if it has been {@link submitted()}.
   * @param object $obj
   */
  public function process_existing ($obj)
  {
    $this->_process ($obj, Form_load_action_object);
  }

  /**
   * Validate the form and all fields.
   * This is done automatically when {@link attempt_action()} is called.
   * @param object Validate for this object.
   */
  public function validate ($obj)
  {
    $this->load_from_request ();
    $this->_errors = array ();

    $this->_pre_validate ($obj);

    foreach ($this->_field_list as &$field)
    {
      $field->validate ($this);
    }
    
    $this->_post_validate ($obj);

    /* Since each file upload in a form is its own transaction, we have to process the uploads
       regardless of whether the form is valid or not. This lets a form optimize and upload only
       once (setting the file to the side until the form is successfully submitted). */

    if ($this->contains_uploads ())
    {
      $form_is_valid = ! $this->has_errors ();
      
      foreach ($this->_upload_fields as &$field)
      {
        if ($this->num_errors ($field->id) == 0)
        {
          $idx_file = 0;
          $cnt_file = $field->num_files ();
          while ($idx_file < $cnt_file)
          {
            if ($this->num_errors ($field->id, $idx_file) == 0)
            {
              $this->_process_uploaded_file ($field, $field->file_at ($idx_file), $form_is_valid);
            }
            $idx_file += 1;
          }
        }
      }
    }
  }

  /**
   * Try to apply the values in this form to 'obj'.
   * If the form validates, then apply the values to the object and store it to
   * the database.
   * @param object $obj Store the form values to this object.
   */
  public function attempt_action ($obj)
  {
    $this->_committed = false;
    $this->_object = $obj;
    $this->validate ($obj);
    if (! $this->has_errors ())
    {
      $this->_prepare_for_commit ($obj);
      if (! $this->has_errors ())
      {
        $this->commit ($obj);
        $this->_committed = true;
        $this->_store_sticky_fields ();
      }
    }
  }

  /**
   * Load initial properties from this object.
   * @param object $obj
   */
  public function load_from_object ($obj)
  {
    if (isset ($this->captcha))
    {
      $this->set_value ('verification_question', $this->captcha->encode ());
    }
  }

  /**
   * Load default values for a new object.
   */
  public function load_with_defaults ()
  {
    if (isset ($this->captcha))
    {
      $this->set_value ('verification_question', $this->captcha->encode ());
    }
  }

  /**
   * Displays the form itself using the renderer given.
   * If no renderer is supplied, the result of {@link make_renderer()} is used.
   * @param FORM_RENDERER $renderer
   */
  public function display ($renderer = null)
  {
    /* When debugging forms, it's often nice to see all error messages. It makes it easier to see where
       fields have been drawn, but no call to display that field's errors were made. */

    if ($this->env->debugging)
    {
      $this->display_all_errors ();
    }

    if (! isset ($renderer))
    {
      $renderer = $this->make_renderer ();
    }

    $this->_draw_form ($renderer);

    ob_start ();
      $this->_draw_scripts ();
      $renderer->draw_scripts ();
      $scripts = ob_get_contents ();
    ob_end_clean ();
    if (! empty ($scripts) || ($this->allow_focus && $this->_initial_focus))
    {
?>
<script type="text/javascript">
<?php
  if (! empty ($scripts))
  {
    echo $scripts;
  }
  if ($this->allow_focus && $this->_initial_focus)
  {
    echo '  ' . $this->js_form_name () . '.' . $this->_initial_focus . '.focus ();';
  }
?>

</script>
<?php
    }
  }

  /**
   * Displays errors for all fields in the form.
   */
  public function display_all_errors ()
  {
    if ($this->has_errors ())
    {
      echo "<div class=\"error\">\n";
      foreach ($this->_fields as $field)
      {
        $this->draw_errors ($field->id, false);
      }
      echo "</div>\n";
    }
  }

  /**
   * Returns the list of errors for "id".
   * Returns an empty array if no errors are found.
   * @return array[string]
   */
  public function errors_for ($id)
  {
    if (isset ($this->_errors [$id]))
    {
      $Result = $this->_errors [$id];
    }
    else
    {
      $Result = array ();
    }
    return $Result;
  }

  /**
   * Returns the list of all errors for this form.
   * Merges results from {@link errors_for()} for all fields.
   * @return array[string]
   */
  public function all_errors ()
  {
    $Result = array ();
    if ($this->has_errors ())
    {
      foreach ($this->_fields as $field)
      {
        $Result = array_merge ($Result, $this->errors_for ($field->id));
      }
    }
    return $Result;
  }

  /**
   * Returns the requested fields as query-string arguments.
   * Useful when working with forms with method = 'get'. The values that generated
   * the form can then be retrieved with this function and mapped to other links outside this form.
   * Pagination in search forms is supported this way.
   * @param array[string] $field_names Specifies which field values to use. If empty, all values are returned.
   * @return string
   */
  public function as_query_string ($field_names = '')
  {
    if (! $field_names)
    {
      foreach ($this->_fields as $id => $field)
      {
        $val = $field->as_text ($this);
        $Result [] = "$id=$val";
      }
    }
    else
    {
      foreach ($field_names as $id)
      {
        $field = $this->field_at ($id);
        $val = $field->as_text ($this);
        $Result [] = "$id=$val";
      }
    }

    if (sizeof ($Result))
    {
      $Result = join ('&amp;', $Result);
    }

    return $Result;
  }

  /**
   * Change the name of the form.
   * Do not set the form name manually or the form will not know whether it has been submitted.
   * @param string $name
   */
  public function set_name ($name)
  {
    if ($this->name != $name)
    {
      foreach ($this->_form_based_field_names as $field_name)
      {
        $field_names [] = $this->form_based_field_name ($field_name);
      }

      $idx = 0;
      $count = sizeof ($this->_field_list);
      while ($idx < $count)
      {
        /* As soon as elements are removed, the list shrinks */
        if (isset ($this->_field_list [$idx]))
        {
          $field = $this->_field_list [$idx];
          foreach ($field_names as $field_name)
          {
            if ($field->id == $field_name)
            {
              array_splice ($this->_field_list, $idx, 1);
            }
          }
        }
        $idx += 1;
      }

      unset ($this->_fields [$field_name]);

      $this->name = $name;
      $this->_update_name ();
    }
  }

  /**
   * JavaScript for retrieving the form.
   * @return string
   */
  public function js_form_name ()
  {
    return "document.getElementById ('$this->name')";
  }

  /**
   * Return a reference to the requested field.
   * @param string $id Must be a valid field id.
   * @return FIELD
   * @access private
   */
  public function field_at ($id)
  {
    $this->_verify_id ($id, 'field_at');
    return $this->_fields [$id];
  }

  /**
   * What is the current value of this field?
   * @param string $id Must be a valid field id.
   * @return string
   * @access private
   */
  public function value_for ($id)
  {
    $this->_verify_id ($id, 'value_for');
    return $this->_fields [$id]->value ();
  }

  /**
   * Retrieve the {@link UPLOADED_FILE} from the given field.
   * Use the 'index' parameter to select which control to use (the default uses
   * the first one). Only returns an object is it exists and is valid;
   * otherwise, returns <code>null</code>.
   * @param string $id Must be a valid field id.
   * @return UPLOADED_FILE
   * @access private
   */
  public function upload_file_for ($id, $index = Form_first_control_for_field)
  {
    $this->_verify_id ($id, 'upload_file_for');
    $file_set = $this->value_for ($id);
    if (isset ($file_set))
    {
      $file = $file_set->files [$index];
      if (isset ($file) && $file->is_valid ())
      {
        return $file;
      }
    }
    
    return null;
  }

  /**
   * Does the field for 'id' exist?
   * @param string $id
   * @return boolean
   */
  public function is_field ($id)
  {
    return array_key_exists ($id, $this->_fields);
  }

  /**
   * Is this field enabled?
   * @param string $id Must be a valid field id.
   * @return boolean
   * @access private
   */
  public function enabled ($id)
  {
    $this->_verify_id ($id, 'enabled');
    return $this->_fields [$id]->enabled;
  }

  /**
   * Is this field required in order for the form to be committed?
   * @param string $id Must be a valid field id.
   * @return boolean
   * @access private
   */
  public function required ($id)
  {
    $this->_verify_id ($id, 'required');
    return $this->_fields [$id]->required;
  }

  /**
   * Is this value for this field selected?
   * Only makes sense with array field types, usually connected to arrays of checkboxes.
   * @see ARRAY_FIELD
   * @param string $id Must be a valid field id.
   * @param string $value
   * @return boolean
   * @access private
   */
  public function selected ($id, $value)
  {
    $this->_verify_id ($id, 'visible');
    return $this->_fields [$id]->selected ($value);
  }

  /**
   * Is this field visible?
   * @param string $id Must be a valid field id.
   * @return boolean
   * @access private
   */
  public function visible ($id)
  {
    $this->_verify_id ($id, 'visible');
    return $this->_fields [$id]->visible;
  }

  /**
   * The fully-resolved JavaScript reference to this field.
   * Includes the form and document qualifiers.
   * @param string $id Must be a valid field id.
   * @see FORM::name()
   * @return string
   * @access private
   */
  public function js_name ($id)
  {
    $this->_verify_id ($id, 'js_name');
    $js_name = $this->_fields [$id]->js_name ();
    return $this->js_form_name () . " ['$js_name']";
  }

  /**
   * Simply the field's Javascript name.
   * Is not fully resolved with the form and document name.
   * @see FORM::js_name()
   * @param string $id Must be a valid field id.
   * @return string
   * @access private
   */
  public function name ($id)
  {
    $this->_verify_id ($id, 'name');
    return $this->_fields [$id]->js_name ();
  }

  /**
   * Return the contents of field 'id' as text.
   * This routine cleans out HTML entities and automagically added quotes (if magic quotes are enabled).
   * @param string $id Must be a valid field id.
   * @return string
   * @access private
   */
  public function value_as_text ($id)
  {
    $this->_verify_id ($id, 'value_as_text');
    return $this->_fields [$id]->as_text ($this);
  }

  /**
   * Return whether the value is interpreted as empty.
   * Some fields return objects from {@link value_for()}. Use this function to determine whether
   * the value can be treated as empty (e.g. when validating).
   * @return boolean
   * @access private
   */
  public function value_is_empty ($id)
  {
    $this->_verify_id ($id, 'value_is_empty');
    return $this->_fields [$id]->is_empty ();
  }

  /**
   * Set the enabled state of the field at 'id'.
   * @param string $id Must be a valid field id.
   * @param string $value
   * @access private
   */
  public function set_enabled ($id, $value)
  {
    $this->_verify_id ($id, 'set_enabled');
    $this->_fields [$id]->enabled = $value;
  }

  /**
   * Set the required state of the field at 'id'.
   * @param string $id Must be a valid field id.
   * @param string $value
   * @access private
   */
  public function set_required ($id, $value)
  {
    $this->_verify_id ($id, 'set_required');
    $this->_fields [$id]->required = $value;
  }

  /**
   * Set the visible state of the field at 'id'.
   * @param string $id Must be a valid field id.
   * @param string $value
   * @access private
   */
  public function set_visible ($id, $value)
  {
    $this->_verify_id ($id, 'set_visible');
    $this->_fields [$id]->visible = $value;
  }

  /**
   * Set the value of the field at 'id'.
   * @param string $id Must be a valid field id.
   * @param string $value
   * @access private
   */
  public function set_value ($id, $value)
  {
    $this->_verify_id ($id, 'set_value');
    $this->_fields [$id]->set_value ($value);
  }

  /**
   * Set the value of the field at 'id' from the client, if possible.
   * @param string $id Must be a valid field id.
   * @param string $value Default value if none is found.
   * @access private
   */
  public function load_from_client ($id, $value)
  {
    $this->_verify_id ($id, 'load_from_client');
    $this->_fields [$id]->load_from_client ($this, $this->context->storage, $value);
  }

  /**
   * Load the value of the field at 'id' from the request.
   * @param string $id Must be a valid field id. */
  public function load_from_request_for ($id)
  {
    $this->_verify_id ($id, 'load_from_request_for');
    $values = $this->_request_array_to_use ();
    $this->_fields [$id]->set_value_from_request ($values);
  }

  /**
   * Which control has initial focus?
   * @return string
   */
  public function initial_focus ()
  {
    return $this->_initial_focus;
  }

  /**
   * Make this control focussed when the form is displayed.
   * If 'id' is empty, the form will use the browser-default focus.
   * @param string $id Must be a valid field id.
   */
  public function set_initial_focus ($id)
  {
    if ($id)
    {
      $this->_verify_id ($id, 'set_initial_focus');
    }
    $this->_initial_focus = $id;
  }

  /**
   * Return a validator for {@link MUNGER} tags.
   * @param string $type Can be {@link Tag_validator_single_line} or {@link Tag_validator_multi_line}.
   * @return MUNGER_VALIDATOR
   */
  public function tag_validator ($type)
  {
    return $this->context->make_tag_validator ($type);
  }

  /**
   * Return an object that manages uploaded files.
   * @return UPLOADER
   */
  public function uploader ()
  {
    if (! isset ($this->_uploader))
    {
      $class_name = $this->context->final_class_name ('UPLOADER', 'webcore/util/uploader.php');
      $this->_uploader = new $class_name ($this->context);
    }

    return $this->_uploader;
  }

  /**
   * Returns True if there are upload fields in the form.
   * @return boolean
   */
  public function contains_uploads ()
  {
    return sizeof ($this->_upload_fields) > 0;
  }

  /**
   * Record an error for the field at 'id'.
   * If the 'idx' is provided, then the error is recorded only for the idx-th control for this field.
   * @param string $id Must be a valid field id.
   * @param string $msg The error message *
   * @access private
   */
  public function record_error ($id, $msg, $idx = null)
  {
    if (isset ($idx))
    {
      $id .= $idx;
    }
    if (! array_key_exists ($id, $this->_errors))
    {
      $this->_errors [$id] = array ();
    }
    array_push ($this->_errors [$id], $msg);
  }

  /**
   * How many errors have occurred for 'id'?
   * If the 'idx' is provided, then only errors for the idx-th control for this field are returned.
   * @param string $id Must be a valid field id.
   * @return integer
   * @access private
   */
  public function num_errors ($id, $idx = null)
  {
    if (isset ($idx))
    {
      $id .= $idx;
    }
    return isset ($this->_errors [$id]) && sizeof ($this->_errors [$id]);
  }

  /**
   * Return the index'th error for field 'id'.
   * If the 'idx' is provided, then only errors for the idx-th control for this field are returned.
   * Used to iterate errors.
   * @see FORM::num_errors()
   * @param string $id Must be a valid field id.
   * @param integer $index
   * @return integer
   * @access private
   */
  public function error_at ($id, $index, $idx = null)
  {
    if (isset ($idx))
    {
      $id .= $idx;
    }
    return $this->_errors [$id][$index];
  }

  /**
   * Add a field to the form.
   * Almost always called from the {@link FORM} constructor. The parameter
   * is explicitly not a reference for PHP4; call this function only after
   * setting all properties of the field. In PHP5, normal semantics apply.
   * @param FIELD $field
   * @access private
   */
  public function add_field ($field)
  {
    $this->_field_list [] = $field;
    $this->_fields [$field->id] = $field;
    $field->added_to_form ($this);
  }

  /**
   * Make a renderer for this form.
   * This is deferred to the {@link CONTEXT} on which the form is based, so that the
   * user can customize form rendering from one spot. Call this function to
   * retrieve a renderer to pass to the {@link display()} function if you want
   * to customize output for the form.
   * @return FORM_RENDERER
   */
  public function make_renderer ()
  {
    return $this->context->make_form_renderer ($this);
  }

  /**
   * Returns the maximum desired upload size.
   * Iterates the fields that have registered as uploaders and returns the
   * maximum of these values. Adjusts the values returned by the global settings
   * for PHP (set in the INI file for post size).
   * @return integer
   */
  public function max_upload_file_size ()
  {
    if (! isset ($this->_max_upload_file_size))
    {
      $size = 0;
      if ($this->contains_uploads ())
      {
        foreach ($this->_upload_fields as $field)
        {
          $size = max ($size, $field->max_bytes);
        }
      }

      $uploader = $this->uploader ();
      if ($size)
      {
        $size = min ($size, $uploader->ini_max_file_size);
      }
      else
      {
        $size = $uploader->ini_max_file_size;
      }

      $this->_max_upload_file_size = $size;
    }

    return $this->_max_upload_file_size;
  }

  /**
   * Does this form represent an existing object?
   * @return bool
   * @access private
   */
  public function object_exists ()
  {
    return isset ($this->_object) && $this->_object->exists ();
  }

  /**
   * Load the form with valus from the HTTP request.
   * @param boolean $force_reload Will always load, regardless of whether it's
   * already loaded or not.
   */
  public function load_from_request ($force_reload = false)
  {
    if (! $this->_loaded || $force_reload)
    {
      $this->_load_from_request ();
      $this->_loaded = true;
    }
  }

  /**
   * Register an upload field in this form.
   * Used internally by {@link UPLOAD_FILE_FIELD} so that the form is aware of all uploaders
   * in it and can properly calculate the {@link max_upload_file_size()}.
   * @param UPLOAD_FILE_FIELD $field
   * @access private
   */
  public function add_upload_field ($field)
  {
    $this->_upload_fields [] = $field;

    if (! isset ($this->_fields [Form_max_file_size_field_name]))
    {
      // Do not use the local name "field" because it is assigned
      // to the argument in PHP4.

      $max_field = new INTEGER_FIELD ();
      $max_field->id = Form_max_file_size_field_name;
      $max_field->min_value = 0;
      $max_field->visible = false;
      $this->add_field ($max_field);
    }
  }

  /**
   * Return the correct PHP global array for values.
   * Used by {@link _load_from_request()}.
   * return array[string]
   * @access private
   */
  protected function _request_array_to_use ()
  {
    switch ($this->method)
    {
    case 'post':
      return $_POST;
    case 'get':
      return $_GET;
    case 'request':
      return $_REQUEST;
    }
    
    return null;
  }

  /**
   * Form-specific name for specials fields.
   * Form properties are named relative to the form to allow more than one form to be submitted and
   * processed within one page.
   * @param string $base_name
   * @return string
   */
  public function form_based_field_name ($base_name)
  {
    return "{$this->name}_$base_name";
  }

  /**
   * Read in values from the {@link $method} array.
   * Redefine this function in order to adjust the request data or otherwise
   * react to parameters in the request. Called from {@link
   * load_from_request()}.
   * @access private
   */
  protected function _load_from_request ()
  {
    $values = $this->_request_array_to_use ();
    
    foreach ($this->_field_list as &$field)
    {
      $field->set_value_from_request ($values);
    }
    
    if (isset ($this->captcha))
    {
      $question = $this->value_for ('verification_question');
      if ($question)
      {
        $this->captcha->decode ($question);
      }
    }
  }

  /**
   * Notifies fields to store sticky values.
   * Called when a form is successfully committed so that "sticky" fields can
   * store their current values on the client.
   * @access private
   */
  protected function _store_sticky_fields ()
  {
    $s = $this->context->storage;
    $s->expire_in_n_days ($this->context->storage_options->setting_duration);
    $s->start_multiple_value ($this->name);
    foreach ($this->_fields as $field)
    {
      if ($field->sticky)
      {
        $field->store_to_client ($this, $s);
      }
    }
    $s->finish_multiple_value ();
  }

  /**
   * One of more files for 'field' were uploaded.
   * PHP deletes uploaded files if they are not immediately processed. This means it is possible
   * for a form to successfully upload a file, but not successfully validate. If the upload is not
   * saved, the user will have to upload the file again when resubmitting the form. Forms therefore
   * process each file upload as a separate transaction, so that a form can automatically store
   * successfully uploaded files to a 'temp' directory, where PHP can't delete them. When the form
   * is successfully committed, it can then move the files to their final destinations.
   *
   * The form can determine whether the file needs to be saved by checking the 'form_is_valid' flag
   * to see whether the form itself will be committed or not. If the file was already uploaded in a
   * previous form submission attempt, the file will be marked as 'processed'.
   *
   * @see CONTEXT::$upload_options
   * @see _move_uploaded_file()
   *
   * @param UPLOAD_FILE_FIELD $field
   * @param UPLOADED_FILE $file
   * @param boolean $form_is_valid Will the form be committed?
   * @access private
   */
  protected function _process_uploaded_file ($field, $file, $form_is_valid)
  {
    if (! $file->processed && $file->is_valid ())
    {
      $f = $this->_upload_folder_for ($field, $file, $form_is_valid);
      if ($f)
      {
        $this->_move_uploaded_file ($field, $file, $f, $form_is_valid);
        if (!file_name_to_url ($file->current_name ()))
        {
          $this->record_error ($field->id, 'Error in upload configuration (uploaded file [' . $file->current_name () . '] is not visible under any registered mapping).');
        }
      }
    }
  }

  /**
   * Performs the actual move for {@link _process_uploaded_file()}.
   * Can be called from {@link _prepare_for_commit()} in descendent forms to use
   * the same mechanisms for resolved overwrite options.
   * @param UPLOAD_FILE_FIELD $field File is associated with this field.
   * @param UPLOADED_FILE $file Move this file object.
   * @param $path Move to this folder.
   * @param boolean $form_is_valid Will the form be committed?
   * @access private
   */
  protected function _move_uploaded_file ($field, $file, $path, $form_is_valid = true)
  {
    $file->move_to ($path, $this->_upload_file_copy_mode ($field, $file, $form_is_valid));
  }

  /**
   * Return a path for this file.
   * The file will be copied to this location when it is uploaded. Default behavior stores
   * files to a 'temp' area until the form is actually committed. Descendent forms can override
   * this behavior to move files directly to the final location. E.g. if the file is an image, it
   * needs to be in the document root in order to be displayed in a preview.
   * @param UPLOAD_FILE_FIELD $field
   * @param UPLOADED_FILE $file
   * @param boolean $form_is_valid Will the form be committed?
   * @access private
   */
  protected function _upload_folder_for ($field, $file, $form_is_valid)
  {
    $temp_folder = $this->context->upload_options->temp_folder;
    $path = $this->context->resolve_path ($temp_folder, Force_root_on);
    return url_to_folder ($path);
  }

  /**
   * Specify how to store this uploaded file.
   * Return a file-copy mode to determine whether to overwrite an existing file in the move-to location.
   * @param UPLOAD_FILE_FIELD $field
   * @param UPLOADED_FILE $file
   * @param boolean $form_is_valid Will the form be committed?
   * @return string Can be {@link Uploaded_file_unique_name} or {@link Uploaded_file_overwrite}.
   * @access private
   */
  protected function _upload_file_copy_mode ($field, $file, $form_is_valid)
  {
    return Uploaded_file_unique_name;
  }

  /**
   * Loads the form with data, given an object and action.
   * Loads data from the object using {@link load_from_object()} or with
   * defaults using {@link load_with_defaults()} in order to set up disabled
   * states for controls correctly. Then re-load the request data on top of
   * the object's data in the form to reflect the actual edited state.
   * @param object $obj
   * @param string $load_action
   * @access private
   */
  protected function _apply_all_data ($obj, $load_action)
  {
    $this->_process_load_action ($obj, $load_action);
    $this->load_from_request (true);
    $this->_post_load_data ($obj);
  }

  /**
   * Executes the form for an object.
   * @param object $obj
   * @param string $load_action
   * @access private
   */
  protected function _process ($obj, $load_action)
  {
    $this->_load_action = $load_action;

    if ($this->submitted ())
    {
      $this->attempt_action ($obj);
      if (! $this->committed ())
      {
        $this->_apply_all_data ($obj, $load_action);
      }
    }
    else
    {
      $this->_process_load_action ($obj, $load_action);
      $this->_post_load_data ($obj);
    }
  }

  /**
   * Load the form according to the given action.
   * @param object $obj
   * @param string $load_action Can be {@link Form_load_action_default} or {@link Form_load_action_object}.
   * @access private
   */
  protected function _process_load_action ($obj, $load_action)
  {
    $this->_object = $obj;
    /* Make sure to load any client-side data for this form. */
    $this->context->storage->load_multiple_values ($this->name);

    switch ($load_action)
    {
    case Form_load_action_default:
      $this->load_with_defaults ();
      break;
    case Form_load_action_object:
      $this->load_from_object ($obj);
      break;
    default:
      $this->raise ('Unknown load action.', '_process_load_action', 'FORM');
    }
  }

  /**
   * @param string Name of the control.
   * @param string Function from which the check comes from.
   * @return boolean
   * @access private
   */
  protected function _verify_id ($id, $func)
  {
    $this->assert (array_key_exists ($id, $this->_fields), "[$id] is not a field.", $func, 'FORM');
  }

  /**
   * Called before fields are validated.
   * Values have already been loaded, but validation hasn't begun. Here, forms can change field
   * properties before validating them. This is useful for changing {@link FIELD::$required}
   * on fields which depend on other field values. For example, if an integer field depends on
   * a boolean field being checked before it is used. The integer field's 'required' property can
   * be set to whether the boolean field is selected.
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _pre_validate ($obj) {}

  /**
   * Called after fields are validated.
   * Perform multi-field validation here. Use {@link num_errors()} to check whether a field
   * already has errors recorded. Use {@link record_error()} to record new errors for a field.
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    if (isset ($this->captcha))
    {
      if (! $this->num_errors ('verification_answer') && ! $this->captcha->validate ($this->value_for ('verification_answer')))
      {
        $this->record_error ('verification_answer', 'Please provide the correct answer.');
      }
    }
  }

  /**
   * Called after fields are loaded with data.
   * The form is always prepared with a certain data set. Data can be loaded from an existing object,
   * a new object, default values, the server request or some combination thereof. Once the data
   * is fully loaded, this function is executed to allow forms to update control states and prepare
   * for display (e.g. Set enabled/visible states depending on data).
   * @param object $obj Object from which data was loaded. May be null.
   * @access private
   */
  protected function _post_load_data ($obj) {}

  /**
   * Apply post-validation operations to the object.
   * The object itself has passed validation and is ready to be stored. However, you may want to perform
   * other operations which are not object-dependent and which may fail. {@link record_error()} may be used
   * here to stop the call to {@link commit()}.
   *
   * For example, an attachment may be fully validated, but the thumbnail cannot be created. Create the
   * thumbnail in this function and abort the commit. The fact that the object could not be committed has
   * nothing to do with its validity; external factors prevented it from being committed.
   * @param object $obj
   * @access private
   */
  protected function _prepare_for_commit ($obj) {}

  /**
   * Return true to use integrated captcha verification.
   * @return boolean
   */
  protected function _captcha_enabled ()
  {
    return false;
  }

  /**
   * Create a human validator for this form.
   * @return CAPTCHA
   */
  protected function _make_captcha ()
  {
    $class_name = $this->context->final_class_name ('CAPTCHA', 'webcore/util/captcha.php');
    return new $class_name ($this->context);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_hidden_controls ($renderer)
  {
    $sub_field = $this->field_at ($this->form_based_field_name ('submitted'));
    $old_sub_value = $sub_field->value ();
    $sub_field->set_value (1);

    /* Update the maximum number of bytes allowed for upload. */

    if ($this->contains_uploads ())
    {
      $this->set_value (Form_max_file_size_field_name, $this->max_upload_file_size ());
    }

    /* Always reset the debug to be read from anywhere in the request, not just the form. */

    $this->set_value ('debug', $this->env->debugging_flags);

    foreach ($this->_fields as $field)
    {
      if (! $field->visible)
      {
        $renderer->draw_hidden ($field->id);
      }
    }

    $sub_field->set_value ($old_sub_value);
  }

  /**
   * Draw the form itself.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_form ($renderer)
  {
    $encoding = '';
    if ($this->contains_uploads ())
    {
      $encoding = ' enctype="multipart/form-data"';
    }
    $method = $this->method;
    if ($method == 'request')
    {
      $method = 'post';
    }
    $action = $this->action;
    if (isset ($this->action_anchor))
    {
      $browser = $this->env->browser ();
      if ($browser->supports (Browser_anchors_in_posts))
      {
        $action = $action . '#' . $this->action_anchor;
      }
    }
?>
<form id="<?php echo $this->name; ?>" action="<?php echo $action; ?>" method="<?php echo $method; ?>"<?php echo $encoding; ?>>
  <div>
    <?php $this->_draw_hidden_controls ($renderer); ?>
    <?php $this->_draw_controls ($renderer); ?>
  </div>
</form>
<?php
  }

  /**
   * Draw captcha controls to verify human input.
   * Captcha handling is provided by default, but a form must determine on its
   * own where the controls appear.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_captcha_controls ($renderer)
  {
    if (isset ($this->captcha))
    {
      $renderer->draw_text_row (' ', $this->captcha->as_html ());
      $renderer->draw_text_line_row ('verification_answer');
    }
  }

  /**
   * Draw the controls for the form.
   * @param FORM_RENDERER $renderer
   * @access private
   * @abstract
   */
  protected abstract function _draw_controls ($renderer);

  /**
   * Draw any Javascript that the form needs to enable/disable controls.
   * Include Javascript source or define functions and variables here.
   * @access private
   */
  protected function _draw_scripts () {}

  /**
   * Create the JavaScript needed for an icon browser.
   * @param string $id
   * @access private
   */
  protected function _draw_icon_browser_script_for ($id)
  {
    $this->_verify_id ($id, '_draw_icon_browser_script_for');
    $js_form = $this->js_form_name ();
?>
  if (<?php echo $js_form . '.' . $id; ?>)
  {
    var icon_url_field = new WEBCORE_VALUE_FIELD ();
    <?php echo $id; ?>_field.attach (<?php echo $js_form . '.' . $id; ?>);
    <?php echo $id; ?>_field.width = 600;
    <?php echo $id; ?>_field.height = 600;
    <?php echo $id; ?>_field.page_name = 'browse_icon.php';
    <?php echo $id; ?>_field.set_value ('<?php echo $this->value_for ($id); ?>');
  }
<?php
  }

  /**
   * Draw errors for the field at 'id'
   * @param string $id
   * @param boolean $use_style Surround messages with 'error' style if True.
   * @access private
   */
  public function draw_errors ($id, $use_style = true)
  {
    $errors = $this->errors_for ($id);
    if (sizeof ($errors))
    {
      if ($use_style)
      {
        echo "<div class=\"error\">\n";
      }
      foreach ($errors as $error)
      {
        echo '<div>' . $error . '</div>';
      }
      if ($use_style)
      {
        echo "</div>\n";
      }
    }
  }

  /**
   * Update the name of the form.
   * @access private
   */
  protected function _update_name ()
  {
    foreach ($this->_form_based_field_names as $field_name)
    {
      $field = new BOOLEAN_FIELD ();
      $field->id = $this->form_based_field_name ($field_name);
      $field->title = ucfirst ($field_name);
      $field->set_value(false);
      $field->visible = false;
      $this->add_field ($field);
    }
  }

  /**
   * Execute the form.
   * The form has been validated and can be executed.
   * @param object $obj
   * @access private
   * @abstract
   */
  public function commit ($obj) {}

  /**
   * Table of fields indexed by id.
   * @var array[string,FIELD]
   * @see FIELD
   * @access private
   */
  protected $_fields = array ();

  /**
   * Simple list of all fields.
   * Used for referenced iteration.
   * @var array[FIELD]
   * @access private
   */
  protected $_field_list = array ();

  /**
   * @var array[string]
   * @access private
   */
  protected $_errors = array ();

  /**
   * @var array[UPLOAD_FILE_FIELD]
   * @see UPLOAD_FILE_FIELD
   * @access private
   */
  protected $_upload_fields = array ();

  /**
   * @var bool
   * @access private
   */
  protected $_committed = false;

  /**
   * @var bool
   * @access private
   */
  protected $_loaded = false;

  /**
   * @var object
   * @access private
   */
  protected $_object;

  /**
   * Set when the form is loaded or processed.
   * Can be {@link Form_load_action_default} or {@link Form_load_action_object}.
   * @var string
   * @access private
   */
  protected $_load_action;

  /**
   * Name of the control with focus when the form is displayed.
   * Set this before calling {@link display()}.
   * @var string
   * @access private
   */
  protected $_initial_focus = '';

  /**
   * @var integer
   * @access private
   */
  protected $_max_upload_file_size;
}

/**
 * Forms that use an id as foreign key.
 * Use {@link UNIQUE_OBJECT_FORM} to use the primary key.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @abstract
 */
abstract class ID_BASED_FORM extends FORM
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function ID_BASED_FORM ($app)
  {
    FORM::FORM ($app);

    $field = new INTEGER_FIELD ();
    $field->id = 'id';
    $field->title = 'ID';
    $field->min_value = 1;
    $field->visible = false;
    $this->add_field ($field);
  }

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

?>