<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
 * @since 2.6.0
 */

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

/** */
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders the contents of an {@link EXCEPTION_SIGNATURE}.
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
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
    /** @var $options EXCEPTION_RENDERER_OPTIONS */
    $options = $this->_options;
?>
  <p class="error"><?php echo $error_message; ?></p>
  <table class="basic columns left-labels">
    <tr>
      <th>Page</th>
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
      <th>Application</th>
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
      <th>Class</th>
      <td><code><?php echo $dynamic_class_name; ?></code> (<code><?php echo $class_name; ?>)</code></td>
    </tr>
  <?php
          }
          else
          {
  ?>
    <tr>
      <th>Class</th>
      <td><code><?php echo $class_name; ?></code></td>
    </tr>
  <?php
          }
        }
        else
        {
  ?>
    <tr>
      <th>Scope</th>
      <td><code>global</code></td>
    </tr>
  <?php
        }
  ?>
    <tr>
      <th>Routine</th>
      <td><code><?php if ($class_name == $routine_name) echo '&lt;constructor&gt;'; else echo $routine_name; ?></code></td>
    </tr>
  <?php
      }
      else
      {
  ?>
    <tr>
      <th>Scope</th>
      <td><code>global</code></td>
    </tr>
  <?php
      }
  ?>
    <tr>
      <th>Server</th>
      <td><?php echo $this->env->server_info (); ?></td>
    </tr>
    <tr>
      <th>Library</th>
      <td><?php echo 'WebCore ' . $this->env->version; ?></td>
    </tr>
    <?php
      if ($options->include_browser_info)
      {
        $browser = $this->env->browser ();
    ?>
    <tr>
      <th>Browser</th>
      <td>
        <?php
          echo $browser->description_as_html ();
        ?>
      </td>
    </tr>
    <tr>
      <th>OS</th>
      <td>
        <?php
          echo $browser->system_id ();
        ?>
      </td>
    </tr>
    <tr>
      <th>User agent</th>
      <td>
        <?php echo $browser->user_agent_string; ?>
      </td>
    </tr>
    <?php
      }

      if ($options->include_page_data)
      {
        $this->_show_array_as_html ('Post', $obj->variables_for (Var_type_post));
        $this->_show_array_as_html ('URL', $obj->variables_for (Var_type_get));
        $this->_show_array_as_html ('Cookie', $obj->variables_for (Var_type_cookie));
        $this->_show_array_as_html ('Uploads', $obj->variables_for (Var_type_upload));
      }
    ?>
  </table>
<?php
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
  <th><?php echo $title . ' ' . $layer->toggle_as_html (); ?></th>
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

    /** @var $options EXCEPTION_RENDERER_OPTIONS */
    $options = $this->_options;

    if ($options->include_browser_info)
    {
      $table->add_separator ();
      $table->add_item ('Browser', $browser->description_as_plain_text ());
      $table->add_item ('OS', $browser->system_id ());
      $table->add_item ('User agent', $browser->user_agent_string);
    }

    $table->display ();

    echo $this->line ();

    if ($options->include_page_data)
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
 * @version 3.4.0
 * @since 2.6.0
 * @access private
 */
class EXCEPTION_RENDERER_OPTIONS extends OBJECT_RENDERER_OPTIONS
{
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