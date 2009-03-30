<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.0.0
 * @since 2.2.1
 * @package webcore
 * @subpackage forms-core
 * @access private
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

/**
 * Base class for different types of form fields.
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @see FORM
 * @access private
 */
class FIELD extends RAISABLE
{
  /**
   * Identifier used for control.
   * This must be unique to the form to which this field is attached.
   * @var string
   */
  public $id = '';

  /**
   * Title used in error messages and display.
   * @var string
   */
  public $title = '';

  /**
   * Must this field be non-empty?
   * @var boolean
   */
  public $required = false;

  /**
   * Is the control for this field enabled (can it be changed?)
   * @var boolean
   */
  public $enabled = true;

  /**
   * Is the control displayed in the form?
   * @var boolean
   */
  public $visible = true;

  /**
   * Description of the control.
   * Displayed differently depending on the control. Some controls don't display this at all.
   * @var string
   */
  public $description = '';

  /**
   * Perform tag validation for content.
   * Text fields can be checked for compliance with the munger formatter. Errors are listed with
   * line and character number.
   * @var boolean
   */
  public $tag_validator_type = Tag_validator_none;

  /**
   * Persist values on the client?
   * If True, the last value entered is stored in a cookie and used as the
   * default value when the same form is shown again.
   * @var boolean
   */
  public $sticky = false;

  /**
   * Check whether the current value for this field conforms.
   * Doesn't return a value. Just record conformance violations with {@link FORM::record_error()}.
   * @see FORM::record_error()
   * @param FORM $form
   */
  public function validate ($form)
  {
    if ($this->required && $this->is_empty ())
    {
      $form->record_error ($this->id, "Please provide a value for $this->title.");
    }
  }

  /**
   * Is this field's value set?
   * @return boolean
   */
  public function is_empty ()
  {
    return ! isset ($this->_value) || ($this->_value === '');
  }

  /**
   * Transform the id to a valid JavaScript id.
   * @return string
   */
  public function js_name ()
  {
    return $this->id;
  }

  /**
   * Is this field selected?
   * Returns true if 'value' is equal to the field's value. Some fields will override this
   * behavior to imbue it with their own semantics (e.g. {@link ARRAY_FIELD}).
   * @param mixed $value
   * @return boolean
   */
  public function selected ($value)
  {
    return $this->value () == $value;
  }

  /**
   * Return the value in native format.
   * You can also retrieve the value as text using {@link as_text()}.
   * @return mixed
   */
  public function value ()
  {
    if (isset ($this->_value))
    {
      return $this->_value;
    }
    
    return null;
  }

  /**
   * Convert the {@link value()} to text.
   * @param FORM $form Not used here, but used by descendents.
   * @param mixed $value Optional parameter used by some fields to distinguish between different components of the value.
   * @return string
   */
  public function as_text ($form, $value = null)
  {
    return $this->value ();
  }

  /**
   * Convert the {@link value()} to html.
   * Escapes special characters as HTML entities.
   * @param FORM $form Provides a context from which to read {@link
   * TEXT_OPTIONS} and convert HTML entities.
   * @param string $quote_style Can be "ENT_NOQUOTES" or "ENT_QUOTES", which
   * translates quotes or not, respectively.
   * @param mixed $value Optional parameter used by some fields to distinguish between different components of the value.
   * @return string
   */
  public function as_html ($form, $quote_style, $value = null)
  {
    return $form->context->text_options->convert_to_html_entities ($this->as_text ($form, $value), $quote_style);
  }

  /**
   * Does this field need validation?
   * This will return True only if the field has not already generated an error and it has content.
   * Descendents use this function to determine whether to continue validation or not.
   * @param FORM $form
   * @return boolean
   */
  public function continue_validating ($form)
  {
    return ! $this->is_empty () && ! $form->num_errors ($this->id);
  }

  /**
   * Set this field from a request array.
   * Sets the field to <code>null</code> if it is not in the array.
   * @param array[string] $values */
  public function set_value_from_request ($values)
  {
    if (isset ($values [$this->id]))
    {
      $this->set_value_from_text ($values [$this->id]);
    }
    else
    {
      $this->set_value_from_text (null);
    }
  }

