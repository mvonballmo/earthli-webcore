<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @access private
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
require_once ('webcore/forms/controls_renderer.php');

/**
 * Properties for an item in a list.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @access private
 */
class FORM_LIST_ITEM
{
  /**
   * Title to display.
   * @var string
   */
  public $title = '';

  /**
   * Value of item posted with the form.
   * @var string
   */
  public $value;

  /**
   * Is this item enabled?
   * This property is not used by all control renderers. If the item is disabled, it is not selectable.
   * @var boolean
   */
  public $enabled = true;

  /**
   * Additional non-label text.
   * This text is formatted with the item, but is not enclosed within the label for the control. This
   * makes it possible to add other controls and larger texts to the item.
   * @var string
   */
  public $text = '';

  /**
   * Descriptive text displayed after the item.
   * This text is not included in the label for the control and is renderered only by the list box in
   * a paragraph following the control and title.
   */
  public $description = '';

  /**
   * Script used by this item (if applicable).
   * Some items, like checkboxes or radio buttons can have their own click scripts.
   * If this property is set, it will be used. Otherwise, the script from the list properties is used.
   * @var string
   */
  public $on_click_script = null;

  /**
   * Wraps text to the right of the control only.
   * If this is false, text can wrap underneath the control. If True, the control is placed in a block
   * to the left of another block, which contains the text. Overrides the value set in the list properties.
   * @var boolean
   */
  public $smart_wrapping = false;
}

/**
 * Properties for a check box.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.6.0
 * @access private
 */
class CHECK_BOX_ITEM extends FORM_LIST_ITEM
{
  /**
   * Value of item posted with the form.
   * @var string
   */
  public $value = 1;
}

/**
 * Set of items to show in a list.
 * These items are used with {@FORM_RENDERER::draw_list_box()},
 * {@FORM_RENDERER::draw_drop_down()} and {@FORM_RENDERER::draw_radio_group()}
 * to describe the individual items in the list. Make sure to set {@link
 * $show_descriptions} to true if you pass descriptions to {@link add_item()}.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @access private
 */
class FORM_LIST_PROPERTIES
{
  /**
   * How wide should the list control be?
   * Should be specified in legal CSS units. Used by the drop-down box and the list-box.
   * @var string
   */
  public $width = null;

  /**
   * How many items at once should be shown?
   * Used by the list-box.
   * @var integer
   */
  public $height = 5;

  /**
   * JavaScript to call when the control is clicked or changed.
   * Used by radio buttons and checkboxes when clicked; used by drop-downs when changed.
   * @var string
   */
  public $on_click_script = null;

  /**
   * List of items for this control.
   * Do not add items to this list directly. Use {@link add_item()} instead.
   * @var array [FORM_LIST_ITEM]
   * @see FORM_LIST_ITEM
   */
  public $items = array ();

  /**
   * How many items to show one one line.
   * Only some control types will use this.
   * @var integer
   */
  public $items_per_row = 1;

  /**
   * CSS value for the amount of space between rows of items.
   * This property is ignored if 'show_descriptions' is True, since a default spacing is used there
   * to offset the description.
   * @var string
   */
  public $line_spacing = '';

  /**
   * Shows the list with descriptions.
   * Only by the radio and check button renderers. If this is True, then
   * {@link $items_per_row} is ignored. Ignored in the list box renderer.
   * and assumed to be equal to one.
   * @var boolean
   */
  public $show_descriptions = false;

  /**
   * Should the description immediately follow the control?
   * If the control has a description, it will be displayed immediately after the control,
   * wrapping under it if it is too long. If this value is False, the description is always
   * placed under the control. Some controls may ignore this flag.
   * @var boolean
   */
  public $show_description_on_same_line = false;

  /**
   * Style to use for the list item title.
   * Used only if {@link $show_descriptions} is True.
   * @see $description_class
   * @var string name of a CSS class.
   */
  public $item_class = '';

  /**
   * Style to use for list item descriptions.
   * Used only if {@link $show_descriptions} is True.
   * @var string name of a CSS class.
   */
  public $description_class = 'notes';

  /**
   * Wraps text to the right of the control only.
   * If this is false, text can wrap underneath the control. If True, the control is placed in a block
   * to the left of another block, which contains the text.
   * @var boolean
   */
  public $smart_wrapping = false;

  /**
   * Extra CSS class to apply to the control.
   * Control will still have the class "menu-control", but this class is applied
   * afterwards.
   */
  public $CSS_class = '';

  /**
   * Add an item to the list.
   * These items are renderered by specific controls using the values given. {@link add_item_object()}
   * does the same with an existing {@link FORM_LIST_ITEM} object.
   * @param string $title Display text. Used by all controls.
   * @param string $value Value submitted with form data. Used by all controls.
   * @param string $description Displayed if {@link $show_descriptions} is True.
   * @param boolean $enabled Is the control for this item enabled? Used only by radio buttons and checkboxes.
   * @param string $text Additional text to display outside of the label.
   * @param string $script Called when the item is clicked. Overrides the {@link $on_click_script} for this object.
   * @see FORM_LIST_ITEM
   */
  public function add_item ($title, $value, $description = '', $enabled = true, $text = '', $script = '')
  {
    $item = new stdClass();
    $item->title = $title;
    $item->value = $value;
    $item->enabled = $enabled;
    $item->text = $text;
    $item->description = $description;
    $item->on_click_script = $script;
    $item->smart_wrapping = false;
    $this->items [] = $item;
  }

  /**
   * Add an item to the list.
   * These items are then renderered by specific controls, using the values given.
   * @see add_item()
   * @param FORM_LIST_ITEM $item
   */
  public function add_item_object ($item)
  {
    $this->items [] = $item;
  }

  /**
   * Replace an existing item in the list.
   * These items are then renderered by specific controls, using the values given.
   * @param $index Zero-based index of item to replace.
   * @param $title Display text. Used by all controls.
   * @param $value Value submitted with form data. Used by all controls.
   * @param $text Additional text to display outside of the label.
   * @param $enabled Is the control for this item enabled? Used only by radio buttons and checkboxes.
   * @see FORM_LIST_ITEM
   */
  public function replace_item ($index, $title, $value, $enabled = true, $text = '')
  {
    $item = $this->items [$index];
    $item->title = $title;
    $item->value = $value;
    $item->enabled = $enabled;
    $item->text = $text;
  }

  /**
   * Reset the item list.
   */
  public function clear_items ()
  {
    $this->items = null;
  }
}

/**
 * Passed as a parameter to several rendering functions in {@link FORM_RENDERER}.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @access private
 */
