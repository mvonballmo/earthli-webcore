<?php

/** */
require_once ('news/config/default_make_app.php');

/** Return a news application customized for your website.
  * @param PAGE &$page Global page object.
  * @return NEWS_APPLICATION */
function &custom_make_news_application (&$page)
{
  $Result =& default_make_news_application ($page);

  /* See the documentation in </webcore/config/default_make_app.php>
   * for more configuration options. */

  $Result->storage->set_path ('/');

  $opts =& $Result->mail_options;
  $opts->log_file_name = '/home/mydomain/log/news_mail.log';
  $opts->publisher_user_name = 'publisher';
  $opts->publisher_user_password = 'password';

  return $Result;
}

?>