  /**
   * Apply a new value to this field.
   * Value should always be in native format; the field manages internal value and display text. Use
   * {@link set_value_from_text()} if you have a string.
   * @var mixed $value
   */
  public function set_value ($value)
  {
    $this->_value = $value;
  }

  /**
   * Apply a text value to this field.
   * This is the value applied by a form submission. It is not in native format and needs to be transformed.
   * @var string $value
   */
  public function set_value_from_text ($value)
  {
    $this->_value = $value;
  }

  /**
   * Unique storage id within the given form.
   * Called from {@link store_to_client()} and {@link load_from_client()}.
   * @param FORM $form
   * @return string
   */
  public function storage_id_for ($form)
  {
    return $form->name . '_' . $this->id;
  }

  /**
   * Stores the current value to the client.
   * Uses the {@link CONTEXT::$storage} to record its value.
   * @var FORM $form
   * @param STORAGE $storage
   */
  public function store_to_client ($form, $storage)
  {
    $storage->set_value ($this->storage_id_for ($form), $this->as_text ($form));
  }

  /**
   * Loads a value from the 'storage' into the field.
   * @param FORM $form
   * @param STORAGE $storage
   * @param mixed $default Use this value if the client is empty.
   */
  public function load_from_client ($form, $storage, $default)
  {
    $key = $this->storage_id_for ($form);
    if ($storage->exists_on_client ($key))
    {
      $this->set_value_from_text ($storage->value ($key));
    }
    else
    {
      $this->set_value ($default);
    }
  }

  /**
   * Called by the form when adding this field to its list.
   * Override in descendents to implement custom behavior.
   * @param FORM $form
   */
  public function added_to_form ($form) {}

  /**
   * Actual value stored in this field.
   * @var string
   * @access private
   */
  protected $_value;
}

/**
 * Integer form validator.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class INTEGER_FIELD extends FIELD
{
  /**
   * Maximum valid value.
   * Used only if the variable is set.
   * @var integer
   */
  public $max_value;

  /**
   * Minimum valid value.
   * Used only if the variable is set.
   * @var integer
   */
  public $min_value;

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);
    if ($this->continue_validating ($form))
    {
      if ($this->_value && !is_numeric ($this->_value))
      {
        $form->record_error ($this->id, "Please enter a number for $this->title: [$this->_value] is not valid.");
      }
      else
      {
        if (isset ($this->min_value))
        {
          if (isset ($this->max_value))
          {
            if (! (($this->min_value <= $this->_value) && ($this->_value <= $this->max_value)))
            {
              $form->record_error ($this->id, "Please enter a number between $this->min_value and $this->max_value for $this->title");
            }
          }
          else
          {
            if ($this->_value < $this->min_value)
            {
              $form->record_error ($this->id, "Please enter a number greater than or equal to $this->min_value for $this->title");
            }
          }
        }
        else
        {
          if (isset ($this->max_value))
          {
            if ($this->_value > $this->max_value)
            {
              $form->record_error ($this->id, "Please enter a number less than or equal to $this->max_value for $this->title");
            }
          }
        }
      }
    }
  }
}

/**
 * Integer form validator.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @access private
 * @version 3.0.0
 * @since 2.7.0
 */
class FLOAT_FIELD extends INTEGER_FIELD
{
}

/**
 * Boolean form validator.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class BOOLEAN_FIELD extends FIELD
{
  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);
    if ($this->continue_validating ($form))
    {
      if (! ((0 <= $this->_value) && ($this->_value <= 1)))
      {
        $form->record_error ($this->id, "Please enter a boolean value (0 or 1) for $this->title");
      }
    }
  }

  /**
   * Return a '1' or a '0'.
   * @return integer
   */
  public function value ()
  {
    if ($this->is_empty () || ($this->_value === false) || ($this->_value === '0') || ($this->_value === 0))
    {
      return 0;
    }

    return 1;
  }
}