class FORM_TEXT_CONTROL_OPTIONS
{
  /**
   * How wide should the control be (in CSS units)?
   * Defaults to {@link FORM_RENDERER::$default_control_width} if not specified.
   * @var string
   */
  public $width = null;

  /**
   * Should the description immediately follow the control?
   * If the control has a description, it will be displayed immediately after the control,
   * wrapping under it if it is too long. If this value is False, the description is always
   * placed under the control. Some controls may ignore this flag.
   * @var boolean
   */
  public $show_description_on_same_line = false;

  /**
   * Text to add to the description of the field.
   * This text will be merged with the the {@link FIELD}'s description when displayed.
   * @var string
   */
  public $extra_description = '';

  /**
   * Javascript to call when the control's contents change.
   * @var string
   */
  public $on_change_script = null;

  /**
   * Extra CSS class to apply to the control.
   * Control will still have the class "text-control", but this class is
   * applied afterwards.
   */
  public $CSS_class = '';
}

/**
 * Helper class for painting {@link FORM}s.
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.5.0
 * @access private
 */
class FORM_RENDERER extends CONTROLS_RENDERER
{
  /**
   * Is the form centered in its parent?
   * @var boolean
   */
  public $centered = true;

  /**
   * Name of the icon to use for the 'required' mark.
   * Preferred over {@link required_text}.
   * @var string
   */
  public $required_icon = '{icons}indicators/required_16px';

  /**
   * Text to use to mark a field as 'required'.
   * Used only if {@link required_icon} is empty.
   * @var string
   */
  public $required_text = '*';

  /**
   * How wide should controls be by default?
   * Should be specified in legal CSS units. Used by all text controls and the drop-down box.
   * @var string
   */
  public $default_control_width = '35em';

  /**
   * How high should controls be by default?
   * Should be specified in legal CSS units. Used only by the text-area.
   * @var string
   */
  public $default_control_height = '8em';

  /**
   * How width should date only controls be by default?
   * Should be specified in legal CSS units. Only the {@link date_as_html()} and
   * {@link draw_date_row()} functions use this default.
   * @see $default_date_time_width;
   * @var string
   */
  public $default_date_width = '8em';

  /**
   * How width should date/time controls be by default?
   * Should be specified in legal CSS units. Only the {@link date_as_html()} and
   * {@link draw_date_row()} functions use this default.
   * @var string
   */
  public $default_date_time_width = '12em';

  /**
   * Should the form ensure that all form values are submitted?
   * Disabled fields in an HTML form do not submit values. This can often make validation and
   * updating logic more complicated or impossible. If this value is True, the form will re-enable
   * all disabled fields using JavaScript before submitting.
   * @var boolean
   */
  public $submit_all_fields = 1;

  /**
   * How wide should the form be?
   * Should be specified in legal CSS units.
   * @var string
   */
  public $width = '35em';

  /**
   * Spacing between rows and columns.
   * @var integer
   */
  public $spacing = 0;

  /**
   * Padding inside each row.
   * @var integer
   */
  public $padding = 2;

  /**
   * Show the icon for required fields?
   * @var boolean
   */
  public $show_required_mark = true;

  /**
   * Should this renderer allow previewing?
   * @var boolean
   */
  public $preview_enabled = false;

  /**
   * Should this renderer allow drafts?
   * @var boolean
   */
  public $drafts_enabled = false;

  /**
   * @param FORM $form
   */
  public function __construct ($form)
  {
    parent::__construct ($form->context);
    $this->_form = $form;
    $this->default_control_properties = new FORM_LIST_ITEM ();
  }

  /**
   * Restrict the width of the generated form.
   * Applied widths are stored in a stack and can be restored with {@link
   * restore_width()}.
   * @param string $css_width
   */
  public function set_width ($css_width)
  {
    /* Store the current values. */
    $stored_width = new stdClass();
    $stored_width->width = $this->width;
    $stored_width->control_width = $this->default_control_width;
    $this->_widths [] = $stored_width;

    /* Apply the new width */
    $this->default_control_width = $css_width;
    $this->width = $css_width;
  }

  /**
   * Restore width settings changed by {@link set_width()}.
   * Should be paired with a call to {@link set_width()}.
   */
  public function restore_width ()
  {
    $stored_width = array_pop ($this->_widths);
    $this->default_control_width = $stored_width->width;
    $this->width = $stored_width->control_width;
  }

  /**
   * Return a helper class for creating lists.
   * @return FORM_LIST_PROPERTIES
   */
  public function make_list_properties ()
  {
    return new FORM_LIST_PROPERTIES ();
  }

  /**
   * Return a helper class for formatting check boxes.
   * @return CHECK_BOX_ITEM
   */
  public function make_check_properties ($id = '')
  {
    $Result = new CHECK_BOX_ITEM ();
    $Result->title = $id;
    return $Result;
  }

  /**
   * Draw any scripts needed by the renderer.
   * Should only be called by the {@link FORM}. Specific forms need never call this function.
   */
  public function draw_scripts ()
  {
  }

  /**
   * Start rendering the form.
   * All rendering calls are now valid.
   */
  public function start ()
  {
    $this->_required_mark_used = false;
    $this->_column_started = false;

    if ($this->centered)
    {
      $style [] = 'margin: auto';
    }
    if (isset ($this->width) && $this->width)
    {
      $style [] = 'width: ' . $this->width;
    }

    if (sizeof ($style) > 0)
    {
      $style = implode ('; ', $style);
      echo '<table cellspacing="' . $this->spacing . '" cellpadding="' . $this->padding . '" class="form-' . $this->_form->CSS_class . '" style="' . $style . '">' . "\n";
    }
    else
    {
      echo '<table cellspacing="' . $this->spacing . '" cellpadding="' . $this->padding . '">' . "\n";
    }

    if ($this->_form->num_errors (Form_general_error_id))
    {
      $this->draw_error_row (Form_general_error_id, '');
      $this->draw_separator ();
    }
  }

  /**
   * Finish rendering the form.
   * You should no longer call any of the row, column, block or field rendering functions.
   */
  public function finish ()
  {
    if ($this->_required_mark_used)
    {
      $this->draw_separator ();
?>
  <tr>
    <td></td>
    <td><?php echo $this->required_mark (); ?> Required fields</td>
  </tr>
<?php
    }

    echo "</table>\n";
  }

