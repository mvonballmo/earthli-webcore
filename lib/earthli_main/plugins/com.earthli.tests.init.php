<?php

require_once ('webcore/tests/config/test_harness_application_engine.php');

class EARTHLI_TEST_APPLICATION_ENGINE extends TEST_HARNESS_APPLICATION_ENGINE
{
  function _init_application (&$page, &$app)
  {
    parent::_init_application ($page, $app);
    $app->database_options->name = 'test';
    $app->storage_options->login_user_name = 'test_user';
  }
}

?>