/**
 * Validates text input.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class TEXT_FIELD extends FIELD
{
  /**
   * Maximum valid length.
   * Used only if the variable is set.
   * @var integer
   */
  public $max_length = 0;

  /**
   * Minimum valid length.
   * @var integer
   */
  public $min_length = 0;

  /**
   * Perl-style regular expression against which the value must validate.
   * @var string
   */
  public $expression = '';

  /**
   * Help message explaining why the value didn't conform if the regular
   * expression fails.
   * @var string
   */
  public $expression_help = '';

  /**
   * Perform tag validation for content.
   * Text fields can be checked for compliance with the munger formatter. Errors
   * are listed with line and character number.
   * @var boolean
   */
  public $tag_validator_type = Tag_validator_none;

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);
    if ($this->continue_validating ($form))
    {
      $len = strlen ($this->_value);
      if ($this->max_length > 0)
      {
        if (! (($this->min_length <= $len) && ($len <= $this->max_length)))
        {
          $form->record_error ($this->id, "Please enter between $this->min_length and $this->max_length characters for $this->title");
        }
      }
      else
      {
        if ($len < $this->min_length)
        {
          $form->record_error ($this->id, "Please enter at least $this->min_length characters for $this->title");
        }
      }

      if ($this->expression && ! preg_match ($this->expression, $this->_value))
      {
        $form->record_error ($this->id, "$this->title $this->expression_help.");
      }

      if ($this->tag_validator_type != Tag_validator_none)
      {
        $tag_validator = $form->tag_validator ($this->tag_validator_type);
        $tag_validator->validate ($this->_value);
        if (sizeof ($tag_validator->errors))
        {
          foreach ($tag_validator->errors as $error)
          {
            $msg = sprintf ($error->message, $error->token->data ());
            $name = $this->js_name ();
            $line = $error->line_number;
            $from_col = $error->column;
            $to_col = $error->column + strlen ($error->token->data ());
            $js = "select_line_column_range (this.document.getElementById ('$name'), $line, $from_col, $line, $to_col)";
            $position = "$this->title [<a href=\"javascript:$js\">line $line, char $from_col</a>]";
            $form->record_error ($this->id, $position . ' ' . htmlspecialchars ($msg));
          }
        }
      }
    }
  }

  /**
   * Convert the {@link value()} to text.
   * Replaces all HTML entities with proper character equivalents and
   * counteracts the effects of PHP's {@link PHP_MANUAL#get_magic_quotes_gpc()}
   * setting, if necessary.
   * @param FORM $form
   * @param mixed $value Ignored.
   * @return string
   */
  public function as_text ($form, $value = null)
  {
    /* Get the final value. */
    $Result = $this->value ();
    /* Get rid of magic quotes, if necessary. */
    if (get_magic_quotes_gpc ())
    {
      $Result = stripslashes ($Result);
    }
    return $form->context->text_options->convert_from_html_entities ($Result);
  }
}

/**
 * Validates an email address.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.7.1
 * @access private
 */
class EMAIL_FIELD extends TEXT_FIELD
{
  /**
   * @var integer
   */
  public $max_length = 255;

  /**
   * @var integer
   */
  public $min_length = 5;

  /**
   * @var string
   */
  public $expression = '/^(.+)@(.+)\\.(.+)$/';

  /**
   * @var string
   */
  public $expression_help = 'must be a valid email address';
}

/**
 * Validates an item title.
 * Allows one non-white-space character or a string that doesn't begin or end
 * with white-space.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.7.1
 * @access private
 */
class TITLE_FIELD extends TEXT_FIELD
{
  /**
   * @var integer
   */
  public $max_length = 100;

  /**
   * @var integer
   */
  public $min_length = 1;

  /**
   * @var string
   */
  public $expression = '/^\S$|^\S[\S ]*\S$/';

  /**
   * @var string
   */
  public $expression_help = 'must start and end with a non-white-space character';
}

/**
 * Validates an internal or external URL.
 * Internal URLs can start with an alias; external URLs must have a protocol.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.7.1
 * @access private
 */
class URI_FIELD extends TEXT_FIELD
{
  /**
   * @var integer
   */
  public $max_length = 255;

  /**
   * @var integer
   */
  public $min_length = 1;

  /**
   * Not implemented.
   * @var string
   */
  public $expression = '';

  /**
   * @var string
   */
  public $expression_help = 'must be a valid URL';
}

/**
 * Validates multi-line {@link MUNGER} text.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class MUNGER_TEXT_FIELD extends TEXT_FIELD
{
  /**
   * Validates content as multi-line {@link MUNGER} text.
   * @var boolean
   */
  public $tag_validator_type = Tag_validator_multi_line;

  /**
   * @var integer
   */
  public $max_length = 65535;
}

