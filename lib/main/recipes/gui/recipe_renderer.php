<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage gui
 * @version 3.5.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Recipes.

earthli Recipes is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Recipes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Recipes; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Recipes, visit:

http://earthli.com/software/webcore/app_recipes.php

****************************************************************************/

/** */
require_once ('webcore/gui/entry_renderer.php');

/**
 * Render details for a {@link RECIPE}.
 * @package recipes
 * @subpackage gui
 * @version 3.5.0
 * @since 1.3.0
 */
class RECIPE_RENDERER extends DRAFTABLE_ENTRY_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param RECIPE $entry
   * @access private
   */
  protected function _display_as_html ($entry)
  {
    if ($entry->originator)
    {
  ?>
  <p>From the kitchen of <span class="field"><?php echo $entry->originator_as_html (); ?></span>.</p>
  <?php
    }
  ?>
  <?php $this->_echo_html_description ($entry); ?>
  <?php
    if (! $this->_options->preferred_text_length )
    {
      $box = $this->context->make_box_renderer ();
      $box->start_column_set ();
      $box->new_column_of_type ('two-columns text-flow');
      echo '<h3>Ingredients</h3>';
      echo $entry->ingredients_as_html ();
      $box->new_column_of_type ('two-columns text-flow');
      echo '<h3>Instructions</h3>';
      echo $entry->instructions_as_html ();
      $box->finish_column_set();
    }

    $this->_echo_html_user_information ($entry, 'info-box-bottom');
  }

  /**
   * Outputs the object as plain text.
   * @param RECIPE $entry
   * @access private
   */
  protected function _display_as_plain_text ($entry)
  {
    $this->_echo_plain_text_user_information ($entry);

    if ($entry->originator)
    {
      echo $this->par ("From the kitchen of $entry->originator");
    }

    $this->_echo_plain_text_description ($entry);

    if (! $this->_options->preferred_text_length)
    {
      echo $this->line ('Ingredients');
      echo $this->line ($this->sep ());
      echo $entry->ingredients_as_plain_text ();
      echo $this->line ();
      echo $this->line ('Instructions');
      echo $this->line ($this->sep ());
      echo $entry->instructions_as_plain_text ();
    }
  }
}
?>