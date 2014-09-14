<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
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
require_once ('webcore/forms/form.php');

/**
 * Updates {@link THEME} properties.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */
class THEME_SELECTOR_FORM extends FORM
{
  /**
   * @var string
   */
  public $name = 'theme_setter_form';

  /**
   * @param CONTEXT $page
   * @param THEME[] $themes List of available themes.
   */
  public function __construct ($page, $themes)
  {
    parent::__construct ($page);

    $this->_themes = $themes;

    $field = new INTEGER_FIELD ();
    $field->id = 'page_number';
    $field->caption = 'Page Number';
    $field->min_value = 1;
    $field->visible = false;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'main_CSS_file_name';
    $field->caption = 'Name';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'font_name_CSS_file_name';
    $field->caption = 'Font Name';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'font_size_CSS_file_name';
    $field->caption = 'Font Size';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'dont_apply_to_forms';
    $field->caption = 'Form Styling';
    $field->description = 'Don\'t apply theme to form controls.';
    $this->add_field ($field);
  }

  /**
   * Store the form's values as the theme settings.
   * @param object $obj This parameter is ignored.
   * @access private
   */
  public function commit ($obj)
  {
    $this->page->set_theme_font_name ($this->value_for ('font_name_CSS_file_name'));
    $this->page->set_theme_font_size ($this->value_for ('font_size_CSS_file_name'));
    $this->page->set_theme_main ($this->value_for ('main_CSS_file_name'));
    $this->page->set_theme_dont_apply_to_forms ($this->value_for ('dont_apply_to_forms'));
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    $this->set_value ('page_number', read_var ('page_number', 1));
    $this->set_value ('dont_apply_to_forms', $this->page->stored_theme->dont_apply_to_forms);
    $this->set_value ('main_CSS_file_name', $this->page->stored_theme->id);
    $this->set_value ('font_name_CSS_file_name', $this->page->stored_theme->font_name_CSS_file_name);
    $this->set_value ('font_size_CSS_file_name', $this->page->stored_theme->font_size_CSS_file_name);
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function set_main_theme (name)
  {
    <?php echo $this->js_form_name (); ?>.main_CSS_file_name.value = name;
    <?php echo $this->js_form_name (); ?>.submit ();
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->labels_css_class = 'top right';
    $renderer->start ();

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'this.form.submit()';
    $props->add_item ('[Default]', '');
    foreach ($this->_themes as $theme)
    {
      $props->add_item ($theme->title_as_plain_text (), $theme->id);
    }

    $renderer->draw_drop_down_row ('main_CSS_file_name', $props);

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'this.form.submit()';
    $props->add_item ('[Default]', '');
    $theme_font_names = $this->page->theme_options->font_names ();
    foreach ($theme_font_names as $name => $url)
    {
      $props->add_item ($name, $url);
    }

    $renderer->draw_drop_down_row ('font_name_CSS_file_name', $props);

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'this.form.submit()';
    $props->add_item ('[Default]', '');
    $theme_font_sizes = $this->page->theme_options->font_sizes ();
    foreach ($theme_font_sizes as $name => $url)
    {
      $props->add_item ($name, $url);
    }

    $renderer->draw_drop_down_row ('font_size_CSS_file_name', $props);

    $check_props = $renderer->make_check_properties ();
    $check_props->on_click_script = 'this.form.submit ()';
    $renderer->draw_check_box_row ('dont_apply_to_forms', $check_props);

    $renderer->finish ();
  }
}
?>