/**
 * Validates single-line {@link MUNGER} text.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class MUNGER_STRING_FIELD extends TEXT_FIELD
{
  /**
   * Validates content as multi-line {@link MUNGER} text.
   * @var boolean
   */
  public $tag_validator_type = Tag_validator_single_line;
}

/**
 * Validates single-line {@link MUNGER} title.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class MUNGER_TITLE_FIELD extends TITLE_FIELD
{
  /**
   * Validates content as multi-line {@link MUNGER} text.
   * @var boolean
   */
  public $tag_validator_type = Tag_validator_single_line;
}

/**
 * Date-time form validator.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @access private
 */
class DATE_TIME_FIELD extends FIELD
{
  /**
   * Maximum date-time.
   * Used only if the variable is set. Do not set directly, use @see set_max_date instead.
   * @var DATE_TIME
   */
  public $max_date;

  /**
   * Minimum date-time.
   * Used only if the variable is set. Do not set directly, use @see set_min_date instead.
   * @var integer
   */
  public $min_date;

  /**
   * Can be {@link Date_time_time_part}, {@link Date_time_date_part} or {@link Date_time_both_parts}
   * @var integer
   */
  public $parts_to_convert = Date_time_both_parts;

  /**
   * Set the minimum date.
   * Class does not apply a minimum if this function is not called.
   * @param DATE_TIME $d
   * @param integer $type
   */
  public function set_min_date ($d, $type = Date_time_iso)
  {
    $this->min_date = new DATE_TIME ($d, $type);
  }

  /**
   * Set the maximum date.
   * Class does not apply a maximum if this function is not called.
   * @param DATE_TIME $d
   * @param integer $type
   */
  public function set_max_date ($d, $type = Date_time_iso)
  {
    $this->max_date = new DATE_TIME ($d, $type);
  }

  /**
   * Apply a new value to this field.
   * Value should always be in native format; the field manages internal value and display text. Use
   * {@link set_value_from_text()} if you have a string.
   * @var mixed $value
   */
  public function set_value ($value)
  {
    $this->_value = $value;

    if ($value->is_valid ())
    {
      $f = $value->formatter ();
      $f->type = $this->_date_format;
      $f->clear_flags ();
      $this->_text_value = $value->format ($f);
    }
    else
    {
      $this->_text_value = '';
    }
  }

  /**
   * Apply a text value to this field.
   * This is the value applied by a form submission. It is not in native format and needs to be transformed.
   * @var string $value
   */
  public function set_value_from_text ($value)
  {
    $this->_value->set_from_text ($value, $this->parts_to_convert);
    $this->_text_value = $value;
  }

  /**
   * Convert the {@link value()} to html.
   * @param FORM $form Not used.
   * @param mixed $value Optional parameter used by some fields to distinguish between different components of the value.
   * @return string
   */
  public function as_text ($form, $value = null)
  {
    return $this->_text_value;
  }

  /**
   * Is this field's value set?
   * @return boolean
   */
  public function is_empty ()
  {
    return ! isset ($this->_text_value) || ($this->_text_value === '');
  }

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);

    if ($this->continue_validating ($form))
    {
      if (! $this->_value->is_valid ())
      {
        $form->record_error ($this->id, "[$this->_text_value] is not a valid date/time.");
      }
      else
      {
        $date = $this->_value;
        if (isset ($this->max_date))
        {
          if (isset ($this->min_date))
          {
            if ($date->less_than_equal ($this->min_date) || $this->max_date->less_than_equal ($date))
            {
              $form->record_error ($this->id, 'Please enter a date between '.$this->min_date->as_iso ().' and '.$this->max_date->as_iso ()." for $this->title");
            }
          }
          else
          {
            if ($this->max_date->less_than_equal ($date))
            {
              $form->record_error ($this->id, 'Please enter a date less than '.$this->max_date->as_iso ()." for $this->title");
            }
          }
        }
        else
        {
          if (isset ($this->min_date))
          {
            if ($date->less_than_equal ($this->min_date))
            {
              $form->record_error ($this->id, 'Please enter a date greater than '.$this->min_date->as_iso ()." for $this->title");
            }
          }
        }
      }
    }
  }

  /**
   * Called by the form when adding this field to its list.
   * Override in descendents to implement custom behavior.
   * @param FORM $form
   */
  public function added_to_form ($form)
  {
    $this->_value = $form->context->make_date_time ();
    $form->page->add_script_file ('{scripts}webcore_calendar.js');
  }

  /**
   * @var string
   * @access private
   */
  protected $_date_format = Date_time_format_short_date_and_time;

  /**
   * @var string
   * @access private
   */
  protected $_text_value = '';
}

