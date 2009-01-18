<?php

require_once ('webcore/db/query.php');

class DIETING_STATISTIC_QUERY extends QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    QUERY::apply_defaults ();
    $this->set_table ('stats obj');
    $this->set_day_field ('date');
    $this->order_by_recent ();
  }

  function _make_object ()
  {
    $class_name = $this->context->final_class_name ('DIETING_STATISTIC', 'diet/obj/dieting_statistic.php');
    return new $class_name ($this->context);
  }
}
?>