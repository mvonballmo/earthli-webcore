<?php

/** */
require_once ('recipes/config/default_make_app.php');

/** Return a recipes application customized for your website.
  * @param PAGE &$page Global page object.
  * @return RECIPE_APPLICATION */
function &custom_make_recipe_application (&$page)
{
  $Result =& default_make_recipe_application ($page);

  /* See the documentation in </webcore/config/default_make_app.php>
   * for more configuration options. */

  $Result->storage->set_path ('/');
  $Result->set_path (Folder_name_application, '{root}/recipes/');

  $opts =& $Result->mail_options;
  $opts->log_file_name = '/home/mydomain/log/projects_mail.log';
  $opts->publisher_user_name = 'publisher';
  $opts->publisher_user_password = 'password';

  return $Result;
}

?>
