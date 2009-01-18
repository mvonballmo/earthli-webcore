<?php

include_once ('webcore/config/default_make.php');

/** Create an environment customized for this deployment.
 * @return ENVIRONMENT */
function &custom_make_environment ()
{
  $Result =& default_make_environment ();
  /* Title displayed in the location/navigation menu bar. */

  $Result->title = 'My Domain';

  /* This is used only when sending email from a command line (otherwise, auto determined
     from the server context). Set it to your domain name. */

  $Result->default_host_name = 'MyDomain.com';

  /* Redirect exceptions to a different handler file. */

  $Result->exception_handler_page = '/albums/handle_exception.php';

  /* Set the logger stylesheet used by HTML loggers. */

  $Result->logger_style_sheet = '/albums/common/styles/log.css';

  return $Result;
}

/** Create a page object customized for this deployment.
 * @param ENVIRONMENT &$env
 * @return PAGE */
function &custom_make_page (&$env)
{
  $Result =& default_make_page ($env);

  /* See the documentation in </webcore/config/default_make.php>
   * for more configuration options. */

  /* set the locations of auxiliary resources; absolute paths are necessary for
   * sending mails in HTML. */

  $Result->set_path (Folder_name_root, '/');
  $Result->set_path (Folder_name_resources, '{root}/albums/common/');
  $Result->set_path (Folder_name_pages, '{root}/albums/');

  /* Change the logo, copyright and icon for the page. */

  $Result->template_options->logo_file = '{icons}logos/earthli_webcore_logo_full';
  $Result->template_options->logo_title = 'MyDomain.com';
  $Result->template_options->copyright = "Copyright &copy; " . date ('Y') . " MyDomain.com. All Rights Reserved.";

  $Result->icon_options->file_name = '{icons}logos/webcore_icon.png';
  $Result->icon_options->mime_type = 'image/png';

  /* Set up database options. */

  $Result->database_options->host = 'localhost';  // resets the default
  $Result->database_options->name = 'earthli';
  $Result->database_options->user_name = 'root';  // resets the default
  $Result->database_options->password = '';

  /* Set up mailing options. */

  $opts =& $Result->mail_options;
  $opts->SMTP_server = $env->default_host_name;
  $opts->webmaster_address = 'webmaster@' . $env->default_host_name;
  $opts->send_from_address = 'webmaster@' . $env->default_host_name;
  $opts->send_from_name = $Result->title->group;
  $opts->log_file_name = '/home/mydomain/log/general_mail.log';

  return $Result;
}

?>
