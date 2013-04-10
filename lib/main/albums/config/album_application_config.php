<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage config
 * @version 3.3.0
 * @since 2.9.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/config/application_config.php');

/**
 * @package albums
 * @subpackage config
 * @version 3.3.0
 * @since 2.9.0
 */
class ALBUM_APPLICATION_DISPLAY_OPTIONS extends APPLICATION_DISPLAY_OPTIONS
{
  /**
   * Return the list of {@link JOURNAL::$weather_type}s.
   * @see PROPERTY_VALUE
   * @return PROPERTY_VALUE[]
   */
  public function weather_icons ()
  {
    if (! isset ($this->_weather_icons))
    {
      $this->_init_weather_icons ();
    }
    return $this->_weather_icons;
  }

  /**
   * Add a value for a {@link JOURNAL::$weather_type}.
   * @param string $title
   * @param string $icon Location of an image.
   * @param integer $value Value stored in the database.
   */
  public function add_weather_icon ($value, $title, $icon)
  {
    include_once ('webcore/sys/property.php');
    $prop = new PROPERTY_VALUE ($this->context);
    $prop->value = $value;
    $prop->title = $title;
    $prop->icon = '{' . Folder_name_app_icons . '}weather/' . $icon;
    $this->_weather_icons [$value] = $prop;
  }

  /**
   * Initialize the initial list of comment icons.
   * Called from {@link weather_icons()}.
   * @access private
   */
  protected function _init_weather_icons ()
  {
    $this->add_weather_icon (0, 'Sunny', 'sunny');
    $this->add_weather_icon (1, 'Mostly Sunny', 'mostly_sunny');
    $this->add_weather_icon (2, 'Partly Sunny', 'partly_sunny');
    $this->add_weather_icon (3, 'High Clouds', 'high_clouds');
    $this->add_weather_icon (4, 'Mixed Sun and Clouds', 'mixed_sun_and_clouds');
    $this->add_weather_icon (5, 'Mostly Cloudy', 'mostly_cloudy');
    $this->add_weather_icon (6, 'Hazy', 'hazy');
    $this->add_weather_icon (7, 'Cloudy', 'cloudy');
    $this->add_weather_icon (8, 'Mixed Rain and Sun', 'mixed_rain_and_sun');
    $this->add_weather_icon (9, 'Drizzle', 'drizzle');
    $this->add_weather_icon (10, 'Rainy', 'rain');
    $this->add_weather_icon (11, 'Heavy Rain', 'heavy_rain');
    $this->add_weather_icon (12, 'Mixed Rain and Snow', 'mixed_rain_and_snow');
    $this->add_weather_icon (13, 'Light Snow', 'light_snow');
    $this->add_weather_icon (14, 'Snowy', 'snow');
    $this->add_weather_icon (15, 'Heavy Snow', 'heavy_snow');
    $this->add_weather_icon (16, 'Mixed Sun and Thunderstorms', 'mixed_sun_and_thunderstorms');
    $this->add_weather_icon (17, 'Mixed Snow and Thunderstorms', 'mixed_snow_and_thunderstorms');
    $this->add_weather_icon (18, 'Thunderstorms', 'thunderstorms');
    $this->add_weather_icon (19, 'Heavy Thunderstorms', 'heavy_thunderstorms');
    $this->add_weather_icon (20, 'Hail', 'hail');
    $this->add_weather_icon (21, 'Light Fog', 'light_fog');
    $this->add_weather_icon (22, 'Heavy Fog', 'heavy_fog');
  } 

  /**
   * @see PROPERTY_VALUE
   * @var PROPERTY_VALUE[]
   * @access private
   */
  protected $_weather_icons;
}

?>