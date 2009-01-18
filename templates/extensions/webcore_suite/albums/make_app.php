<?php

/** */
require_once ('albums/config/default_make_app.php');

/** Return an album application customized for your website.
 * @param PAGE &$page Global page object.
 * @return ALBUM_APPLICATION */
function &custom_make_album_application (&$page)
{
  $Result =& default_make_album_application ($page);

  /* See the documentation in </webcore/config/default_make_app.php>
   * for more configuration options. */

  $Result->storage->set_path ('/');

  $opts =& $Result->mail_options;
  $opts->log_file_name = '/home/mydomain/log/albums_mail.log';
  $opts->publisher_user_name = 'publisher';
  $opts->publisher_user_password = 'password';

  return $Result;
}

?>