/**
 * Declare a field that is only a date.
 * Accepts three formats for dates:
 * Y-m-d (ISO 8601, international standard)
 * d.m.Y (DIN 5008 / german, deprecated)
 * m/d/Y (american colloquial, deprecated)
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class DATE_FIELD extends DATE_TIME_FIELD
{
  /**
   * Can be {@link Date_time_time_part}, {@link Date_time_date_part} or {@link Date_time_both_parts}
   * @var integer
   */
  public $parts_to_convert = Date_time_date_part;

  /**
   * @var string
   * @access private
   */
  protected $_date_format = Date_time_format_short_date;
}

/**
 * Array form validator.
 * {@link ENUMERATED_FIELD} is a similar list field that enforces a set of values.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @access private
 */
class ARRAY_FIELD extends FIELD
{
  /**
   * Maximum valid number of values.
   * Used only if the variable is set.
   * @var integer
   */
  public $max_values;

  /**
   * Minimum valid number of values.
   * @var integer
   */
  public $min_values = 0;

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);
    if ($this->continue_validating ($form))
    {
      $size = sizeof ($this->_value);
      if (isset ($this->min_values))
      {
        if (isset ($this->max_values))
        {
          if (! (($this->min_values <= $size) && ($size <= $this->max_values)))
          {
            $form->record_error ($this->id, "Please enter between $this->min_values and $this->max_values values for $this->title");
          }
        }
        else
        {
          if ($size < $this->min_values)
          {
            $form->record_error ($this->id, "Please enter at least $this->min_values values for $this->title");
          }
        }
      }
      else
      {
        if ($size > $this->max_values)
        {
          $form->record_error ($this->id, "Please enter at most $this->max_values for $this->title");
        }
      }
    }
  }

  /**
   * Convert the {@link value()} to text.
   * All elements are returned as a comma-separated list.
   * @param FORM $form Not used.
   * @param integer $value Ignored in this field.
   * @return string
   */
  public function as_text ($form, $value = null)
  {
    if (isset ($this->_value))
    {
      if (is_array ($this->_value))
      {
        return join (',', trim_array ($this->_value));
      }

      return $this->_value;
    }
    
    return null;
  }

  /**
   * Apply a text value to this field.
   * @var string $value
   */
  public function set_value_from_text ($value)
  {
    if (! $value)
    {
      $this->_value = array ();
    }
    elseif (! is_array ($value))
    {
      $this->_value = explode (',', $value);
    }
    else
    {
      $this->_value = $value;
    }
  }

  /**
   * Transform the id to a valid JavaScript id.
   * field names ending in [] are automatically marshalled to arrays by PHP.
   * @return string
   */
  public function js_name ()
  {
    return "$this->id[]";
  }

  /**
   * Is this field selected?
   * Returns whether the value is in the array.
   * @param mixed $value
   * @return boolean
   */
  public function selected ($value)
  {
    return in_array ($value, $this->_value);
  }
}

/**
 * Enumerated form validator.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class ENUMERATED_FIELD extends FIELD
{
  /**
   * Add a valid key to this field.
   * @param mixed $val
   */
  public function add_value ($val)
  {
    $this->_values [$val] = 1;
  }

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    if (! sizeof ($this->_values))
    {
      $form->raise ('ENUMERATED_FIELD', 'validate', 'no values assigned to enumerated type.');
    }

    parent::validate ($form);
    if ($this->continue_validating ($form))
    {
      if (! isset ($this->_values [$this->_value]))
      {
        $flat = join (', ', array_keys ($this->_values));
        $form->record_error ($this->id, "Please enter one of [$flat] for $this->title");
      }
    }
  }

  /**
   * Stores the valid values.
   * @see ENUMERATED_FIELD::add_value()
   * @var array
   * @access private
   */
  protected $_values = array ();
}

