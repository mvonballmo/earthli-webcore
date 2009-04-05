<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.6.0
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

/** */
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders the contents of an {@link EXCEPTION_SIGNATURE}.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.6.0
 */
class EXCEPTION_RENDERER extends OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param EXCEPTION_SIGNATURE $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $page_name = $obj->page_name;
    $dynamic_class_name = $obj->dynamic_class_name;
    $class_name = $obj->class_name;
    $application_description = $obj->application_description;
    $routine_name = $obj->routine_name;
    $error_message = $obj->message;

    if (! $this->_options->show_details)
    {
      $layer = $this->context->make_layer ('exception_details');
    }
?>
<div style="width: 75%; margin: auto">
  <p class="error"><?php echo $error_message; ?></p>
  <?php
    if (isset ($layer))
    {
  ?>
  <p class="detail"><?php echo $layer->draw_toggle (); ?> Click the arrow for more details.</p>
  <?php
    }
  ?>
</div>
<?php if (isset ($layer)) $layer->start (); ?>
<table class="chart" style="margin: auto">
  <tr>
    <td class="label">Page</td>
    <td>
      <?php
        $page_title = substr ($page_name, 0, 50);
        if ($page_title != $page_name)
        {
          echo "<span title=\"$page_name\">$page_title...</span>\n";
        }
        else
        {
          echo "$page_title\n";
        }
      ?>
    </td>
  </tr>
<?php
    if ($application_description)
    {
?>
  <tr>
    <td class="label">Application</td>
    <td><?php echo $application_description; ?></td>
  </tr>
<?php
    }
?>
<?php
    if ($routine_name)
    {
      if ($dynamic_class_name)
      {
        if ($dynamic_class_name != $class_name)
        {
?>
  <tr>
    <td class="label">Class</td>
    <td><code><?php echo $dynamic_class_name; ?></code> (<code><?php echo $class_name; ?>)</code></td>
  </tr>
<?php
        }
        else
        {
?>
  <tr>
    <td class="label">Class</td>
    <td><code><?php echo $class_name; ?></code></td>
  </tr>
<?php
        }
      }
      else
      {
?>
  <tr>
    <td class="label">Scope</td>
    <td><code>global</code></td>
  </tr>
<?php
      }
?>
  <tr>
    <td class="label">Routine</td>
    <td><code><?php if ($class_name == $routine_name) echo '&lt;constructor&gt;'; else echo $routine_name; ?></code></td>
  </tr>
<?php
    }
    else
    {
?>
  <tr>
    <td class="label">Scope</td>
    <td><code>global</code></td>
  </tr>
<?php
    }
?>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Server</td>
    <td><?php echo $this->env->server_info (); ?></td>
  </tr>
  <tr>
    <td class="label">Library</td>
    <td><?php echo 'WebCore ' . $this->env->version; ?></td>
  </tr>
  <?php
    if ($this->_options->include_browser_info)
    {
      $browser = $this->env->browser ();
  ?>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Browser</td>
    <td>
      <?php
        echo $browser->description_as_html ();
      ?>
    </td>
  </tr>
  <tr>
    <td class="label">OS</td>
    <td>
      <?php
        echo $browser->system_id ();
      ?>
    </td>
  </tr>
  <tr>
    <td class="label">User agent</td>
    <td>
      <?php
        $browser_title = substr ($browser->user_agent_string, 0, 50);
        if ($browser_title != $browser->user_agent_string)
        {
          echo "<span title=\"$browser->user_agent_string\">$browser_title...</span>\n";
        }
        else
        {
          echo "$browser_title\n";
        }
      ?>
    </td>
  </tr>
  <?php
    }

    if ($this->_options->include_page_data)
    {
  ?>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php
      $this->_show_array_as_html ('Post', $obj->variables_for (Var_type_post));
      $this->_show_array_as_html ('URL', $obj->variables_for (Var_type_get));
      $this->_show_array_as_html ('Cookie', $obj->variables_for (Var_type_cookie));
      $this->_show_array_as_html ('Uploads', $obj->variables_for (Var_type_upload));
    }
  ?>
