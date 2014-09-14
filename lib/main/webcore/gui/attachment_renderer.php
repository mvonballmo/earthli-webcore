<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link ATTACHMENT}s.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
 */
class ATTACHMENT_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param ATTACHMENT $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $file_name = $obj->full_file_name ();
    $file_url = htmlentities ($obj->full_url ());
?>
    <table class="basic columns left-labels">
      <tr>
        <th>Name</th>
        <td>
          <?php echo $obj->original_file_name; ?>
        </td>
      </tr>
      <tr>
        <th>Size</th>
        <td>
          <?php echo file_size_as_text ($obj->size); ?>
        </td>
      </tr>
      <tr>
        <th>Type</th>
        <td>
          <?php
            echo $obj->mime_type;
          ?>
        </td>
      </tr>
<?php

    if ($obj->exists () && ($obj->original_file_name != $obj->file_name) && $this->_options->show_interactive)
    {
?>
      <tr>
        <th></th>
        <td>
          <?php
          $this->page->show_message('Stored as <span class="field">' . $obj->file_name . '</span> on the server.', 'info')
          ?>
        </td>
      </tr>
<?php
    }
    echo "\n";

    if ($this->_options->show_interactive && ! $obj->is_image)
    {
      ?>
      <tr>
        <th></th>
        <td>
        <?php
        $menu = $this->app->make_menu ();
        $menu->append ('Download', $file_url, '{icons}buttons/download');
        $menu->renderer = $this->app->make_menu_renderer ();
        $menu->display ();
        ?>
        </td>
      </tr>
      <?php
    }
?>
    </table>

