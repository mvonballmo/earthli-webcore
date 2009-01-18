<?php

require_once ('recipes/config/recipe_application_engine.php');

class EARTHLI_RECIPE_APPLICATION_ENGINE extends RECIPE_APPLICATION_ENGINE
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