<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.1
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
require_once ('webcore/forms/form.php');

/**
 * Executes the {@link APP_WIZARD_TASK} based on user input.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.1
 */
class EXECUTE_APP_WIZARD_FORM extends FORM
{
  /**
   * @var string
   */
  var $name = 'app_wizard';
  /**
   * @var string
   */
  var $button = 'Generate';
  /**
   * @var string
   */
  var $button_icon = '{icons}buttons/ship';
  
  /**
   * @param CONTEXT &$context
   */
  function EXECUTE_APP_WIZARD_FORM (&$context)
  {
    FORM::FORM ($context);

    $field = new TEXT_FIELD ();
    $field->id = 'app_title';
    $field->title = 'Title';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'app_id';
    $field->title = 'Identifier';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'app_url';
    $field->title = 'URL';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'app_folder';
    $field->title = 'Folder';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'app_prefix';
    $field->title = 'Prefix';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'folder_name';
    $field->title = 'Folder Name';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'entry_name';
    $field->title = 'Entry Name';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'author_name';
    $field->title = 'Author Name';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);

    $field = new EMAIL_FIELD ();
    $field->id = 'author_email';
    $field->title = 'Author Email';
    $field->sticky = TRUE;
    $field->required = TRUE;
    $this->add_field ($field);
  }
  
  function load_with_defaults ()
  {
    $this->load_from_client ('app_title', '');
    $this->load_from_client ('app_id', '');
    $this->load_from_client ('app_prefix', '');
    $this->load_from_client ('app_url', '');
    $this->load_from_client ('app_folder', '');
    $this->load_from_client ('folder_name', '');
    $this->load_from_client ('entry_name', '');
    $this->load_from_client ('author_name', '');
    $this->load_from_client ('author_email', '');
  }
  
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->load_with_defaults ();
  }

  /**
   * Execute the form.
   * @param TASK &$obj
   * @access private
   */
  function commit (&$obj)
  {
    $obj->app_title = $this->value_for ('app_title');
    $obj->app_id = $this->value_for ('app_id');
    $obj->app_prefix = $this->value_for ('app_prefix');
    $obj->app_url = $this->value_for ('app_url');
    $obj->app_folder = $this->value_for ('app_folder');
    $obj->folder_name = $this->value_for ('folder_name');
    $obj->entry_name = $this->value_for ('entry_name');
    $obj->author_name = $this->value_for ('author_name');
    $obj->author_email = $this->value_for ('author_email');
    $obj->owns_page = FALSE;
    $obj->debug = FALSE;
    $obj->execute ();
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->start ();

    $renderer->draw_text_line_row ('app_title');    
    $renderer->draw_text_line_row ('app_id');    
    $renderer->draw_text_line_row ('app_prefix');    
    $renderer->draw_text_line_row ('app_url');    
    $renderer->draw_text_line_row ('app_folder');    
    $renderer->draw_text_line_row ('folder_name');    
    $renderer->draw_text_line_row ('entry_name');    
    $renderer->draw_text_line_row ('author_name');    
    $renderer->draw_text_line_row ('author_email');    
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    
    $renderer->finish ();
  }
}

?>