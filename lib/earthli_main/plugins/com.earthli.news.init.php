<?php

require_once ('news/config/news_application_engine.php');

class EARTHLI_NEWS_APPLICATION_ENGINE extends NEWS_APPLICATION_ENGINE
{
  function _init_application (&$page, &$app)
  {
    parent::_init_application ($page, $app);
    $app->storage->set_path ('/');
    $app->mail_options->publisher_user_name = 'auto_publisher';
    $app->mail_options->publisher_user_password = 'ch00tney';
    $app->register_class ('PANEL_MANAGER_HELPER', 'EARTHLI_NEWS_PANEL_MANAGER_HELPER');
  }
}

require_once ('news/gui/news_panel.php');

class EARTHLI_NEWS_PANEL_MANAGER_HELPER extends NEWS_PANEL_MANAGER_HELPER
{
  /**
   * Apply global options to a panel manager.
   * @param PANEL_MANAGER &$manager
   */
  function configure (&$manager)
  {
    parent::configure ($manager);
    $panel =& $manager->panel_at ('article');
    $panel->rows = 5;
  }
}

?>