<?php
    if ($obj->is_image)
    {
      $this->_draw_html_image ($obj, $file_url);
    }
    elseif ($obj->is_archive)
    {
      $this->_draw_html_archive ($obj, $file_name);
    }
    else
    {
      $this->_echo_html_description ($obj);
    }

    $this->_echo_html_user_information ($obj, 'info-box-bottom');
  }
  
  /**
   * Show the image for this given file name.
   * @param ATTACHMENT $obj
   * @param string $file_url
   * @access private
   */
  protected function _draw_html_image ($obj, $file_url)
  {
    $class_name = $this->app->final_class_name ('IMAGE_METRICS', 'webcore/util/image.php');
    /** @var $metrics IMAGE_METRICS */
    $metrics = new $class_name ();
    $metrics->set_url ($file_url);
    $metrics->resize_to_fit (800, 800);
    if ($metrics->loaded ())
    {
?>
<div style="width: <?php echo $metrics->width (); ?>px">
  <div class="text-flow">
    <?php $this->_echo_html_description ($obj); ?>
  </div>
  <div>
    <?php
      if ($this->_options->show_interactive) 
        echo $metrics->as_html ($obj->title_as_plain_text ());
      else 
        echo $metrics->as_html_without_link ($obj->title_as_plain_text ());
    ?>    
  </div>
  <?php
    if ($this->_options->show_interactive && $metrics->was_resized)
    {
  ?>
  <div class="subdued">
    Resized from
    <?php echo $metrics->original_width; ?> x <?php echo $metrics->original_height; ?> to
    <?php echo $metrics->constrained_width; ?> x <?php echo $metrics->constrained_height; ?>.
    Click to show full size in a separate window.
  </div>
  <?php
    }
  ?>
</div>
<?php
    }
    else
    {
      echo "<div class=\"error\">[$metrics->url] could not be opened for preview.</div>";    
    }
  }

  /**
   * Show the {@link ARCHIVE} for this given file name.
   * @param ATTACHMENT $obj
   * @param string $file_name
   * @access private
   */
  protected function _draw_html_archive ($obj, $file_name)
  {
    $this->_echo_html_description ($obj);

    if ($obj->is_archive)
    {
      $class_name = $this->app->final_class_name ('ARCHIVE', 'webcore/util/archive.php');
      /** @var $archive ARCHIVE */
      $archive = new $class_name ($file_name);      
      echo '<h2>Files</h2>';
      echo '<br>';
      echo '<table class="basic columns">';
      echo '<tr><th>Name</th><th>Size</th></tr>';
      $archive->for_each (new CALLBACK_METHOD ('list_file_as_html', $this));
      echo '</table>';
    }
    else if ($obj->mime_type == 'text/plain')
    {
      echo '<pre>';
      if ($this->_options->preferred_text_length)
      {
        $handle = fopen ($file_name, 'r+');
        echo fread ($handle, $this->_options->preferred_text_length);
      }
      else
      {
        readfile ($file_name);
      }
      echo '</pre>';
    }
  }
  
  /**
   * Outputs the object as plain text.
   * @param object $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    $this->_echo_plain_text_user_information ($obj);

    $file_url = $obj->full_url ();
    $file_name = url_to_file_name ($file_url);
    $file_url = htmlentities ($file_url);

    echo $this->line ('[Name]: ' . $obj->original_file_name);
    echo $this->line ('[Size]: ' . file_size_as_text ($obj->size));
    echo $this->line ('[Type]: ' . $obj->mime_type);
    echo $this->par (' [URL]: <' . $file_url . '>');

    $this->_echo_plain_text_description ($obj);

    if ($obj->is_archive)
    {
      $this->_draw_text_archive ($obj, $file_name);
    }
    else if ($obj->mime_type == 'text/plain')
    {
      if ($this->_options->preferred_text_length)
      {
        $handle = fopen ($file_name, 'r+');
        echo fread ($handle, $this->_options->preferred_text_length);
      }
      else
      {
        readfile ($file_name);
      }
    }
  }

  /**
   * Show the {@link ARCHIVE} for this given file name.
   * @param ATTACHMENT $obj
   * @param string $file_name
   * @access private
   */
  protected function _draw_text_archive ($obj, $file_name)
  {
    $class_name = $this->app->final_class_name ('ARCHIVE', 'webcore/util/archive.php');
    /** @var ARCHIVE $archive */
    $archive = new $class_name ($file_name);      
    echo $this->line ('[Files]');
    echo $this->sep ();
    $this->_longest_name = 0;
    $archive->for_each (new CALLBACK_METHOD ('list_file_as_text', $this));
    foreach ($this->_file_entries as $entry)
    {
      echo $this->line ($entry->name . str_repeat (' ', $this->_longest_name - strlen ($entry->name)) . ' (' . file_size_as_text ($entry->size) . ')');
    }
  }
  
  /**
   * Draw information for a file as HTML.
   * Passed as a {@link CALLBACK} if the attachment is an {@link ARCHIVE}.
   * @param ARCHIVE $archive
   * @param COMPRESSED_FILE_ENTRY $entry
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   * @access private
   */
  public function list_file_as_html ($archive, $entry, $error_callback = null)
  {
    $ft = $this->context->file_type_manager ();
    $url = new FILE_URL ($entry->name);
    $icon_with_text = $this->context->get_icon_with_text($ft->icon_url('', $url->extension()), Sixteen_px, $entry->name);

    echo '<tr><td>' . $icon_with_text  . '</td><td>' . file_size_as_text ($entry->size) . '</td></tr>';
  }

  /**
   * Draw information for a file as plain text.
   * Passed as a {@link CALLBACK} if the attachment is an {@link ARCHIVE}.
   * @param ARCHIVE $archive
   * @param COMPRESSED_FILE_ENTRY $entry
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   * @access private
   */
  public function list_file_as_text ($archive, $entry, $error_callback = null)
  {
    $this->_file_entries [] = $entry;
    $this->_longest_name = max ($this->_longest_name, strlen ($entry->name));
  }

  /**
   * @var integer
   * @access private
   */
  protected $_longest_name;

  /**
   * @var COMPRESSED_FILE_ENTRY[]
   * @see COMPRESSED_FILE_ENTRY
   * @access private
   */
  protected $_file_entries;
}

?>