  /**
   * Start a new row without closing it.
   * Use this feature to be able to draw multiple fields into a row. Must be closed with {@link finish_row()}.
   * If the title is empty, the row is generated with a label area, which places the content area beneath the
   * labels for any rows already created in this block. To force a label area to be generated, pass ' ' as the
   * title.
   * @param string $title Label for the row.
   * @param string $CSS_class CSS class assigned to the row's content container.
   */
  public function start_row ($title = '', $CSS_class = '')
  {
    if (! $CSS_class)
    {
      $CSS_class = $this->_form->CSS_class;
    }

    if ($title)
    {
?>
  <tr>
    <td class="form-<?php echo $CSS_class; ?>-label"><?php echo $title; ?></td>
    <td class="form-<?php echo $CSS_class; ?>-content">
<?php
    }
    else
    {
?>
  <tr>
    <td colspan="2" class="form-<?php echo $CSS_class; ?>-content">
<?php
    }
  }

  /**
   * Close a row opened with {@link start_row()}.
   */
  public function finish_row ()
  {
?>
    </td>
  </tr>
<?php
  }

  /**
   * Starts a hideable/hidden area in the form.
   * @param string $id Unique name of the layer
   * @param string $title Title displayed for the layer row
   * @param string $description Text displayed next to the title; formatted
   * using {@link PHP_MANUAL#sprintf} to build the final string; make sure to
   * include %s somewhere in the string so the instructions for toggling the
   * layer are included (e.g. "These are only for advanced users. %s advanced
   * options." generates "These are only for advanced users. Use the arrow to
   * the left to show advanced options.").
   * @param boolean $visible Whether to initially display the layer or not
   * @param boolean $styled Whether to apply a special style to the generated
   * block inside the layer.
   * @return LAYER Pass this layer to {@link finish_layer_row()} to close it.
   * @see finish_layer_row()
   */
  public function start_layer_row ($id, $title, $description, $visible = false, $styled = true)
  {
    $browser = $this->env->browser ();
    $use_layer = $browser->supports (Browser_DHTML);

    if ($use_layer)
    {
      $Result = $this->context->make_layer ($id);
      $Result->visible = $visible;
      $toggle = $Result->toggle_as_html ();
      if (! empty ($toggle))
      {
        $title .= ' ' . $toggle;
      }
      $description = sprintf ($description, 'Use the arrow to the left to show ');
    }
    else
    {
      $description = sprintf ($description, 'Shown below are ');
      $Result = null;
    }

    $this->draw_text_row ($title, $description, 'notes');
    $this->start_row (' ');
    if (isset ($Result))
    {
      $Result->start ();
    }
    $this->start_block ($styled);

    return $Result;
  }

  /**
   * Closes a hideable/hidden area in the form.
   * Should be paired with {@link start_layer_row()}.
   * @see start_layer_row()
   */
  public function finish_layer_row ($layer)
  {
    $this->finish_block ();
    if (isset ($layer))
    {
      $layer->finish ();
    }
    $this->finish_row ();
  }

  /**
   * Add a spacer row to the form.
   */
  public function draw_separator ()
  {
?>
  <tr>
    <td colspan="2" class="form-<?php echo $this->_form->CSS_class; ?>-separator">&nbsp;</td>
  </tr>
<?php
  }

  /**
   * Draw a block of text in the form.
   * This does its best to keep the sizing vis-a-vis other controls correct.
   * @param $title.
   * @param $text Text to display.
   * @param $class CSS class used for text.
   */
  public function draw_text_row ($title, $text, $CSS_class = '')
  {
    if (! $CSS_class)
    {
      $CSS_class = "form-" . $this->_form->CSS_class . "-content";
    }

    if ($title)
    {
  ?>
  <tr>
    <td class="form-<?php echo $this->_form->CSS_class; ?>-label"><?php echo $title; ?></td>
    <td class="<?php echo $CSS_class; ?>" <?php if (! empty($this->width)) { ?>style="width: <?php echo $this->width; ?>"<?php } ?>>
      <?php echo $text; ?>
    </td>
  </tr>
  <?php
    }
    else
    {
  ?>
  <tr>
    <td colspan="2" class="<?php echo $CSS_class; ?>" <?php if (! empty($this->width)) { ?>style="width: <?php echo $this->width; ?>"<?php } ?>>
      <?php echo $text; ?>
    </td>
  </tr>
  <?php
    }
  }

  /**
   * Draw a caution message in a form.
   * This does its best to keep the sizing vis-a-vis other controls correct.
   * @param $title.
   * @param $text Text to display.
   */
  public function draw_caution_row ($title, $text)
  {
    $this->draw_text_row ($title, $this->app->resolve_icon_as_html ('{icons}indicators/warning', 'Warning', '16px') . ' ' . $text, 'caution detail');
  }

  /**
   * Draw errors for a control onto a separate row in the form.
   * @param string $id Name of field.
   */
  public function draw_error_row ($id, $title = ' ', $width = '')
  {
    if (! $width)
    {
      $width = $this->width;
    }

    if (! $this->_form->num_errors ($id) && isset ($this->_num_controls [$id]))
    {
      $test_id = $id . ($this->_num_controls [$id] - 1);
      if ($this->_form->num_errors ($test_id))
      {
        $id = $test_id;
      }
    }

    if ($this->_form->num_errors ($id))
    {
      if ($title)
      {
  ?>
  <tr>
    <td class="form-<?php echo $this->_form->CSS_class; ?>-label"><?php echo $title; ?></td>
    <td class="form-<?php echo $this->_form->CSS_class; ?>-content" style="width: <?php echo $width; ?>"><?php $this->_form->draw_errors ($id); ?></td>
  </tr>
  <?php
      }
      else
      {
  ?>
  <tr>
    <td colspan="2" class="form-<?php echo $this->_form->CSS_class; ?>-content" style="width: <?php echo $width; ?>"><?php $this->_form->draw_errors ($id); ?></td>
  </tr>
  <?php
      }
    }
  }

  /**
   * Open a nested content area in the form.
   * Use this feature to create hierarchical 'sub-forms'. Any rows added to the form
   * are added to blocks nested within the content area of the last open row. Should only be called when
   * there is already a row opened with {@link start_row()}. Must be closed with {@link finish_block()}.
   * @param boolean $styled Applies a style using {@link FORM::$CSS_class} as
   * a base (may make the block visible).
   * @param string $width A CSS style for the width to use within the block.
   * Calls {@link set_width()} with the given value.
   */
  public function start_block ($styled = false, $width = null)
  {
    if ($styled)
    {
      $css_class = ' class="form-' . $this->_form->CSS_class . '-block"';
    }
    else
    {
      $css_class = '';
    }
    echo '<table width="100%" cellspacing="' . $this->spacing . '" cellpadding="' . $this->padding . '"' . $css_class . '>' . "\n";

    if (isset ($width))
    {
      $this->set_width ($width);
    }
  }

