<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/db/query.php');

/**
 * Return {@link SUBSCRIBER}s (which may or may not correspond to {@link USER}s).
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class SUBSCRIBER_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'subscribers';

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('subscribers.*');
    $this->set_table ($this->app->table_names->subscribers . ' subscribers');
    $this->set_order ('subscribers.email ASC');
  }

  /**
   * @param string $name
   * @return USER
   */
  function object_at_email ($email)
  {
    return $this->object_with_field ('email', $email);
  }

  /**
   * @return SUBSCRIBER
    * @access private
    */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('SUBSCRIBER', 'webcore/obj/subscriber.php');
    return new $class_name ($this->app);
  }
}

/**
 * Return a list of subscriptions.
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.2.1
 */
class SUBSCRIPTION_QUERY extends SUBSCRIBER_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->set_select ('DISTINCT(subscribers.email), subscribers.*');
    $this->add_table ($this->app->table_names->subscriptions . ' subs', 'subscribers.id = subs.subscriber_id');
  }

  /**
   * Restrict to one of the given kind/id combinations.
   * @param array[string][integer] $choices
   */
  function restrict_kinds ($choices)
  {
    foreach ($choices as $kind => $id)
      $restrictions [] = "(ref_id = $id) AND (kind = '$kind')";

    $this->restrict_to_one_of ($restrictions);
  }
}

?>