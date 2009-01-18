<?php

require_once ('webcore/obj/renderable.php');
require_once ('webcore/sys/date_time.php');

class DIETING_STATISTIC extends RENDERABLE
{
  var $weight;
  var $body_fat;
  var $date;
  var $user_id;

  function DIETING_STATISTIC (&$context)
  {
    RENDERABLE::RENDERABLE ($context);
    $this->date = $context->make_date_time ();
  }

  function load (&$db)
  {
    parent::load ($db);

    $this->user_id = $db->f ("user_id");
    $this->weight = $db->f ("lbs_weight");
    $this->body_fat = $db->f ("percent_body_fat");
    $this->date->set_from_iso ($db->f ("date"));
  }

  function store_to (&$storage)
  {
    parent::store_to ($storage);

    $tname = $this->_table_name ();
    $storage->add ($tname, 'user_id', Field_type_integer, $this->user_id, Storage_action_create);
    $storage->add ($tname, 'lbs_weight', Field_type_string, $this->weight);
    $storage->add ($tname, 'percent_body_fat', Field_type_string, $this->body_fat);
    $storage->add ($tname, 'date', Field_type_date_time, $this->date);
  }
  
  function equals (&$other)
  {
    return ($other->weight == $this->weight) && ($other->body_fat == $this->body_fat) && $other->date->equals ($this->date);
  }
  
  function page_name ()
  {
    return 'dieting_statistic.php';
  }
  
  function raw_title ()
  {
    $f = $this->date->formatter ();
    $f->type = Date_time_format_short_date;
    $f->clear_flags ();
    return $this->date->format ($f) . ' / ' . $this->weight . 'lbs / ' . $this->body_fat . '%';
  }

  function _table_name ()
  {
    return 'stats';
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_commands:
        include_once ('diet/cmd/dieting_statistic_commands.php');
        return new DIETING_STATISTIC_COMMANDS ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>