  /**
   * Close a nested content area in the form.
   * Must be paired with {@link start_block()}.
   * @param boolean $restore_width Calls {@link restore_width()} if True. Set
   * to True only if a width was passed to {@link start_block()}.
   */
  public function finish_block ($restore_width = false)
  {
    echo "</table>\n";
    if ($restore_width)
    {
      $this->restore_width ();
    }
  }

  /**
   * Starts a left indent.
   * To be used only when {@link start_row()} has already been called. Call {@link finish_indent()}
   * before calling {@link finish_row()} to maintain proper structure. These are useful when placing
   * controls after a radio button, so that the controls appear to "belong" to that selection.
   * @param string $size CSS dimension controlling amount of indent
   * @see finish_indent()
   */
  public function start_indent ($size = '2em')
  {
    echo "<div style=\"margin-left: $size\">\n";
  }

  /**
   * Closes a left indent.
   * To be used only when {@link start_indent()} has already been called.
   * @see start_indent()
   */
  public function finish_indent ($size = '2em')
  {
    echo "</div>\n";
  }

  /**
   * Start a new set of columns in an open block.
   * Starting a column block automatically starts a new row, so {@link start_row()} should not be
   * called first. Successive calls to this function will create new column content areas until
   * {@link finish_column()} is called to finish the column block.
   */
  public function start_column ($title = '')
  {
    if (! $this->_column_started)
    {
      $this->_column_started = true;
      $this->start_row ($title);
  ?>
    <table cellspacing="0" cellpadding="0">
      <tr>
        <td style="vertical-align: top; padding-right: 1em">
  <?php
      $this->start_block ();
    }
    else
    {
      $this->finish_block ();
  ?>
        </td>
        <td style="vertical-align: top; padding-right: 1em">
  <?php
      $this->start_block ();
    }
  }

  /**
   * Stop generating columns in this content block.
   * Must follow at least one call to {@link start_column()}.
   */
  public function finish_column ()
  {
    $this->_column_started = false;
    $this->finish_block ();
?>
        </td>
      </tr>
    </table>
<?php
    $this->finish_row ();
  }