</table>
<?php
    if (isset ($layer)) $layer->finish ();
  }

  protected function _show_array_as_html ($title, $arr)
  {
    if (sizeof ($arr))
    {
      $layer = $this->context->make_layer ('array_' . $title);
      $layer->name = 'array_' . $title;
      $layer->visible = false;
?>
<tr>
  <td class="label"><?php echo $title . ' ' . $layer->toggle_as_html (); ?></td>
  <td>
    <div><span class="field"><?php echo sizeof ($arr); ?></span> parameters</div>
    <?php $layer->start (); ?>
    <pre><?php print_r ($arr); ?></pre>
    <?php $layer->finish (); ?>
  </td>
</tr>
<?php
    }
  }

  /**
   * Outputs the object as plain text.
   * @param EXCEPTION_SIGNATURE $obj
   * @access private
   * @abstract
   */
  protected function _display_as_plain_text ($obj)
  {
    $page_name = $obj->page_name;
    $dynamic_class_name = $obj->dynamic_class_name;
    $class_name = $obj->class_name;
    $application_description = $obj->application_description;
    $routine_name = $obj->routine_name;
    $error_message = $obj->message;
    $browser = $this->env->browser ();

    echo $this->par ($error_message);
    echo $this->sep ();

    $table = new TEXT_TABLE_RENDERER ($this);

    $table->add_item ('Page', $page_name);

    if ($application_description)
    {
      $table->add_item ('Application', $application_description);
    }

    if ($routine_name)
    {
      if ($dynamic_class_name)
      {
        if ($dynamic_class_name != $class_name)
        {
          $table->add_item ('Class', "$dynamic_class_name ($class_name)");
        }
        else
        {
          $table->add_item ('Class', $class_name);
        }
      }
      else
      {
        $table->add_item ('Class', $class_name);
      }

      if ($class_name == $routine_name)
      {
        $table->add_item ('Routine', '<constructor>');
      }
      else
      {
        $table->add_item ('Routine', $routine_name);
      }
    }
    else
    {
      $table->add_item ('Scope', 'global');
    }

    $table->add_separator ();

    $table->add_item ('Server', $this->env->server_info ());
    $table->add_item ('Library', 'WebCore ' . $this->env->version);

    if ($this->_options->include_browser_info)
    {
      $table->add_separator ();
      $table->add_item ('Browser', $browser->description_as_plain_text ());
      $table->add_item ('OS', $browser->system_id ());
      $table->add_item ('User agent', $browser->user_agent_string);
    }

    $table->display ();

    echo $this->line ();

    if ($this->_options->include_page_data)
    {
      $this->_show_array_as_text ('Post', $obj->variables_for (Var_type_post));
      $this->_show_array_as_text ('URL', $obj->variables_for (Var_type_get));
      $this->_show_array_as_text ('Cookie', $obj->variables_for (Var_type_cookie));
      $this->_show_array_as_text ('Uploads', $obj->variables_for (Var_type_upload));
    }
  }

  /**
   * Echoes an array preceded by a title; 
   * @param string $title
   * @param array $arr
   * @access private
   */
  protected function _show_array_as_text ($title, $arr)
  {
    if (sizeof ($arr))
    {
      echo $this->par ($title . ':');
      print_r ($arr);
      echo $this->line ();
    }
  }

  /**
   * @return EXCEPTION_RENDERER_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new EXCEPTION_RENDERER_OPTIONS ();
  }
}

/**
 * Options used with the {@link EXCEPTION_RENDERER}.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class EXCEPTION_RENDERER_OPTIONS extends OBJECT_RENDERER_OPTIONS
{
  /**
   * Details are hidden in a {@link LAYER} if False.
   * @var boolean
   */
  public $show_details = false;

  /**
   * Send Get/Post/Cookie info with exception?
   * @var boolean
   */
  public $include_page_data = true;

  /**
   * Send browser info with exception?
   * @var boolean
   */
  public $include_browser_info = true;
}

?>