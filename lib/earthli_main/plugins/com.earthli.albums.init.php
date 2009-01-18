<?php

require_once ('albums/config/album_application_engine.php');

class EARTHLI_ALBUM_APPLICATION_ENGINE extends ALBUM_APPLICATION_ENGINE
{
  function _init_application (&$page, &$app)
  {
    parent::_init_application ($page, $app);
    $app->storage->set_path ('/');
    $app->register_page_template ('webcore/pages/index.php', 'plugins/albums/fast_index.php');
    $app->mail_options->publisher_user_name = 'auto_publisher';
    $app->mail_options->publisher_user_password = 'ch00tney';
  }
}

?>