<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

  /** @var THEMED_PAGE $themed_page */
  $themed_page = $Page;
  /** @var $theme_query THEME_QUERY */
  $theme_query = $themed_page->theme_query ();
  $themes = $theme_query->objects ();

  $Page->title->subject = $theme_query->size () . ' Themes';
  $Page->template_options->title = 'Settings';
  $Page->template_options->settings_url = '';

  $Page->location->add_root_link ();
  $Page->location->append ('Settings');
  $Page->location->append ($theme_query->size () . ' Themes');

  // Leave the form initialization before the call to $Page->start_display() so that the page is rendered
  // most recently selected theme
  include_once ('webcore/forms/theme_selector_form.php');
  $form = new THEME_SELECTOR_FORM ($Page, $themes);
  $form->process_plain ();
  $form->load_with_defaults ();

  $Page->start_display ();
?>
<div class="main-box">
  <?php
  $box = $Page->make_box_renderer();
  $box->start_column_set();
  $box->new_column_of_type('left-sidebar-column');
  ?>
  <div class="left-sidebar">
    <p>Customize the font and theme to the right and see a preview below.</p>
    <div class="form-content">
      <?php
      $form->display ();
      ?>
    </div>
    <p>You can also switch themes with the samples below. Press the button under the thumbnail to select a theme.</p>
    <div class="grid-content">
      <?php
      $class_name = $Page->final_class_name ('THEME_GRID', 'webcore/gui/theme_grid.php');

      /** @var $grid THEME_GRID */
      $grid = new $class_name ($Page);
      $grid->is_chooser = true;
      $grid->paginator->pages_to_show = 2;
      $grid->paginator->show_first_and_last = false;
      $grid->paginator->show_total = false;
      $grid->set_ranges (5, 1);
      $grid->set_query ($theme_query);
      $grid->display ();
      ?>
    </div>
  </div>
  <?php
  $box->new_column_of_type('content-column text-flow');
  ?>
  <h1>Preview (level 1 heading)</h1>
  <h2>Buttons & Menus (level 2 heading)</h2>
  <?php
  require_once ('webcore/gui/page_navigator.php');
  $navigator = new PAGE_NAVIGATOR($Page);
  $navigator->set_ranges(50, 10);
  $navigator->pages_to_show = 4;
  $navigator->display();
  ?>
  <div class="button-content">
    <?php
    require_once ('webcore/cmd/commands.php');
    $menu = $Page->make_menu();
    $menu->commands->append_group('Group One');
    $menu->append('One', '#', '{icons}/buttons/edit');
    $menu->append('Two', '#', '{icons}/buttons/add');
    $menu->append('Three', '#', '{icons}/buttons/delete');
    $menu->display();
    ?>
  </div>
  <div class="button-content">
    <?php
    $menu->renderer->set_size(Menu_size_compact);
    $menu->renderer->content_mode |= Menu_show_as_buttons;
    $menu->display();
    ?>
  </div>
  <div class="button-content">
    <?php
    $menu->renderer->set_size(Menu_size_full);
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->display();
    ?>
  </div>
  <h3>Form elements (level 3 header)</h3>
  <div class="form-content">
    <?php
    require_once('webcore/forms/form.php');
    require_once('webcore/forms/form_renderer.php');

    class SAMPLE_FORM extends FORM
    {
      function __construct($context)
      {
        parent::__construct ($context);

        $field = new INTEGER_FIELD ();
        $field->id = 'radio';
        $field->caption = 'Radio';
        $this->add_field ($field);

        $field = new TEXT_FIELD ();
        $field->id = 'name';
        $field->caption = 'Name';
        $this->add_field ($field);

        $field = new TEXT_FIELD ();
        $field->id = 'description';
        $field->caption = 'Description';
        $this->add_field ($field);

        $field = new DATE_TIME_FIELD();
        $field->id = 'date';
        $field->caption = 'Date';
        $this->add_field ($field);

        $field = new BOOLEAN_FIELD ();
        $field->id = 'bool1';
        $field->set_value(1);
        $field->caption = 'Option 1';
        $this->add_field ($field);

        $field = new BOOLEAN_FIELD ();
        $field->id = 'bool2';
        $field->caption = 'Option 2';
        $this->add_field ($field);

        $field = new ENUMERATED_FIELD();
        $field->id = 'select';
        $field->caption = 'Select';
        $field->add_value (0);
        $field->add_value (1);
        $field->add_value (2);
        $field->add_value (3);
        $field->required = true;
        $this->add_field ($field);
      }

      /**
       * Draw the controls for the form.
       * @param FORM_RENDERER $renderer
       * @access private
       */
      protected function _draw_controls($renderer)
      {
        $renderer->start();
        $props = $renderer->make_list_properties ();
        $props->add_item('bool1', 1);
        $props->add_item('bool2', 1);
        $props->items_per_row = 4;
        $renderer->draw_check_boxes_row('Options', $props);
        $renderer->start_row('Text');
        $text_props = new FORM_TEXT_CONTROL_OPTIONS();
        $text_props->width = '10em';
        echo $renderer->date_as_html('date');
        echo $renderer->text_line_as_html('name', $text_props);
        $renderer->finish_row();
        $renderer->draw_text_box_row('description', null, '2em');
        $props = $renderer->make_list_properties ();
        $props->show_descriptions = true;
        $props->width = '30em';
        $props->height = '2em';
        $props->add_item ('One day', 0, 'For parties or sporting events.');
        $props->add_item ('Several days', 1, 'For trips; both first and last day are fixed.');
        $renderer->draw_radio_group_row('select', $props);
        $renderer->draw_drop_down_row('select', $props);
        $renderer->draw_list_box_row('select', $props);

        $renderer->finish();
      }
    }

    $form = new SAMPLE_FORM($Page);
    $form_renderer = new FORM_RENDERER($form);
    $form->display();
    ?>
  </div>
  <h4>Text and block elements (level 4 heading)</h4>
  <div class="quote-block">"This is a block quote. These are often used in article to include
    text from other sources. This is generally used for larger citations. Use the inline
    style for smaller citations."</div>
  <div class="preview">
    <p>This is text in a preview box. <span class="quote-inline">This is an example of an inline quotation.</span>
      This text following the citation and should make the paragraph wrap at least once.</p>
  </div>
  <div class="chart">
    <h3 class="chart-title">Box (Code Listing)</h3>
    <div class="chart-body">
      <pre><code>protected function _process_given_tokenizer($input, $tokenizer)
{
  $tokenizer->set_input($input);
  while ($tokenizer->tokens_available())
  {
    $tokenizer->read_next_token();
    $token = $tokenizer->current_token();
    $this->_process_token($token);
  }
}</code></pre>
    </div>
  </div>
  <table class="basic columns left-labels">
    <tr>
      <th>Style</th>
      <th>Description</th>
    </tr>
    <tr>
      <td><strong>strong</strong></td>
      <td>Strongly formatted text</td>
    </tr>
    <tr>
      <td><em>emphasized</em></td>
      <td>Emphasized text</td>
    </tr>
    <tr>
      <td><small class="notes">notes</small></td>
      <td>Notes/comments</td>
    </tr>
    <tr>
      <td><code>code</code></td>
      <td>Inline code</td>
    </tr>
    <tr>
      <td><strong class="highlight">highlighted</strong></td>
      <td>Highlighted text</td>
    </tr>
    <tr>
      <td><del>deleted</del></td>
      <td>Deleted text</td>
    </tr>
    <tr>
      <td><var>variableOne</var></td>
      <td>Variable names</td>
    </tr>
    <tr>
      <td><kbd>&#8984;</kbd> + <kbd>B</kbd></td>
      <td>Keyboard characters</td>
    </tr>
    <tr>
      <td><dfn>definition</dfn></td>
      <td>Definitions</td>
    </tr>
    <tr>
      <td><abbr title="Computer-aided Design">CAD</abbr></td>
      <td>Abbreviations</td>
    </tr>
    <tr>
      <td><cite>citation</cite></td>
      <td>Citations</td>
    </tr>
  </table>
  <?php
  $box->finish_column_set ();
  ?>
</div>
<?php
  $Page->finish_display ();
?>