/**
 * Manages an uploaded file.
 * @see FORM
 * @package webcore
 * @subpackage forms-core
 * @since 2.5.0
 * @access private
 */
class UPLOAD_FILE_FIELD extends FIELD
{
  /**
   * Maximum number of bytes.
   * Each {@link FORM} can enforce a maximum file size; the form calculates that value as the
   * largest of 'max_bytes' for all the upload fields it has. There is no per-field limit, by
   * default.
   * @var integer
   */
  public $max_bytes = 0;

  /**
   * Upload files do not accept initial values.
   * @var mixed $value
   */
  public function set_value ($value)
  {
    $this->raise ('Illegal operation.', 'set_value', 'UPLOAD_FILE_FIELD');
  }

  /**
   * Upload files do not accept initial values.
   * @var string $value
   */
  public function set_value_from_text ($value)
  {
    if (isset ($this->_uploader->file_sets [$this->id]))
    {
      $this->_value = $this->_uploader->file_sets [$this->id];
    }
  }

  /**
   * Convert the {@link value()} to text.
   * Returns a result only if 'value' is set.
   * @param FORM $form Not used.
   * @param mixed $value 'Value' must be an integer, indexing into the list of
   * files associated with this field.
   * @return string
   */
  public function as_text ($form, $value = null)
  {
    if (isset ($value) && isset ($this->_value))
    {
      return $this->_value->files [$value]->name;
    }
    
    return '';
  }

  /**
   * Transform the id to a valid JavaScript id.
   * field names ending in [] are automatically marshalled to arrays by PHP.
   * @return string
   */
  public function js_name ()
  {
    return "$this->id[]";
  }

  /**
   * Has this upload been successfully processed?
   * @param integer $idx
   * @return boolean
   */
  public function is_processed ($idx)
  {
    $file = $this->file_at ($idx);
    return (isset ($file) && $file->is_valid () && ! $this->_form->num_errors ($this->id, $idx) && $file->processed);
  }

  /**
   * An uploaded file from this field's set.
   * Only valid after the form has been submitted.
   * @param integer $idx
   * @return UPLOADED_FILE
   */
  public function file_at ($idx)
  {
    if (isset ($this->_value))
    {
      return $this->_value->files [$idx];
    }

    global $Null_reference;
    return $Null_reference;
  }

  /**
   * How many files are contained in the upload?
   * @return integer
   */
  public function num_files ()
  {
    if (isset ($this->_value))
    {
      return $this->_value->size ();
    }
    
    return 0;
  }

  /**
   * Is this field's value set?
   * @return boolean
   */
  public function is_empty ()
  {
    return ! isset ($this->_value) || ! $this->_value->size ();
  }

  /**
   * Move all files to the new location.
   * Only valid after the form has been submitted.
   * @param string $path
   * @param string $options Can be {@link Uploaded_file_unique_name} or {@link Uploaded_file_overwrite}.
   */
  public function move_files_to ($path, $options = Uploaded_file_unique_name)
  {
    $this->_value->move_to ($path, $options);
  }

  /**
   * @var FORM $form
   */
  public function validate ($form)
  {
    parent::validate ($form);

    if ($this->continue_validating ($form))
    {
      $idx = 0;
      foreach ($this->_value->files as $file)
      {
        if (! $file->is_valid ())
        {
          if ($this->required || ($file->error != Uploaded_file_error_missing))
          {
            $form->record_error ($this->id, $file->error_message (), $idx);
          }
        }
        else
        {
          if ($this->max_bytes && ($file->size > $this->max_bytes))
          {
            $form->record_error ($this->id, "$this->title can be at most ".file_size_as_text ($this->max_bytes).'.', $idx);
          }
        }

        $idx++;
      }
    }
  }

  /**
   * Called by the form when adding this field to its list.
   * Override in descendents to implement custom behavior.
   * @param FORM $form
   */
  public function added_to_form ($form)
  {
    $this->_uploader = $form->uploader ();
    $this->_form = $form;
    $form->_add_upload_field ($this);
  }

  /**
   * @var UPLOADER
   * @access private
   */
  protected $_uploader;

  /**
   * @var FORM
   * @access private
   */
  protected $_form;
}

?>