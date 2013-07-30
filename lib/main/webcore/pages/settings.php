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

/** @var $theme_query THEME_QUERY */
  $theme_query = $Page->theme_query ();

  $Page->title->subject = $theme_query->size () . ' Themes';
  $Page->template_options->title = 'Settings';
  $Page->template_options->settings_url = '';
  
  $Page->location->add_root_link ();
  $Page->location->append ('Settings');
  $Page->location->append ($theme_query->size () . ' Themes');

  $themes = $theme_query->objects ();

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
  $box->new_column_of_type('content-column text-flow');
  ?>
  <p>Adjust your font and theme settings in the form below. Select <span class="reference">[default]</span>
    to restore the site default for that setting.</p>
  <div class="form-content">
    <?php $form->display (); ?>
  </div>
  <h2>Preview</h2>
  <h1>Level 1 header</h1>
  <h2>Level 2 header</h2>
  <h3>Level 3 header</h3>
  <h4>Level 4 header</h4>
  <table class="basic columns left-labels">
    <tr>
      <th></th>
      <th>Data 1</th>
      <th>Data 2</th>
      <th>Data 3</th>
    </tr>
    <tr>
      <th>Header 1</th>
      <td>Data 1.1</td>
      <td>Data 1.2</td>
      <td>Data 1.3</td>
    </tr>
    <tr>
      <th>Header 2</th>
      <td>Data 2.1</td>
      <td>Data 2.2</td>
      <td>Data 2.3</td>
    </tr>
  </table>
  <div class="quote-block">"This is a block quote. These are often used in article to include
    text from other sources. This is generally used for larger citations. Use the inline
    style for smaller citations."</div>
  <div class="preview">
    <p>This is text in a preview box. <span class="quote-inline">This is an example of an inline quotation.</span>
      This text following the citation and should make the paragraph wrap at least once.</p>
  </div>
  <div class="chart">
    <h3 class="chart-title" style="margin-top: 0">Box (Code Listing)</h3>
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
  $box->new_column_of_type('right-sidebar-column');
  ?>
  <div class="right-sidebar">
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
  $box->finish_column_set ();
  ?>
</div>
<?php
  $Page->finish_display ();
?>