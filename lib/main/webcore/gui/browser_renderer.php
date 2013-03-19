<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * @version 3.3.0
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
    <dl>
      <dt class="field">Application</dt>
      <dd class="detail">
        <?php
        $icon_name = $browser->icon_name ();
        if ($icon_name)
        {
          echo $this->context->resolve_icon_as_html ('{icons}logos/browsers/' . $icon_name, '', '32px') . ' ';
        }
        echo $browser->name () . ' ' . $browser->version ();
      ?>
      </dd>
      <dt class="field">Renderer</dt>
      <dd class="detail">
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
      </dd>
      <dt class="field">Operating System</dt>
      <dd class="detail">
        <?php echo $browser->system_id (); ?>
      </dd>
      <dt class="field">JavaScript</dt>
      <dd class="detail">
        <?php echo $this->_boolean_as_html ($browser->supports (Browser_JavaScript)); ?>
      </dd>
      <dt class="field">CSS</dt>
      <dd class="detail">
        <?php
        echo $this->_boolean_as_html ($browser->supports (Browser_CSS_1), '1.0');
        echo ' ';
        echo $this->_boolean_as_html ($browser->supports (Browser_CSS_2), '2.0');
        echo ' ';
        echo $this->_boolean_as_html ($browser->supports (Browser_CSS_2_1), '2.1');
        echo ' ';
        echo $this->_boolean_as_html ($browser->supports (Browser_columns), 'columns');
      ?>
      </dd>
      <dt class="field">DOM</dt>
      <dd class="detail">
        <?php 
        echo $this->_boolean_as_html ($browser->supports (Browser_DOM_1), '1.0'); 
        echo ' ';
        echo $this->_boolean_as_html ($browser->supports (Browser_DOM_2), '2.0');
        ?>
      </dd>
      <dt class="field">Alpha PNG</dt>
      <dd class="detail">
        <?php echo $this->_boolean_as_html ($browser->supports (Browser_alpha_PNG)); ?>
      </dd>
      <dt class="field">Cookies</dt>
      <dd class="detail">
        <?php echo $this->_boolean_as_html ($browser->supports (Browser_cookie)); ?>
      </dd>
      <?php
      if ($this->show_user_agent)
      {
        ?>
        <dt class="field">User Agent String</dt>
        <dd class="detail">
          <?php echo $browser->user_agent_string; ?>
        </dd>
        <?php
      }
      ?>
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
      return $this->context->resolve_icon_as_html ('{icons}comment/thumbs_up', '', '15px') . ' ' . $text;
    }
    else
    {
      if (! $text)
      {
        $text = 'no';
      }
      return $this->context->resolve_icon_as_html ('{icons}comment/thumbs_down', '', '15px') . ' ' . $text;
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