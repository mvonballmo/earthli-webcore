<?php

require_once ('[[_app_folder_]]/config/[[_prefix_lc_]]_application_engine.php');

class EARTHLI_[[_prefix_uc_]]_APPLICATION_ENGINE extends [[_prefix_uc_]]_APPLICATION_ENGINE
{
  function _init_application (&$page, &$app)
  {
    parent::_init_application ($page, $app);
    $app->storage->set_path ('/');  
    $app->mail_options->publisher_user_name = 'auto_publisher';
    $app->mail_options->publisher_user_password = 'ch00tney';
  }
}

?>
