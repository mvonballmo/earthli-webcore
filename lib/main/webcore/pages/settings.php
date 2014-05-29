<?php

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

  /** @var THEMED_PAGE $themed_page */
  $themed_page = $Page;
  /** @var $theme_query THEME_QUERY */
  $theme_query = $themed_page->theme_query ();

  /** @var THEME[] $themes */
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

  $Page->add_script_file('{scripts}webcore_calendar.js');

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
    $field->required = true;
    $this->add_field ($field);
    $this->set_value('name', 'Filler text');

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'description';
    $field->caption = 'Description';
    $this->add_field ($field);
    $this->set_value('description', 'Filler text that demonstrates which font is being used in longer, wrapping text.');

    $field = new DATE_TIME_FIELD();
    $field->id = 'date';
    $field->caption = 'Date';
    $this->add_field ($field);
    $this->set_value('date', new DATE_TIME());

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
    $renderer->default_control_height = '75px';
    $renderer->set_width('25em');

    $renderer->start();
    $props = $renderer->make_list_properties ();
    $props->add_item('bool1', 1);
    $props->add_item('bool2', 1);
    $props->items_per_row = 4;
    $renderer->draw_check_boxes_row('Options', $props);
    $renderer->start_row('Text');
    $text_props = new FORM_TEXT_CONTROL_OPTIONS();
    $text_props->width = '8em';
    echo $renderer->date_as_html('date');
    echo ' ';
    echo $renderer->text_line_as_html('name', $text_props);
    $renderer->finish_row();
    $renderer->draw_text_box_row('description', null);

    $renderer->start_block('Block');

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;
    $props->add_item ('Option One', 0, 'Description for option one.');
    $props->add_item ('Option Two', 1, 'Description for option two.');
    $props->items_per_row = 2;
    $renderer->draw_radio_group_row('select', $props);

    $field = $this->field_at('select');
    $props->width = '8em';

    $renderer->start_row('Menus');
    echo $renderer->drop_down_as_html('select', $props);
    echo ' ';
    $field->required = true;
    echo $renderer->drop_down_as_html('select', $props);
    $renderer->finish_row();

    $renderer->start_row('Lists');
    $field->required = false;
    echo $renderer->list_box_as_html('select', $props);
    echo ' ';
    $field->required = true;
    echo $renderer->list_box_as_html('select', $props);
    $renderer->finish_row();

    $renderer->finish_block();
    $renderer->draw_submit_button_row();

    $renderer->finish();
  }
}

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
    <div class="form-content" style="width: 200px">
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
      $grid->pager->pages_to_show = 2;
      $grid->pager->show_first_and_last = false;
      $grid->pager->show_total = false;
      $grid->set_ranges (13, 1);
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
    $item = $menu->append('Three', '#', '{icons}/buttons/delete');
    $item->description = 'Button three includes a description below';
    $menu->renderer->content_mode &= ~Menu_show_icon;
    $menu->display();
    ?>
  </div>
  <div class="button-content">
    <?php
    $menu->renderer->separator_class = 'objects';
    $menu->display();
    ?>
  </div>
  <div class="button-content">
    <?php
    $menu->renderer->separator_class = 'location';
    $menu->display();
    ?>
  </div>
  <div class="button-content">
    <a href="#" class="button">L</a><?php
    $menu->renderer->set_size(Menu_size_compact);
    $menu->renderer->content_mode |= Menu_show_as_buttons | Menu_show_icon;
    $menu->renderer->options &= ~Menu_options_show_trigger_title;
    $menu->display();
    $menu->renderer->set_size(Menu_size_full);
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->display();
    $menu->renderer->set_size(Menu_size_compact);
    $menu->renderer->content_mode |= Menu_show_as_buttons | Menu_show_icon;
    $menu->renderer->options &= ~Menu_options_show_trigger_title;
    $menu->display();
    ?><a href="#" class="button">R</a>
  </div>
  <div class="button-content">
    <a href="#" class="button">L</a><?php
    $menu->renderer->set_size(Menu_size_full);
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->display();
    ?><a href="#" class="button">R</a>
  </div>
  <div class="button-content">
    <?php
    $renderer = $Page->make_controls_renderer ();
    echo $renderer->button_as_html ('', '#', '{icons}buttons/upgrade', Thirty_two_px);
    echo $renderer->button_as_html ('Upgrayedd', '#', '{icons}buttons/upgrade', Thirty_two_px);
    echo $renderer->button_as_html ('', '#', '{icons}buttons/upgrade', Twenty_px);
    echo $renderer->button_as_html ('Upgrayedd', '#', '{icons}buttons/upgrade', Twenty_px);
    echo $renderer->button_as_html ('', '#', '{icons}buttons/upgrade', Sixteen_px);
    echo $renderer->button_as_html ('Upgrayedd', '#', '{icons}buttons/upgrade', Sixteen_px);
    ?>
  </div>
  <div class="button-content">
    <?php
    include ('webcore/gui/tree_node.php');

    $tree = $Page->make_tree_renderer ();
    $tree->node_info = new GENERIC_TREE_NODE_INFO($Page);

    $node = new TREE_NODE ('Documents', '', false);
    $node->append (new TREE_NODE ('Specs (PDF)', '#', '', true));
    $node->append (new TREE_NODE ('Specs 2 (PDF)', '#'));
    $node->append (new TREE_NODE ('Specs 3 (PDF)', '#'));
    $nodes [] = $node;

    $root = new TREE_NODE ('Encodo', '', false, '', true);
    foreach ($nodes as $node)
    {
      $root->append ($node);
    }
    $roots [] = $root;
    $roots [] = new TREE_NODE ('Earthli');

    $node = new TREE_NODE ('Archive');
    $node->append (new TREE_NODE ('Specs (PDF)', '#'));
    $node->append (new TREE_NODE ('Specs 2 (PDF)', '#'));
    $node->append (new TREE_NODE ('Specs 3 (PDF)', '#'));

    $nodes = null;
    $nodes [] = $node;

    $root = new TREE_NODE ('Home');
    foreach ($nodes as $node)
    {
      $root->append ($node);
    }
    $roots [] = $root;

    $tree->display ($roots);
    ?>
  </div>
  <h3>Form elements (level 3 heading)</h3>
  <div class="form-content">
    <?php

    $form = new SAMPLE_FORM($Page);
    $form_renderer = new FORM_RENDERER($form);
    $form->display();
    ?>
  </div>
  <div class="info-box-top">
    <p>This is an info box at the top of a <a href="#">page</a> or <a href="#">section</a>.</p>
  </div>
  <h4>Text and block elements (level 4 heading)</h4>
  <p>Standard paragraph text.</p>
  <ol><li>Item One</li><li>Item Two</li></ol>
  <ul><li>Item One</li><li>Item Two</li></ul>
  <div class="info-box-bottom">
    <p>This is an info box at the bottom of a <a href="#">page</a> or <a href="#">section</a>.</p>
    <p>Paragraph two.</p>
    <p>Paragraph three.</p>
    <p>Div one.</p>
    <p>Paragraph four.</p>
    <p>Div two.</p>
  </div>
  <p class="quote-block">"This is a block quote. These are often used in article to include
    text from other sources. This is generally used for larger citations. Use the inline
    style for smaller citations."</p>
  <div class="preview">
    <h3 class="preview-title">Title</h3>
    <p>This is text in a preview box. <span class="quote-inline">This is an example of an inline quotation.</span>
      This text following the citation and should make the paragraph wrap at least once.</p>
  </div>
  <?php
    $Page->show_message('This is a <a href="#">caution</a> box.', 'info');
    $Page->show_message('This is a <a href="#">warning</a> box.', 'warning');
    $Page->show_message('This is an <a href="#">error</a> box.');
  ?>
  <h3>Chart/Graph</h3>
  <div class="graph-background">
    <div class="graph-foreground" style="width: 30px; height: 100px"></div>
    <div class="graph-foreground" style="width: 30px; height: 10px"></div>
    <div class="graph-foreground" style="width: 30px; height: 80px"></div>
    <div class="graph-foreground" style="width: 30px; height: 75px"></div>
    <div class="graph-foreground" style="width: 30px; height: 75px"></div>
    <div class="graph-foreground" style="width: 30px; height: 100px"></div>
    <div class="graph-foreground" style="width: 30px; height: 50px"></div>
  </div>
  <div class="chart">
    <h3 class="chart-title">Box</h3>
    <div class="chart-body">
      <p>The following is a code from the <c>MUNGER</c>.</p>
      <pre><code>protected function _process($input, $tokenizer)
{
  $tokenizer->set_input($input);
  while ($tokenizer->available())
  {
    $tokenizer->read_next();
    $token = $tokenizer->current();
    $this->_process($token);
  }
}</code></pre>
    </div>
  </div>
  <p>This is code by itself.</p>
<pre><code>protected function _process($input, $tokenizer)
{
  $tokenizer->set_input($input);
  while ($tokenizer->available())
  {
    $tokenizer->read_next();
    $token = $tokenizer->current();
    $this->_process($token);
  }
}</code></pre>
  <div class="abstract">
    This is the text of an abstract. These are often used to summarize much longer blocks of text, similar in a way to the block at the beginning of a scientific paper.
  </div>
  <p>This is text before a rule.</p>
  <hr>
  <p>This is text after a rule.</p>
  <div class="quote pullquote right" style="float: right; width: 150px">Pull-quotes catch your eye.</div>
  <p>This is the text that accompanies the pull-quote. Pull-quotes are often used to highlight interesting bits of text in much longer articles in order to pique a reader's interest or to catch a scanner's eye.</p>
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
      <td><strong class="selected">selected</strong></td>
      <td>Selected text</td>
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