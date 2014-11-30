<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
 * @since 2.5.0
 * @access private
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
require_once ('webcore/forms/controls_renderer.php');

/**
 * Properties for an item in a list.
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
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
   * This text is not included in the label for the control and is rendered only by the list box in
   * a paragraph following the control and title.
   *
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
   * The class to use for the line generated for this list item (if rendered as a form row).
   * @var string
   */
  public $css_class = '';
}

/**
 * Properties for a check box.
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
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
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class FORM_LIST_PROPERTIES
{
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
   * @var FORM_LIST_ITEM[]
   */
  public $items = array ();

  /**
   * How many items to show one one line.
   * Only some control types will use this.
   * @var integer
   */
  public $items_per_row = 1;

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
   * Extra CSS class to apply to the control.
   */
  public $css_class = '';

  /**
   * Add an item to the list.
   * These items are rendered by specific controls using the values given. {@link add_item_object()}
   * does the same with an existing {@link FORM_LIST_ITEM} object.
   * @param string $title Display text. Used by all controls.
   * @param string $value Value submitted with form data. Used by all controls.
   * @param string $description Displayed if {@link $show_descriptions} is True.
   * @param boolean $enabled Is the control for this item enabled? Used only by radio buttons and checkboxes.
   * @param string $text Additional text to display outside of the label.
   * @param string $script Called when the item is clicked. Overrides the {@link $on_click_script} for this object.
   * @return \FORM_LIST_ITEM
   * @see FORM_LIST_ITEM
   */
  public function add_item ($title, $value, $description = '', $enabled = true, $text = '', $script = '')
  {
    $item = new FORM_LIST_ITEM();
    $item->title = $title;
    $item->value = $value;
    $item->enabled = $enabled;
    $item->text = $text;
    $item->description = $description;
    $item->on_click_script = $script;
    $this->items [] = $item;

    return $item;
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
   * These items are then rendered by specific controls, using the values given.
   * @param $index int Zero-based index of item to replace.
   * @param $title string Display text. Used by all controls.
   * @param $value string Value submitted with form data. Used by all controls.
   * @param $text string Additional text to display outside of the label.
   * @param $enabled boolean Is the control for this item enabled? Used only by radio buttons and checkboxes.
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
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class FORM_TEXT_CONTROL_OPTIONS
{
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
   * applied afterwards.
   */
  public $css_class = '';
}

/**
 * Helper class for painting {@link FORM}s.
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class FORM_RENDERER extends CONTROLS_RENDERER
{
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
  public $default_date_css_class = 'small';

  /**
   * How width should date/time controls be by default?
   * Should be specified in legal CSS units. Only the {@link date_as_html()} and
   * {@link draw_date_row()} functions use this default.
   * @var string
   */
  public $default_date_time_css_class = 'small-medium';

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
  public $width = 'auto';

  public $labels_css_class = 'ltr right';

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
   * If enabled, {@link draw_submit_button_row()} generates calls use the inline preview.
   * @var boolean
   */
  public $inline_operations_enabled = false;

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
   * Return a helper class for creating lists.
   * @return FORM_LIST_PROPERTIES
   */
  public function make_list_properties ()
  {
    return new FORM_LIST_PROPERTIES ();
  }

  /**
   * Return a helper class for formatting check boxes.
   * @param string $id
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

  public function start_inline_preview_area()
  {
    echo '<div class="form-with-preview">';
  }

  public function finish_inline_preview_area($height_class = 'medium-tall')
  {
    echo '<div class="inline-preview-block preview text-flow">';
    echo '<div class="fixed-header">';
    echo '<div class="preview-title">Preview</div>';
    echo '<div class="inline-preview-message"></div>';
    echo '</div>';
    echo '<div class="' . $height_class . ' inline-preview"></div>';
    echo '</div>';
    echo '</div>';
  }

  /**
   * Start rendering the form.
   * All rendering calls are now valid.
   */
  public function start ()
  {
    $this->_text_control_was_rendered = false;

    echo '<div class="' . $this->_form->css_class . '-form ' . $this->labels_css_class . '">' . "\n";

    if ($this->_form->num_errors (Form_general_error_id))
    {
      $this->draw_error_row (Form_general_error_id, '');
    }
  }

  /**
   * Finish rendering the form.
   * You should no longer call any of the row, column, block or field rendering functions.
   */
  public function finish ()
  {
    echo "</div>\n";
  }

  /**
   * Start a new row without closing it.
   * Use this feature to be able to draw multiple fields into a row. Must be closed with {@link finish_row()}.
   * If the title is empty, the row is generated with a label area, which places the content area beneath the
   * labels for any rows already created in this block. To force a label area to be generated, pass ' ' as the
   * title.
   * @param string $title Label for the row.
   * @param string $css_class
   */
  public function start_row ($title = '', $css_class = '')
  {
    if ($title && !ctype_space($title))
    {
?>
  <div class="form-row<?php if ($css_class) echo ' ' . $css_class; ?>">
    <label><?php echo $title; ?></label>
<?php
    }
    else
    {
?>
    <div class="form-row<?php if ($css_class) echo ' ' . $css_class; ?>">
<?php
    }
  }

  /**
   * Close a row opened with {@link start_row()}.
   */
  public function finish_row ()
  {
?>
  </div>
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
   * @return LAYER Pass this layer to {@link finish_layer_row()} to close it.
   * @see finish_layer_row()
   */
  public function start_layer_row ($id, $title, $description, $visible = false)
  {
    if ($this->context->dhtml_allowed())
    {
      $Result = $this->context->make_layer ($id);
      $Result->visible = $visible;
      $toggle = $Result->toggle_as_html ();
      $description = sprintf ($description, 'Use the arrow to the left to show ');
    }
    else
    {
      $description = sprintf ($description, 'Shown below are ');
      $Result = null;
    }

    $this->start_block ($title, 'toggle');
    if (isset($toggle))
    {
?>
      <span class="toggle">
        <?php echo $toggle; ?>
      </span>
<?php
    }

    ?>
    <span class="text">
      <?php echo $description; ?>
    </span>
    <?php
    if (isset ($Result))
    {
      $Result->start ();
    }
    ?>
    <div class="content">
    <?php

    return $Result;
  }

  /**
   * Closes a hideable/hidden area in the form.
   * Should be paired with {@link start_layer_row()}.
   * @param LAYER $layer
   * @see start_layer_row()
   */
  public function finish_layer_row ($layer)
  {
    echo '</div>';
    if (isset ($layer))
    {
      $layer->finish ();
    }
    $this->finish_block ();
  }

  /**
   * Draw a block of text in the form.
   * This does its best to keep the sizing vis-a-vis other controls correct.
   * @param $title string The title to use for the row.
   * @param $text string The text to display.
   * @param string $css_class The class to use for the content box.
   * @internal param string $class CSS class used for text.
   */
  public function draw_text_row ($title, $text, $css_class = '')
  {
    if (! $css_class)
    {
      $css_class = $this->_form->css_class . "form--content";
    }

?>
  <div class="form-row">
<?php
    if ($title && !ctype_space($title))
    {
?>
    <label><?php echo $title; ?></label>
<?php
    }
?>
    <div class="<?php echo $css_class; ?>">
      <?php echo $text; ?>
    </div>
  </div>
  <?php
  }

  /**
   * Draw a caution message in a form.
   * This does its best to keep the sizing vis-a-vis other controls correct.
   * @param string $title.
   * @param string $text Text to display.
   */
  public function draw_caution_row ($title, $text)
  {
    $this->draw_text_row ($title, $this->app->resolve_icon_as_html ('{icons}indicators/warning', Sixteen_px, 'Warning') . ' ' . $text, 'caution detail');
  }

  /**
   * Draw errors for a control onto a separate row in the form.
   * @param string $id Name of field.
   * @param string $title
   */
  public function draw_error_row ($id, $title = ' ')
  {
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
?>
  <div class="form-row">
  <?php
      if ($title && !ctype_space($title))
      {
    ?>
    <label><?php echo $title; ?></label>
    <?php
      }

      $this->_form->draw_errors ($id);
    ?>
  </div>
<?php
    }
  }

  /**
   * Open a nested content area in the form.
   * Use this feature to create hierarchical 'sub-forms'. Any rows added to the form
   * are added to blocks nested within the content area of the last open row. Should only be called when
   * there is already a row opened with {@link start_row()}. Must be closed with {@link finish_block()}.
   */
  public function start_block ($title, $css_class = '')
  {
    if ($css_class)
    {
      echo '<fieldset class="' . $css_class . '">' . "\n";
    }
    else
    {
      echo '<fieldset>' . "\n";
    }

    if ($title)
    {
      echo '<legend>' . $title . '</legend>';
    }
  }

  /**
   * Close a nested content area in the form.
   * Must be paired with {@link start_block()}.
   */
  public function finish_block ()
  {
    echo "</fieldset>\n";
  }

  /**
   * Starts a left indent.
   * To be used only when {@link start_row()} has already been called. Call {@link finish_indent()}
   * before calling {@link finish_row()} to maintain proper structure. These are useful when placing
   * controls after a radio button, so that the controls appear to "belong" to that selection.
   * @see finish_indent()
   */
  public function start_indent ()
  {
    echo "<div class=\"group\">\n";
  }

  /**
   * Closes a left indent.
   *
   * To be used only when {@link start_indent()} has already been called.
   *
   * @see start_indent()
   */
  public function finish_indent ()
  {
    echo "</div>\n";
  }

  /**
   * Draw a single-line text control onto a separate row in the form.
   * 
   * @param string $id The id of the field to render.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   */
  public function draw_text_line_row ($id, $options = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_line_as_html ($id, $options), 'text-line');
  }

  /**
   * Draw a single-line text control with a 'browse' button onto a separate row in the form.
   *
   * @param string $id The id of the field to render.
   * @param string $button The button to show next to the control.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   * @param string $css_class The class to apply to the overall container.
   */
  public function draw_text_line_with_button_row($id, $button, $options = null, $css_class = 'browse')
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_line_with_button_as_html($id, $button, $options, $css_class), 'text-line');
  }

  /**
   * Draw a single-line text control with a 'browse' button onto a separate row in the form.
   *
   * @param string $id The id of the field to render.
   * @param string $browse_script The script to execute from the browse button.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   */
  public function draw_text_line_with_browse_button_row($id, $browse_script, $options = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_line_with_browse_button_as_html($id, $browse_script, $options), 'text-line');
  }

  /**
   * Draw a single-line password onto a separate row in the form.
   * 
   * @param string $id The id of the field to render.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   */
  public function draw_password_row ($id, $options = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->password_as_html ($id, $options), 'text-line');
  }

  /**
   * Draw a validating date control onto a separate row in the form.
   * 
   * @param string $id The id of the field to render.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   */
  public function draw_date_row ($id, $options = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->date_as_html ($id, $options), 'text-line');
  }

  /**
   * Draw a file upload control onto a separate row in the form.
   * 
   * @param string $id The id of the field to render.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   */
  public function draw_file_row ($id, $options = null)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->file_as_html ($id, $options), 'text-line');
  }

  /**
   * Render a text-box field onto a separate row in the form.
   * 
   * @param string $id The id of the field to render.
   * @param string $css_class
   */
  public function draw_text_box_row ($id, $css_class = '')
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->text_box_as_html ($id, $css_class), 'text-line');
  }

  /**
   * Draw a group of radio buttons onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   */
  public function draw_radio_group_row ($id, $props)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->radio_group_as_html ($id, $props));
  }

  /**
   * Draw a drop-down menu onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   */
  public function draw_drop_down_row ($id, $props)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->drop_down_as_HTML ($id, $props), 'text-line');
  }

  /**
   * Draw a list box onto a separate row in the form.
   * @param string $id
   * @param FORM_LIST_PROPERTIES $props
   */
  public function draw_list_box_row ($id, $props)
  {
    $this->_draw_field_row ($this->_field_at ($id), $this->list_box_as_HTML ($id, $props), 'text-line');
  }

  /**
   * Draw a list of checkboxes in a separate row in the form.
   * Each item in the list of properties is interpreted as 'id => value' rather than 'title => value',
   * as in other uses of the property list. The title from the field is used for each checkbox and each
   * has its own on/off value.
   * @param string $title Title to show for the group
   * @param FORM_LIST_PROPERTIES $props
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
   * @param string $id The id of the field to render as a check box.
   * @param $props FORM_LIST_PROPERTIES The properties that represent the check boxes to render.
   */
  public function draw_check_group_row ($id, $props)
  {
    echo $this->check_group_as_HTML ($id, $props);

    $field = $this->_field_at($id);
    $this->draw_error_row ($field->id);
  }

  /**
   * Draw a checkbox onto a separate row in the form.
   * @param string $id The id of the field to render as a check box.
   * @param CHECK_BOX_ITEM $item Properties used to render this check box.
   */
  public function draw_check_box_row ($id, $item = null)
  {
    $field = $this->_field_at ($id);
    if (! isset ($item))
    {
      $item = $this->make_check_properties ();
    }

    if ($field->description)
    {
      $item->description .= $field->description;
    }

    $ctrl = $this->_grouped_control_as_HTML ($field, null, $item, 'checkbox', $field->id);
    $ctrl =  $this->_control_created ($id, $ctrl);
    $this->_draw_field_row($field, $ctrl, $item->css_class);
  }

  /**
   * Draw a button to submit the form.
   * 'show_preview' is used if set; otherwise, 'preview' will only be shown if {@link $preview_enabled} is True.
   * @param boolean $show_preview
   */
  public function draw_submit_button_row ($show_preview = null)
  {
    $buttons = array();

    if (!$this->_form->allow_cancel_only)
    {
      if ($this->drafts_enabled)
      {
        $buttons [] = $this->submit_button_as_html ('Publish', '{icons}buttons/ship', 'save_as_visible');
      }
      else
      {
        $buttons [] = $this->submit_button_as_html ();
      }

      if ($this->drafts_enabled)
      {
        if ($this->_form->object_exists() && $this->inline_operations_enabled)
        {
          $url = $this->app->resolve_file('{app}/save_field.php');
          $buttons [] = $this->javascript_button_as_html('Save', 'execute_field(\'' . $url . '\', \'' . $this->_form->name . '\')', '{icons}buttons/save');
        }
        else
        {
          $buttons [] = $this->submit_button_as_html ('Save', '{icons}buttons/save', 'quick_save_and_reload');
        }
      }

      if (! isset ($show_preview))
      {
        $show_preview = $this->preview_enabled;
      }

      if ($show_preview)
      {
        if ($this->_form->object_exists() && $this->inline_operations_enabled)
        {
          $url = $this->context->resolve_file('{app}/generate_preview.php');
          $buttons [] = $this->javascript_button_as_html('Preview', 'execute_field(\'' . $url . '\', \'' . $this->_form->name . '\')', '{icons}buttons/view');
        }
        else
        {
          $buttons [] = $this->submit_button_as_html ('Preview', '{icons}buttons/view', 'preview_form');
        }
      }
    }

    if (isset($this->app))
    {
    	$referer_url = $this->app->get_referer_url();
    
	    if (!empty($referer_url))
	    {
	    	$buttons [] = $this->button_as_html('Cancel', $referer_url, '{icons}buttons/cancel');
	    }
    }

    if ($this->_text_control_was_rendered)
    {
      $icon = $this->context->get_icon_with_text('{icons}/indicators/question', Sixteen_px, 'Help');
      $buttons [] = '<a class="button" href="text_formatting.php" target="_blank" title="Show formatting help">' . $icon . '</a>';
    }
    
    $this->draw_buttons_in_row ($buttons);
  }

  public function start_button_row ($title = ' ')
  {
    $this->start_row ($title);
  }

  /**
   * Draw the list of buttons in a row.
   * Draws a series of buttons previously rendered with {@link javascript_button_as_html()},
   * {@link button_as_html()} or {@link submit_button_as_html()}.
   * @param string[] $buttons
   * @param string $title Title to show for this row.
   */
  public function draw_buttons_in_row($buttons, $title = '')
  {
    $this->start_button_row ($title);
    $this->draw_buttons ($buttons);
    $this->finish_row ();
  }

  /**
   * Draw an icon text/browse button row.
   * @param string $field_id Name of the field for the text control.
   */
  public function draw_icon_browser_row ($field_id)
  {
    $this->draw_text_line_with_button_row($field_id, $this->javascript_button_as_HTML ('Browse...', $field_id . '_field.show_picker ()', '{icons}buttons/browse'));
  }

  public function label_as_html($id)
  {
    $field = $this->_field_at ($id);
    $caption = $field->caption;

    if ($caption && !ctype_space($caption))
    {
      return '<label for="' . $id . '">' . $caption . '</label>';
    }

    return '';
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
   * Draw a single-line text control with a 'browse' button onto a separate row in the form.
   *
   * @param string $id The id of the field to render.
   * @param string $button The button to show next to the control.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   * @param string $css_class The class to apply to the overall container.
   * @return string
   */
  public function text_line_with_button_as_html($id, $button, $options = null, $css_class = 'browse')
  {
    $result = '<span class="' . $css_class . '">';
    $result .= $this->text_line_as_html($id, $options);
    $result .= $button;
    $result .= '</span>';

    return $result;
  }

  /**
   * Draw a single-line text control with a 'browse' button onto a separate row in the form.
   *
   * @param string $id The id of the field to render.
   * @param string $browse_script The script to execute from the browse button.
   * @param FORM_TEXT_CONTROL_OPTIONS $options Override the default text control rendering; can be null.
   * @return string
   */
  public function text_line_with_browse_button_as_html($id, $browse_script, $options = null)
  {
    return $this->text_line_with_button_as_html($id, $this->javascript_button_as_HTML ('Browse...', $browse_script, '{icons}buttons/browse'), $options);
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
    /** @var DATE_FIELD $field */
    $field = $this->_field_at ($id);
    $includes_time = $field->parts_to_convert & Date_time_time_part;

    if (! isset ($options))
    {
      $options = clone(default_text_options ());
      if ($includes_time)
      {
        $options->css_class = $this->default_date_time_css_class;
      }
      else
      {
        $options->css_class = $this->default_date_css_class;
      }
    }

    $js_form = $this->_form->js_form_name ();

    /** @var DATE_TIME $value */
    $value = $field->value();
    $js_date_value = $value->as_RFC_2822();

    $script = 'var ' . $id . "_field = new WEBCORE_DATE_TIME_FIELD ();\n";
    $script .= $id . "_field.output_format = Date_format_us;\n";
    if ($includes_time)
    {
      $script .= $id . "_field.show_time = true;\n";
    }

    $script .= $id . '_field.attach (' . $js_form . '.' . $id . ");\n";

    $script .= "cal = new WEBCORE_HTML_CALENDAR ();\n";
    $script .= "cal.show_month_selector = true;\n";
    $script .= "cal.show_now_selector = true;\n";
    $script .= "cal.attach(" . $id . "_field);\n";
    $script .= "cal.set_initial_date_time(new Date('" . $js_date_value . "'));\n";
    $script .= "cal.display()";

    $button = '<ul class="menu-items buttons"><li class="menu-trigger">';
    $button .= $this->javascript_button_as_html('', '', '{icons}buttons/calendar');
    $button .= '<div class="menu-dropdown"><div class="menu"><div class="calendar-menu-item" id="' . $id . '_field">';
    $button .= "<script type=\"text/javascript\">";
    $button .= $script;
    $button .= "</script>";
    $button .= '</div></div></div>';
    $button .= '</li></ul>';

    return $this->text_line_with_button_as_html($id, $button, $options);
  }

  /**
   * Return HTML for a multi-line text box.
   * @param string $id Name of field.
   * @param string $css_class The CSS class to apply to the control
   * @return string
   */
  public function text_box_as_html ($id, $css_class)
  {
    $field = $this->_field_at ($id);

    if ($field->visible)
    {
      $Result = $this->_start_control ($field, 'textarea');
      $Result .= ' cols="30" rows="5" class="' . $css_class . '">';
      $Result .= $this->_to_html ($field, ENT_NOQUOTES) . '</textarea>';

      if ($field->description)
      {
        $text = $field->description;
      }
      else
      {
        $text = '';
      }

      if ($text)
      {
        $Result.= '<div class="description">' . $text . '</div>';
      }

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
      $item = $this->make_check_properties($id);
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
      $css_class = '';
      if ($props->css_class)
      {
        $css_class = $props->css_class;
      }

      $Result = '';

      $ctrl = $this->_start_control ($field, 'select');

      if ($css_class)
      {
        $ctrl .= ' class="' . $css_class . '"';
      }

      if (isset ($props->on_click_script))
      {
        $ctrl .= ' onChange="' . $props->on_click_script . '"';
      }

      $ctrl .= '>';

      $Result .= $ctrl;

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
          $Result .= ' <span class="description">' . $field->description . '</span>';
        }
        else
        {
          $Result .= '<div class="description">' . $field->description . '</div>';
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
      $Result = $this->_start_control ($field, 'select');

      if (isset ($props->on_click_script))
      {
        $Result .= ' onChange="' . $props->on_click_script . '"';
      }

      $Result .= ' size="' . $props->height . '"';
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
        $Result .= '<div class="description">' . $field->description . '</div>';
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
   * @param FORM_TEXT_CONTROL_OPTIONS $options
   * @return string
   */
  public function file_as_html ($id, $options = null)
  {
    /** @var UPLOAD_FILE_FIELD $field */
    $field = $this->_field_at ($id);

    if (! isset ($options))
    {
      $options = clone(default_text_options ());
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
      $icon = $ft->icon_as_html ($file->mime_type, $url->extension (), Sixteen_px);

      // TODO Wrap in an .input class container

      $Result = '<div class="detail">' . $icon . ' ' . $file->name . ' (' . file_size_as_text ($file->size) . ")</div>";

      $file_info = $file->store_to_text ($id);
      $uploader = $this->_form->uploader ();
      $Result .= '<input type="hidden" name="'. $uploader->stored_info_name . '[]" value="' . $file_info . "\">\n";

      if ($field->description)
      {
        $Result .= '<div class="description">' . $field->description . "</div>";
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
   * @param string $icon
   * @param string $script Name of the JavaScript function to execute (must conform to 'function(form: form; submit_all_fields: boolean; submit_field_name, preview_field_name: string)').
   * @param string $icon_size
   * @return string
   */
  public function submit_button_as_html ($title = null, $icon = '', $script = null, $icon_size = Sixteen_px)
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
   * @param string $css_class
   * @access private
   */
  protected function _draw_field_row ($field, $control_text, $css_class = '')
  {
    if ($field->visible)
    {
      if ($field->tag_validator_type != Tag_validator_none)
      {
        $this->_text_control_was_rendered = true;
      }
?>
      <div class="form-row<?php if ($field->required) { echo ' required'; }?><?php if ($css_class) { echo ' ' . $css_class; }?>">
<?php
      $title = $field->caption;

      if ($title && !ctype_space($title))
      {
        echo '<label for="' . $field->id . '">' . $title . '</label>';
      }

      echo $control_text;
?>
      </div>
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
   * @param string $dom_id
   * @return string
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
    if ($field->required)
    {
      $Result .= ' required';
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
   * @param bool $can_be_focused A value that indicates whether the control should be focused.
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

    $style = '';

    $Result = $this->_start_control ($field);

    if (isset ($field->max_length) && ($field->max_length > 0))
    {
      $Result .= ' maxlength="' . $field->max_length . '"';
    }

    $css_class = '';

    if ($options->css_class)
    {
      $css_class = ' class="' . $options->css_class . '"';
    }

    if (isset ($options->on_change_script))
    {
      $Result .= ' OnChange=\'' . $options->on_change_script . '\'';
    }

    $Result .= ' type="' . $type . '"' . $style . $css_class . ' value="' . $this->_to_html ($field, ENT_QUOTES) . '">';

    if ($field->description || $options->extra_description)
    {
      $desc = $field->description . ' ' . $options->extra_description;

      if ($options->show_description_on_same_line)
      {
        $Result .= ' <span class="description">' . $desc . '</span>';
      }
      else
      {
        $Result .= '<div class="description">' . $desc . '</div>';
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
      $css_class = '';
      if ($props->css_class)
      {
        $css_class = $props->css_class;
      }

      if ($props->items_per_row > 1 && count($props->items) > 1)
      {
        $css_class .= ' multiple';
      }

      $counter = 0;
      if ($id)
      {
        if (isset ($this->_num_controls [$id]))
        {
          $counter = $this->_num_controls [$id] + 1;
        }
      }

      $Result = '';

      $item_count = 0;
      foreach ($props->items as $item)
      {
        $row_css_class = $css_class . ' ' . ($item->css_class ? 'form-row ' . $item->css_class : 'form-row');

        if ($Result == '')
        {
          $Result = '<div class="' . $row_css_class . '">';
        }

        $need_row_break = $item_count != 0 && $item_count % $props->items_per_row == 0;
        if ($need_row_break)
        {
          $Result .= '</div><div class="' . $row_css_class . '">';
        }

        $counter += 1;

        if (! $id)
        {
          $field = $this->_field_at ($item->title);
          $item = clone($item);
          $item->title = $field->caption;
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

          $Result .= $ctrl;
        }

        $item_count += 1;
      }

      $Result .= '</div>';

      if ($id)
      {
        $this->_num_controls [$id] = $counter;

        if ($field->description)
        {
          $Result .= '<div class="description">' . $field->description . '</div>';
        }

        $Result = $this->_control_created ($id, $Result, false);
      }

      return $Result;
    }

    return '';
  }

  /**
   * Renders a checkbox or radio button with a label.
   * Used by {@link _control_group_as_HTML()} and {@link draw_check_box_row()} to generate
   * the raw control text.
   * @param FIELD $field Render the control for this field.
   * @param FORM_LIST_PROPERTIES $props Control is rendered in this list (may be null).
   * @param FORM_LIST_ITEM $item Settings to apply for the rendered item.
   * @param string $type Type of control: "checkbox" or "radio".
   * @param $dom_id string The unique id to assign to the generated control.
   * @return string
   * @access private
   */
  protected function _grouped_control_as_html ($field, $props, $item, $type, $dom_id)
  {
    if ($field->visible)
    {
      $ctrl = $this->_start_control ($field, 'input', $dom_id);
      $ctrl .= ' type="' . $type . '" value="' . $item->value . '"';

      if (! $item->enabled && $field->enabled)
      {
        $ctrl .= ' disabled';
      }

      if (! empty ($item->on_click_script))
      {
        $ctrl .= ' onClick="' . $item->on_click_script . '"';
      }
      elseif (isset ($props) && ! empty ($props->on_click_script))
      {
        $ctrl .= ' onClick="' . $props->on_click_script . '"';
      }

      if ($field->selected ($item->value))
      {
        $ctrl .= ' checked';
      }

      $ctrl .= '>';

      $text = $item->text ? $item->text : '';
      $label = '';

      if ($item->description && (!isset($props) || $props->show_descriptions))
      {
        if ($item->title && !ctype_space($item->title))
        {
          $label .= '<label for="' . $dom_id . '"><span class="title">' . $item->title . '</span></label>' .  $text . '<label for="' . $dom_id . '"><span class="description">' . $item->description . '</span></label>';
        }
        else
        {
          $label .= $text . $item->description;
        }
      }
      else
      {
        $label = '<label for="' . $dom_id . '">';
        $label .= $item->title;
        $label .= '</label>' . $text;
      }

      return $ctrl . $label;
    }
    
    return '';
  }

  /**
   * @var FORM
   * @access private
   */
  protected $_form;

  /**
   * Table of used DOM ids.
   * If radio buttons or other group controls are rendered to different parts of a page for the
   * same field, this structure remembers which DOM id was last used for the given field.
   * @var int[]
   * @access private
   */
  protected $_num_controls = array ();

  /**
   * @var bool
   */
  private $_text_control_was_rendered;
}

/**
 * Used by the {@link FORM_RENDERER} to stack width settings.
 * @see FORM::set_width()
 * @package webcore
 * @subpackage forms-core
 * @version 3.6.0
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
