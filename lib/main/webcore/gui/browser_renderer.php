<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.6.0
 * @since 2.7.0
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
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders a {@link BROWSER} for text or html.
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.7.1
 */
class BROWSER_RENDERER extends OBJECT_RENDERER
{
  /**
   * @var boolean
   */
  public $show_user_agent = true;
  
  /**
   * Outputs the object as HTML.
   * @param BROWSER $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $browser = $obj;
?>
    <table class="basic columns left-labels">
      <tr>
        <th>Application</th>
        <td>
          <?php
          $icon_name = $browser->icon_name ();
          if ($icon_name)
          {
            $this->context->start_icon_container('{icons}logos/browsers/' . $icon_name, Thirty_two_px);
            echo $browser->name () . ' ' . $browser->version ();
            $this->context->finish_icon_container();
          }
          ?>
        </td>
      </tr>
      <tr>
        <th>Renderer</th>
        <td>
          <?php echo $browser->renderer_name (); ?> <?php echo $browser->renderer_version (); ?>
          <?php
          if ($browser->is (Browser_gecko))
          {
            $gd = $browser->gecko_date ();
            if ($gd)
            {
              $t = $gd->formatter ();
              $t->type = Date_time_format_short_date;
              echo '(Released ' . $gd->format ($t) . ')';
            }
          }
          ?>
        </td>
      </tr>
      <tr>
        <th><abbr title ="Operating System">OS</abbr></th>
        <td><?php echo $browser->system_id (); ?></td>
      </tr>
      <tr>
        <th>JavaScript</th>
        <td><?php echo $this->_boolean_as_html ($browser->supports (Browser_JavaScript)); ?></td>
      </tr>
      <tr>
        <th>CSS</th>
        <td>
          <?php
          echo $this->_boolean_as_html ($browser->supports (Browser_CSS_1), '1.0');
          echo '<br>';
          echo $this->_boolean_as_html ($browser->supports (Browser_CSS_2), '2.0');
          echo '<br>';
          echo $this->_boolean_as_html ($browser->supports (Browser_CSS_2_1), '2.1');
          echo '<br>';
          echo $this->_boolean_as_html ($browser->supports (Browser_columns), 'columns');
          ?>
        </td>
      </tr>
      <tr>
        <th>DOM</th>
        <td>
          <?php
          echo $this->_boolean_as_html ($browser->supports (Browser_DOM_1), '1.0');
          echo '<br>';
          echo $this->_boolean_as_html ($browser->supports (Browser_DOM_2), '2.0');
          ?>
        </td>
      </tr>
      <tr>
        <th>Alpha PNG</th>
        <td><?php echo $this->_boolean_as_html ($browser->supports (Browser_alpha_PNG)); ?></td>
      </tr>
      <tr>
        <th>Cookies</th>
        <td><?php echo $this->_boolean_as_html ($browser->supports (Browser_cookie)); ?></td>
      </tr>
      <?php
      if ($this->show_user_agent)
      {
        ?>
        <tr>
          <th>User Agent</th>
          <td><?php echo $browser->user_agent_string; ?></td>
        </tr>
      <?php
      }
      ?>
    </table>


    <dl>
    </dl>
<?php    
  }

  /**
   * Outputs the object as plain text.
   * @param BROWSER $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    $browser = $obj;
    
    if ($this->show_user_agent)
    {
      echo $this->par ($browser->user_agent_string);
    }
    $table = new TEXT_TABLE_RENDERER ($this);

    $table->add_item ('Application', $browser->name () . ' ' . $browser->version ());
    $gd = $browser->gecko_date ();
    if ($browser->is (Browser_gecko) && $gd->is_valid ())
    {
      $t = $gd->formatter ();
      $t->type = Date_time_format_short_date;
      $table->add_item ('Renderer', $browser->renderer_name () . ' ' . $browser->renderer_version () . ' (Released ' . $gd->format ($t) . ')');
    }
    else
    {
      $table->add_item ('Renderer', $browser->renderer_name () . ' ' . $browser->renderer_version ());
    }
    $table->add_item ('Operating System', $browser->system_id ());
    $table->add_item ('JavaScript', $this->_boolean_as_text ($browser->supports (Browser_JavaScript)));
    $table->add_item ('CSS', $this->_boolean_as_text ($browser->supports (Browser_CSS_1), '1.0') . ' '
                           . $this->_boolean_as_text ($browser->supports (Browser_CSS_2), '2.0') . ' '
                           . $this->_boolean_as_text ($browser->supports (Browser_CSS_2_1), '2.1') . ' '
                     );
    $table->add_item ('DOM', $this->_boolean_as_text ($browser->supports (Browser_DOM_1), '1.0') . ' '
                           . $this->_boolean_as_text ($browser->supports (Browser_DOM_2), '2.0'));
    $table->add_item ('Alpha PNG', $this->_boolean_as_text ($browser->supports (Browser_alpha_PNG)));
    $table->add_item ('Cookies', $this->_boolean_as_text ($browser->supports (Browser_cookie)));
    $table->display ();    
  }  

  /**
   * Return the boolean value as HTML.
   * Includes a positive or negative image from the "icons" folder. 
   * @param boolean $value
   * @param string $text
   * @return string
   */
  protected function _boolean_as_html ($value, $text = '')
  {
    if ($value)
    {
      if (! $text)
      {
        $text = 'yes';
      }
      return $this->context->get_icon_with_text('{icons}comment/thumbs_up', Fifteen_px, $text);
    }
    else
    {
      if (! $text)
      {
        $text = 'no';
      }
      return $this->context->get_icon_with_text ('{icons}comment/thumbs_down', Fifteen_px, $text);
    }
  }

  /**
   * Return the boolean value as plain text.
   * @param boolean $value
   * @param string $text
   * @return string
   */
  protected function _boolean_as_text ($value, $text = '')
  {
    if ($value)
    {
      if ($text)
      {
        $text .= ' (yes)';
      }
      else
      {
        $text = 'yes';
      }
    }
    else
    {
      if ($text)
      {
        $text .= ' (no)';
      }
      else
      {
        $text = 'no';
      }
    }

    return $text;
  }
}

?>