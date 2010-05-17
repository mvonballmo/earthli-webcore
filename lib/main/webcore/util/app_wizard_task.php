<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.7.1
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
require_once ('webcore/sys/task.php');

/**
 * Channel used to log all wizard-related messages.
 * @see APP_WIZARD_TASK
 */
define ('Msg_channel_app_wizard', 'Wizard');

/**
 * Builds a new webcore application.
 * @package webcore
 * @subpackage tests
 * @version 3.3.0
 * @since 2.6.0
 */
class APP_WIZARD_TASK extends TASK
{
  /**
   * Icon to show in the title bar when executing.
   * @var string
   */
  public $icon = '{icons}buttons/create';

  /**
   * Log all messages in this channel.
   * @var string
   */
  public $log_channel = Msg_channel_app_wizard;
  
  /**
   * @var string
   */
  public $app_title;

  /**
   * @var string
   */
  public $app_id;

  /**
   * @var string
   */
  public $app_prefix;

  /**
   * @var string
   */
  public $app_url;

  /**
   * @var string
   */
  public $app_folder;

  /**
   * @var string
   */
  public $folder_name;

  /**
   * @var string
   */
  public $entry_name;

  /**
   * @var string
   */
  public $author_name;

  /**
   * @var string
   */
  public $author_email;

  /**
   * Return a formatted title for this task.
   * Used as the {@link PAGE_TITLE::$subject} when executed.
   * @return string
   */
  public function title_as_text ()
  {
    return 'Generating application...';
  }
  
  /**
   * @access private
   */
  protected function _execute ()
  {
    $input_path = $this->env->source_path ();
    $input_path->append ('wizards/new_application');

    $config = parse_ini_file ($input_path->appended_as_text ('config.ini'), true);
    $paths = read_array_index ($config, 'recurse_paths');
    $exts = read_array_index ($config, 'extensions');
    
    $files = array ();
    foreach ($paths as $path)
    {
      $files = array_merge ($files, file_list_for ($input_path->appended_as_text ($path), $path, true));
    }

    $output_path = $this->env->source_path ();
    $output_path->append ('wizards/output/' . $this->app_folder);
    
    foreach ($files as $file_name)
    {
      $in = $input_path;
      $in->append ($file_name);
      $ext = $in->extension ();

      $out = $output_path;
      $out->append ($this->_apply_templates ($file_name));
      
      if (in_array ($ext, $exts))
      {
        $this->_log ('Read [' . $in->as_text () . ']');
        $text = file_get_contents ($in->as_text ());
        $text = $this->_apply_templates ($text);
        
        $out->write_text_file ($text);
        $this->_log ('Wrote [' . $out->as_text () . ']', Msg_type_info);      
      }
      else
      {
        $out->ensure_path_exists ();      
        copy ($in->as_text (), $out->as_text ());
        $this->_log ('Copied to [' . $out->as_text () . ']', Msg_type_info);      
      }      
    }
  }
  
  protected function _apply_templates ($text)
  {
    return str_replace (array ('[[_app_title_]]'
                            , '[[_app_name_]]'
                            , '[[_app_url_]]'
                            , '[[_app_folder_]]'
                            , '[[_prefix_uc_]]'
                            , '[[_prefix_lc_]]'
                            , '[[_prefix_mc_]]'
                            , '[[_folder_name_uc_]]'
                            , '[[_folder_name_lc_]]'
                            , '[[_folder_name_mc_]]'
                            , '[[_entry_name_uc_]]'
                            , '[[_entry_name_lc_]]'
                            , '[[_entry_name_mc_]]'
                            , '[[_author_name_]]'
                            , '[[_author_email_]]')
                     , array ($this->app_title
                            , $this->app_id
                            , $this->app_url
                            , $this->app_folder
                            , strtoupper ($this->app_prefix)
                            , strtolower ($this->app_prefix)
                            , ucfirst (strtolower ($this->app_prefix))
                            , strtoupper ($this->folder_name)
                            , strtolower ($this->folder_name)
                            , ucfirst (strtolower ($this->folder_name))
                            , strtoupper ($this->entry_name)
                            , strtolower ($this->entry_name)
                            , ucfirst (strtolower ($this->entry_name))
                            , $this->author_name
                            , $this->author_email)
                     , $text);
  }
}

?>