  /**
   * Draw a single-line text control onto a separate row in the form.
   * @param string $id
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   */
  public function draw_text_line_row ($id, $options = null, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_line_as_html ($id, $options), $title);
  }

  /**
   * Draw a single-line password onto a separate row in the form.
   * @param string $id
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   */
  public function draw_password_row ($id, $options = null, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->password_as_html ($id, $options), $title);
  }

  /**
   * Draw a validating date control onto a separate row in the form.
   * @param string $id
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   */
  public function draw_date_row ($id, $options = null, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->date_as_html ($id, $options), $title);
  }

  /**
   * Draw a file upload control onto a separate row in the form.
   * @param string $id
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @param string $title Override the title; used instead of the field title
   */
  public function draw_file_row ($id, $options = null, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->file_as_html ($id, $options), $title);
  }

  /**
   * Render a text-box field onto a separate row in the form.
   * @param string $id Name of field.
   * @param string $width Width of the control in valid CSS. Defaults to {@link
   * $default_control_width} if not specified.
   * @param string $height Height of the control in valid CSS. Defaults to
   * {@link $default_control_height} if not specified.
   * @param string $title Override the title; used instead of the field title
   */
  public function draw_text_box_row ($id, $width = null, $height = null, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_box_as_html ($id, $width, $height), $title);
  }

  /**
   * Draw a group of radio buttons onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   * @param string $title Override the title; used instead of the field title
   */
  public function draw_radio_group_row ($id, $props, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->radio_group_as_html ($id, $props), $title);
  }

  /**
   * Draw a drop-down menu onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   * @param string $title Override the title; used instead of the field title
   */
  public function draw_drop_down_row ($id, $props, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->drop_down_as_HTML ($id, $props), $title);
  }

  /**
   * Draw a list box onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   */
  public function draw_list_box_row ($id, $props, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->list_box_as_HTML ($id, $props), $title);
  }

  /**
   * Draw a list of checkboxes in a separate row in the form.
   * Each item in the list of properties is interpreted as 'id => value' rather than 'title => value',
   * as in other uses of the property list. The title from the field is used for each checkbox and each
   * has its own on/off value.
   * @param string $title Title to show for the group
   * @param string $props
   * @param string $value Value assigned to the checkbox.
   */
  public function draw_check_boxes_row ($title, $props)
  {
    $control_text = $this->check_boxes_as_HTML ($props);
    if (! empty ($control_text))
    {
      $this->start_row ($title);
      echo $control_text;
      $this->finish_row ();
      foreach ($props->items as $item)
      {
        $field = $this->_field_at ($item->title);
        if ($field->visible)
        {
          $this->draw_error_row ($field->id);
        }
      }
    }
  }

  /**
   * Draw a list of checkboxes in a separate row in the form.
   * @param string $id
   * @param string $on_click JavaScript to execute when clicked.
   * @param string $value Value assigned to the checkbox.
   */
  public function draw_check_group_row ($id, $props, $title = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->check_group_as_HTML ($id, $props), $title);
  }

  /**
   * Draw a checkbox onto a separate row in the form.
   * @param string $id
   * @param CHECK_BOX_ITEM $item Properties used to render this check box.
   */
  public function draw_check_box_row ($id, $item = null, $title = '')
  {
    $field = $this->_field_at ($id);
    if (! isset ($item))
    {
      $item = $this->make_check_properties ();
      $item->smart_wrapping = true;
    }

    if ($field->description)
    {
      $item->title .= ' <span class="notes">' . $field->description . '</span>';
    }

    $ctrl = $this->_grouped_control_as_HTML ($field, null, $item, 'checkbox', $field->id);
    $ctrl = $this->_control_created ($id, $ctrl);
    $this->_draw_field_row ($field, $ctrl, $field->id);
  }

  /**
   * Draw a button to submit the form.
   * 'show_preview' is used if set; otherwise, 'preview' will only be shown if {@link $preview_enabled} is True.
   * @param boolean $show_preview
   */
  public function draw_submit_button_row ($show_preview = null)
  {
    if ($this->drafts_enabled)
    {
      $buttons [] = $this->submit_button_as_html ('Publish', '{icons}buttons/ship', 'save_as_visible');
    }
    else
    {
      $buttons [] = $this->submit_button_as_html ();
    }

    if (! isset ($show_preview))
    {
      $show_preview = $this->preview_enabled;
    }
    if ($show_preview)
    {
      $buttons [] = $this->submit_button_as_html ('Preview', '{icons}buttons/view', 'preview_form');
    }

    if ($this->drafts_enabled)
    {
      $buttons [] = $this->submit_button_as_html ('Save as draft', '{icons}buttons/save', 'save_as_draft');
      $buttons [] = $this->submit_button_as_html ('Quick Save', '{icons}buttons/quick_save', 'quick_save_and_reload');
    }

    $this->draw_buttons_in_row ($buttons);
  }

  public function start_button_row ($title = ' ')
  {
    $this->start_row ($title, 'button');
  }

  /**
   * Draw an icon text/browse button row.
   * @param string $field_id Name of the field for the text control.
   */
  public function draw_icon_browser_row ($field_id)
  {
    $this->start_row ('Icon');
      echo $this->text_line_as_HTML ($field_id);
    $this->finish_row ();
    $button = $this->javascript_button_as_HTML ('Browse...', $field_id . '_field.show_picker ()', '{icons}buttons/browse');
    $this->draw_buttons_in_row (array ($button));
    $this->draw_error_row ($field_id);
  }

  /**
   * Return HTML for a single-line text field.
   * Commonly used to render {@link TEXT_FIELD}s and {@link INTEGER_FIELD}s.
   * @param string $id Name of field.
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @return string
   */
  public function text_line_as_html ($id, $options = null)
  {
    return $this->_text_line_as_html ($id, 'text', $options);
  }

  /**
   * Return HTML for a password field.
   * Commonly used to render {@link TEXT_FIELD}s.
   * @param string $id Name of field.
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @return string
   */
  public function password_as_html ($id, $options = null)
  {
    return $this->_text_line_as_html ($id, 'password', $options);
  }

  /**
   * Return HTML for a date field.
   * Commonly used to render {@link DATE_FIELD}s and {@link DATE_TIME_FIELD}s. Since these fields
   * don't need as much room, the default width is {@link $default_date_width} instead of {@link $default_control_width}.
   * If the field includes the time, it uses {@link $default_date_time_width}.
   * @param string $id Name of field.
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @return string
   */
  public function date_as_html ($id, $options = null)
  {
    $field = $this->_field_at ($id);
    $includes_time = $field->parts_to_convert & Date_time_time_part;

    if (! isset ($options))
    {
      $options = clone(default_text_options ());
      if ($includes_time)
      {
        $options->width = $this->default_date_time_width;
      }
      else
      {
        $options->width = $this->default_date_width;
      }
    }

    $Result = $this->text_line_as_html ($id, $options);

    if (isset ($Result))
    {
      $js_form = $this->_form->js_form_name ();

      $Result .= "<script type=\"text/javascript\">\n";
      $Result .= 'var ' . $id . "_field = new WEBCORE_DATE_TIME_FIELD ();\n";
      $Result .= $id . "_field.output_format = Date_format_us;\n";

      $field = $this->_field_at ($id);
      if ($includes_time)
      {
        $Result .= $id . "_field.show_time = true;\n";
      }

      $Result .= $id . '_field.attach (' . $js_form . '.' . $id . ");\n";

      $Result .= "</script>\n";
      $Result .= ' <a href="javascript:' . $id . '_field.show_calendar ()">' . $this->context->resolve_icon_as_html ('{icons}buttons/calendar', 'Show calendar in popup window', '16px') . '</a>';
      $Result .= ' ' . $this->context->resolve_icon_as_html ('{icons}indicators/info', 'Use [d.m.Y] or [m/d/Y] or [Y-m-d]', '16px', 'vertical-align: middle; cursor: help');

      return $Result;
    }
    
    return '';
  }

  /**
   * Return HTML for a multi-line text box.
   * @param string $id Name of field.
   * @param string $width Width of the control in valid CSS. Defaults to {@link $default_control_width} if not specified.
   * @param string $height Height of the control in valid CSS. Defaults to {@link $default_control_height} if not specified.
   * @return string
   */
  public function text_box_as_html ($id, $width = null, $height = null)
  {
    $field = $this->_field_at ($id);

    if ($field->visible)
    {
      if (! isset ($width))
      {
        $width = $this->default_control_width;
      }
      if (! isset ($height))
      {
        $height = $this->default_control_height;
      }

      if ($field->required)
      {
        $CSS_class = "text-control-required";
      }
      else
      {
        $CSS_class = "text-control";
      }

      $Result = $this->_start_control ($field, 'textarea');
      $Result .= ' class="' . $CSS_class . '" rows="10" cols="25" style="width: ' . $width . '; height: ' . $height . '">';
      $Result .= $this->_to_html ($field, ENT_NOQUOTES) . '</textarea>';

      if ($field->description)
      {
        $text = $field->description;
      }
      else
      {
        $text = '';
      }

      if ($field->tag_validator_type != Tag_validator_none)
      {
        $text .= ' Find out more about <a href="text_formatting.php">supported tags and formatting</a>.';
      }

      if ($text)
      {
        $text = '<div class="notes">' . $text . '</div>';
      }

      $Result .= '<div style="width: ' . $width . '">' . $text . '</div>';

      return $this->_control_created ($id, $Result);
    }
    
    return '';
  }

  /**
   * Return HTML for a check box.
   * @param string $id
   * @param CHECK_BOX_ITEM $item Properties used to render this check box.
   * @return string
   */
  public function check_box_as_html ($id, $item = null)
  {
    if (! isset ($item))
    {
      $item = new stdClass();
      $item->text = '';
      $item->on_click_script = null;
      $item->value = 1;
      $item->title = $id;
      $item->enabled = true;
      $item->smart_wrapping = false;
    }
    else
    {
      $item->title = $id;
    }

    $props = $this->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item_object ($item);
    return $this->check_boxes_as_html ($props);
  }

  /**
   * Return HTML for a list of check boxes.
   * @param FORM_LIST_PROPERTIES
   * @return string
   */
  public function check_boxes_as_html ($props)
  {
    return $this->_control_group_as_html ('', $props, 'checkbox');
  }

  /**
   * Return HTML for a group of check boxes.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @return string
   */
  public function check_group_as_html ($id, $props)
  {
    return $this->_control_group_as_HTML ($id, $props, 'checkbox');
  }

  /**
   * Return HTML for a group of radio buttons.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @return string
   */
  public function radio_group_as_html ($id, $props)
  {
    return $this->_control_group_as_HTML ($id, $props, 'radio');
  }

  /**
   * Return HTML for a drop-down list.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @return string
   */
  public function drop_down_as_html ($id, $props)
  {
    $field = $this->_field_at ($id);

    if ($field->visible)
    {
      if ($field->required)
      {
        $CSS_class = "menu-control-required";
      }
      else
      {
        $CSS_class = "menu-control";
      }

      if ($props->CSS_class)
      {
        $CSS_class .= ' ' . $props->CSS_class;
      }

      $Result = $this->_start_control ($field, 'select') . ' class="' . $CSS_class . '"';

      if (isset ($props->on_click_script))
      {
        $Result .= ' onChange="' . $props->on_click_script . '"';
      }
      if (isset ($props->width))
      {
        $Result .= ' style="width: ' . $props->width . '"';
        $width = $props->width;
      }
      else
      {
        $width = $this->default_control_width;
      }

      $Result .= '>';

      $selected_value = $field->value ();

      foreach ($props->items as $item)
      {
        $Result .= '<option value="' . $item->value . '"';
        if ($selected_value == $item->value)
        {
          $Result .= ' selected';
        }
        $Result .= '>' . $item->title . $item->text . "</option>\n";
      }

      $Result .= '</select>';

      if ($field->description)
      {
        if ($props->show_description_on_same_line)
        {
          $Result .= ' <span class="notes">' . $field->description . '</span>';
        }
        else
        {
          $Result .= '<div style="width: ' . $width . '"><div class="notes">' . $field->description . '</div></div>';
        }
      }

      return $this->_control_created ($id, $Result);
    }
    
    return '';
  }

  /**
   * Return HTML for a list-box.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @return string
   */
  public function list_box_as_html ($id, $props)
  {
    $field = $this->_field_at ($id);

    if ($field->visible)
    {
      if ($field->required)
      {
        $CSS_class = "menu-control-required";
      }
      else
      {
        $CSS_class = "menu-control";
      }

      $Result = $this->_start_control ($field, 'select') . ' class="' . $CSS_class . '"';

      if (isset ($props->on_click_script))
      {
        $Result .= ' onChange="' . $props->on_click_script . '"';
      }

      if (isset ($props->width))
      {
        $width = $props->width;
      }
      else
      {
        $width = $this->default_control_width;
      }

      $Result .= 'style="width: ' . $width . '"';
      $Result .= 'size="' . $props->height . '"';
      $Result .= '>';

      $selected_value = $field->value ();

      foreach ($props->items as $item)
      {
        $Result .= '<option value="' . $item->value . '"';
        if ($selected_value == $item->value)
        {
          $Result .= ' selected';
        }
        if (! $item->enabled)
        {
          $Result .= ' disabled';
        }
        $Result .= '>' . $item->title . $item->text . "</option>\n";
      }

      $Result .= '</select>';

      if ($field->description)
      {
        $Result .= '<div style="margin-top: .5em; width: ' . $width . '"><div class="notes">' . $field->description . '</div></div>';
      }

      return $this->_control_created ($id, $Result);
    }
    
    return '';
  }

  /**
   * Return HTML for a file upload control.
   * If the file has already been uploaded and processed, the name is shown instead of a control
   * so that the user doesn't have to upload again if there were validation errors elsewhere.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @return string
   */
  public function file_as_html ($id, $options = null)
  {
    $field = $this->_field_at ($id);

    if (! isset ($options))
    {
      $options = clone(default_text_options ());
    }

    if (isset ($options->width))
    {
      $width = $options->width;
    }
    else
    {
      $width = $this->default_control_width;
    }

    if (! isset ($this->_num_controls [$id]))
    {
      $this->_num_controls [$id] = 0;
    }

    if ($field->is_processed ($this->_num_controls [$id]))
    {
      $file = $field->file_at ($this->_num_controls [$id]);
      $ft = $this->context->file_type_manager ();
      $url = new FILE_URL ($file->name);
      $icon = $ft->icon_as_html ($file->mime_type, $url->extension (), '16px');

      $Result = '<div style="width: ' . $width . '"><div class="detail">' . $icon . ' ' . $file->name . ' (' . file_size_as_text ($file->size) . ")</div></div>\n";

      $file_info = $file->store_to_text ($id);
      $uploader = $this->_form->uploader ();
      $Result .= '<input type="hidden" name="'. $uploader->stored_info_name . '[]" value="' . $file_info . "\">\n";

      if ($field->description)
      {
        $Result .= '<div style="width: ' . $width . '"><div class="notes">' . $field->description . "</div></div>\n";
      }
    }
    else
    {
      $max_size = $this->_form->max_upload_file_size ();
      if ($field->max_bytes)
      {
        $max_size = min ($field->max_bytes, $max_size);
      }

      $desc = 'Maximum file size is ' . file_size_as_text ($max_size) . '. ';

      $saved_desc = $options->extra_description;
      $options->extra_description .= $desc;

      $Result = $this->_text_line_as_html ($id, 'file', $options);

      $options->extra_description = $saved_desc;
    }

    $this->_num_controls [$id] += 1;
    return $Result;
  }

  /**
   * Return HTML for a submitting button.
   * @param string $title Name on the button.
   * @param string $script Name of the JavaScript function to execute (must conform to 'function(form: form; submit_all_fields: boolean; submit_field_name, preview_field_name: string)').
   * @return string
   */
  public function submit_button_as_html ($title = null, $icon = null, $script = null)
  {
    if (! isset ($title))
    {
      $title = $this->_form->button;
      if ($this->_form->button_icon)
      {
        $icon = $this->_form->button_icon;
      }
    }

    return parent::submit_button_as_html ($title, $icon, $script);
  }

  /**
   * Add a hidden field to the form.
   * @param string $id
   */
  public function draw_hidden ($id)
  {
    $field = $this->_form->field_at ($id);
    $value = $field->value ();
    $name = $field->js_name ();
    if (is_array ($value))
    {
      foreach ($value as $val)
      {
        $this->draw_hidden_value ($name, $val);
      }
    }
    else
    {
      $this->draw_hidden_value ($name, $field->as_text ($this->_form));
    }
  }

  /**
   * Add a hidden value to the form.
   * @param string $name
   * @param string $value
   */
  public function draw_hidden_value ($name, $value)
  {
    echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars ($value) . "\">\n";
  }

  /**
   * Draw the field in the form.
   * This routine will display the field, honoring the required, enabled, visible, etc. properties.
   * Field-specific errors is also drawn here, if necessary.
   * @param FIELD $field
   * @param string $control_text Prepared string of text; should be HTML code for a form control.
   * @param string $dom_id If set, makes an HTML label around the title with that id.
   * @access private
   */
  protected function _draw_field_row ($field, $control_text, $dom_id = '', $title = null)
  {
    if ($field->visible)
    {
      if ($field->required)
      {
        $CSS_class = "form-" . $this->_form->CSS_class . "-required";
      }
      else
      {
        $CSS_class = "form-" . $this->_form->CSS_class . "-label";
      }
?>
  <tr>
    <td class="<?php echo $CSS_class; ?>">
      <?php
        if (isset ($title))
        {
          $t = $title;
        }
        else
        {
          $t = $field->title;
        }
        if (! empty ($t))
        {
          if ($dom_id)
          {
            echo '<label for="' . $dom_id . '">';
          }
          echo $t;
          if ($dom_id)
          {
            echo '</label>';
          }
          if ($field->required && $this->show_required_mark)
          {
            echo $this->required_mark ();
          }
        }
      ?>
    </td>
    <td class="form-<?php echo $this->_form->CSS_class; ?>-content">
      <?php echo $control_text; ?>
    </td>
  </tr>
  <?php
      $this->draw_error_row ($field->id);
    }
  }

  /**
   * Open an HTML tag for the given field.
   * Maps the name and id of the control so that it is uniformly available as JavaScript (automatically
   * mapping {@link ARRAY_FIELD}s). Also renders the field's enabled state.
   * @param FIELD $field
   * @param string $tag_name Name of the HTML tag to generate.
   * @access private
   */
  protected function _start_control ($field, $tag_name = 'input', $dom_id = null)
  {
    if (isset ($dom_id))
    {
      $id = $dom_id;
    }
    else
    {
      $id = $field->id;
    }
    $Result = '<' . $tag_name . ' name="' . $field->js_name () . '" id="' . $id . '"';
    if (! $field->enabled)
    {
      $Result .= ' disabled';
    }
    return $Result;
  }

  /**
   * Return the contents of the field 'id' as HTML.
   * Text values have to have characters properly escaped before inserting them into text inputs or areas.
   * @param FIELD $field
   * @param string $quote_style Can be "ENT_NOQUOTES" or "ENT_QUOTES", which
   * translates quotes or not, respectively.
   * @return string
   * @access private
   */
  protected function _to_html ($field, $quote_style)
  {
    if (isset ($this->_num_controls [$field->id]))
    {
      return $field->as_html ($this->_form, $quote_style, $this->_num_controls [$field->id]);
    }

    return $field->as_html ($this->_form, $quote_style);
  }

  /**
   * Called when a control is created by the renderer.
   * @param string $id Id of the control in the form.
   * @param string $text HTML for the control.
   * @param boolean $can_be_focussed Indicate whether to use this control as initial focus if none is already set.
   * @return string
   */
  protected function _control_created ($id, $text, $can_be_focused = true)
  {
    if (! empty ($id))
    {
      if ($can_be_focused && ! $this->_form->initial_focus ())
      {
        $this->_form->set_initial_focus ($id);
      }
    }
    return $text;
  }

  /* Text indicating that a field is required.
   * @return string
   * @access private
   */
  public function required_mark ()
  {
    $this->_required_mark_used = true;

    if ($this->required_icon)
    {
      $Result = $this->context->resolve_icon_as_html ($this->required_icon, $this->required_text);
    }
    else
    {
      $Result = $this->required_text;
    }

    return $Result;
  }

  /**
   * Return the requested field.
   * @param string $id
   * @see FORM::field_at
   * @return FIELD
   * @access private
   */
  protected function _field_at ($id)
  {
    return $this->_form->field_at ($id);
  }

  /**
   * Return a generic text control as HTML.
   * @param string $id Name of the field to generated.
   * @param string $type Type of text input to create ('text', 'file' and 'password' are accepted).
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @see text_line_as_html
   * @see password_as_html
   * @see date_as_html
   * @see file_as_html
   * @return string
   * @access private
   */
  protected function _text_line_as_html ($id, $type, $options)
  {
    $field = $this->_field_at ($id);

    if (! isset ($options))
    {
      $options = default_text_options ();
    }

    if (isset ($options->width))
    {
      $width = $options->width;
    }
    else
    {
      $width = $this->default_control_width;
    }

    $Result = $this->_start_control ($field);
    if (isset ($field->max_length) && ($field->max_length > 0))
    {
      $Result .= ' maxlength="' . $field->max_length . '"';
    }

    if ($field->required)
    {
      $CSS_class = "text-control-required";
    }
    else
    {
      $CSS_class = "text-control";
    }

    if ($options->CSS_class)
    {
      $CSS_class .= ' ' . $options->CSS_class;
    }

    if (isset ($options->on_change_script))
    {
      $Result .= ' OnChange=\'' . $options->on_change_script . '\'';
    }

    $Result .= ' type="' . $type . '" class="' . $CSS_class . '" value="' . $this->_to_html ($field, ENT_QUOTES) . '" style="width: ' . $width . '">';

    if ($field->description || $options->extra_description)
    {
      $desc = $field->description . ' ' . $options->extra_description;

      if ($options->show_description_on_same_line)
      {
        $Result .= ' <span class="notes">' . $desc . '</span>';
      }
      else
      {
        $Result .= '<div style="width: ' . $width . '"><div class="notes">' . $desc . '</div></div>';
      }
    }

    return $this->_control_created ($id, $Result);
  }

  /**
   * Return HTML for a group of controls.
   * @param string $id Name of field.
   * @param FORM_LIST_PROPERTIES $props
   * @param string $type Can be 'checkbox' or 'radio'.
   * @return string
   * @access private
   */
  protected function _control_group_as_html ($id, $props, $type)
  {
    if ($id)
    {
      $field = $this->_field_at ($id);
    }

    if (! isset ($field) || $field->visible)
    {
      $Result = '';

      if ($id)
      {
        if (isset ($this->_num_controls [$id]))
        {
          $counter = $this->_num_controls [$id] + 1;
        }
        else
        {
          $counter = 0;
        }
      }
      else
      {
        $counter = 0;
      }

      if (isset ($props->width))
      {
        $width = $props->width;
      }
      else
      {
        $width = $this->width;
      }

      $num_items = sizeof ($props->items);

      $needs_descriptions = $props->show_descriptions;
      if ($needs_descriptions)
      {
        $needs_descriptions = false;
        foreach ($props->items as $item)
        {
          if ($id)
          {
            $needs_descriptions = ! empty ($item->description);
          }
          else
          {
            $field = $this->_field_at ($item->title);
            $needs_descriptions = ! empty ($field->description);
          }
          if ($needs_descriptions) break;
        }
      }

      foreach ($props->items as $item)
      {
        $counter += 1;

        if (! $id)
        {
          $field = $this->_field_at ($item->title);
          $item = clone($item);
          $item->title = $field->title;
          $item->description = $field->description;
          $item->enabled = $field->enabled;
          $dom_id = $field->id;
        }
        else
        {
          $dom_id = $field->id . $counter;
        }

        if ($field->visible)
        {
          $ctrl = $this->_grouped_control_as_HTML ($field, $props, $item, $type, $dom_id);
          if (! $id)
          {
            $ctrl = $this->_control_created ($id, $ctrl, false);
          }

          if ($needs_descriptions)
          {
            $Result .= '<dt';
            if ($props->item_class)
            {
              $Result .= ' class="' . $props->item_class . '"';
            }
            $Result .= '>' . $ctrl . "</dt>\n<dd";
            if ($props->description_class)
            {
              $Result .= ' style="margin-left: 2em" class="' . $props->description_class . '"';
            }
            $Result .= '><label for="' . $dom_id . '">' . $item->description . "</label></dd>\n";
          }
          else
          {
            if ($props->item_class)
            {
              $ctrls [] = '<span class="' . $props->item_class . '">' . $ctrl . '</span>';
            }
            else
            {
              $ctrls [] = $ctrl;
            }

            if (($counter == $num_items) || ($props->items_per_row == 1) || (($counter % $props->items_per_row) == 0))
            {
              $ctrl_string = implode ("\n", $ctrls);
              $ctrls = null;

              /* Avoid making an extra block if the controls never wrapped. */

              if (($Result == '') && ($counter == $num_items))
              {
                $Result .= $ctrl_string . "\n";
              }
              else
              {
                if ($props->line_spacing)
                {
                  if ($Result != '')
                  {
                    $Result .= '<div style="margin-top: ' . $props->line_spacing . '">' . $ctrl_string . "</div>\n";
                  }
                  else
                  {
                    $Result = '<div>' . $ctrl_string . "</div>\n";
                  }
                }
                else
                {
                  $Result .= '<div>' . $ctrl_string . "</div>\n";
                }
              }
            }
          }
        }
      }

      if ($needs_descriptions)
      {
        if ($width)
        {
          $Result = '<dl style="margin:0px; width: ' . $width . "\">" . $Result . "</dl>\n";
        }
        else
        {
          $Result = '<dl style="margin:0px">' . $Result . "</dl>\n";
        }
      }

      if ($id)
      {
        $this->_num_controls [$id] = $counter;

        if ($field->description)
        {
          $Result .= '<div style="margin-left: 2em; margin-top: .5em; width: ' . $width . '"><div class="notes">' . $field->description . '</div></div>';
        }

        $Result = $this->_control_created ($id, $Result, false);
      }

      return $Result;
    }
    
    return '';
  }

  /**
   * Renders a checkbox or radio buttion with a label.
   * Used by {@link _control_group_as_HTML()} and {@link draw_check_box_row()} to generate
   * the raw control text.
   * @param FIELD $field Render the control for this field.
   * @param FORM_LIST_PROPERTIES $props Control is rendered in this list (may be null).
   * @param FORM_LIST_ITEM $item Settings to apply for the rendered item.
   * @param string $type Type of control: "checkbox" or "radio".
   * @param string $id to use for the label and control.
   * @return string
   * @access private
   */
  protected function _grouped_control_as_html ($field, $props, $item, $type, $dom_id)
  {
    if ($field->visible)
    {
      $Result = $this->_start_control ($field, 'input', $dom_id);
      $Result .= ' type="' . $type . '" value="' . $item->value . '"';

      if ($type == 'checkbox')
      {
        $Result .= ' class="check-box-control"';
      }
      elseif ($type == 'radio')
      {
        $Result .= ' class="radio-control"';
      }

      if (! $item->enabled && $field->enabled)
      {
        $Result .= ' disabled';
      }

      if (! empty ($item->on_click_script))
      {
        $Result .= ' onClick="' . $item->on_click_script . '"';
      }
      elseif (isset ($props) && ! empty ($props->on_click_script))
      {
        $Result .= ' onClick="' . $props->on_click_script . '"';
      }

      if ($field->selected ($item->value))
      {
        $Result .= ' checked';
      }

      $Result .= '>';
      $ctrl = $Result;
      $label = '<label for="' . $dom_id . '">' . $item->title . '</label>' . $item->text;
      if (((isset ($props) && $props->smart_wrapping) || $item->smart_wrapping) && ($item->title || $item->text))
      {
        $box = $this->context->make_box_renderer ();
        ob_start ();
          $box->start_column_set ();
          $box->new_column ('padding-right: .5em');
          echo $ctrl;
          $box->new_column ();
          echo $label;
          $box->finish_column_set ();
        $Result = ob_get_contents ();
        ob_end_clean ();
      }
      else
      {
        $Result = $ctrl . ' ' . $label;
      }

      return $Result;
    }
    
    return '';
  }

  /**
   * @var FORM
   * @access private
   */
  protected $_form;

  /**
   * Set if the required mark is generated during form construction.
   * The renderer will generate a legend explaining the required mark if the mark
   * is generated at least once during form construction.
   * @var boolean
   * @access private
   */
  protected $_required_mark_used = false;

  /**
   * Currently building columns.
   * @var boolean
   * @access private
   */
  protected $_column_started = false;

  /**
   * Table of used DOM ids.
   * If radio buttons or other group controls are rendered to different parts of a page for the
   * same field, this structure remembers which DOM id was last used for the given field.
   * @var array[string, integer]
   * @access private
   */
  protected $_num_controls = array ();

  /**
   * Stack of widths applied with {@link set_width()}.
   * @var array[STORED_WIDTH]
   * @access private
   */
  protected $_widths;
}

/**
 * Used by the {@link FORM_RENDERER} to stack width settings.
 * @see FORM::set_width()
 * @package webcore
 * @subpackage forms-core
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class STORED_WIDTH
{
  /**
   * Stored value for {@link FORM_RENDERER::$width}.
   * @var string
   */
  public $width;

  /**
   * Stored value for {@link FORM_RENDERER::$default_control_width}.
   * @var string
   */
  public $control_width;
}

/**
 * Returns the global default text options.
 * Used when no options are passed to a rendering function. Returns a reference, so that changes
 * can be made permanently.
 * @return FORM_TEXT_CONTROL_OPTIONS
 * @access private
 */
function default_text_options ()
{
  global $_g_default_text_options;
  if (! isset ($_g_default_text_options))
  {
    $_g_default_text_options = new FORM_TEXT_CONTROL_OPTIONS ();
  }
  return $_g_default_text_options;
}

/**
 * Cached copy of text options.
 * Accessed using {@link default_text_options()}.
 * @global FORM_TEXT_CONTROL_OPTIONS
 * @access private
 */
$_g_default_text_options = new FORM_TEXT_CONTROL_OPTIONS ();

?>
