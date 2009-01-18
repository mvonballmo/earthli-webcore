<?php

function make_poll_application (&$page)
{
  include_once ('polls/sys/poll_application.php');
  return new POLL_APPLICATION ($page);
}

?>