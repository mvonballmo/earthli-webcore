<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/sys/system.php');

/**
 * Messages sent from {@link IMAGE}s are recorded in this channel.
 * Used with {@link log_message()}.
 * @access private
 */
define ('Msg_channel_image', 'Imaging');
/**
 * GIF image type. Defined for backwards-compatibility
 * @access private
 */
define ('Image_type_GIF', 1);
/**
 * JPG image type. Defined for backwards-compatibility
 * @access private
 */
define ('Image_type_JPG', 2);
/**
 * PNG image type. Defined for backwards-compatibility
 * @access private
 */
define ('Image_type_PNG', 3);

/**
 * An in-memory PNG, JPG or GIF.
 * Use {@link set_file()} to set the file name. If the file exists and is an image, {@link loadable()}
 * returns True. If the image is loadable, you can access {@link $properties} to find out the image dimensions
 * and (possibly) EXIF file information associated with it. Call {@link load_from_file()} to read the image
 * data and {@link resize()} or {@link save_to_file()}. Use {@link metrics()} to get an object which can format
 * an image as HTML with constrained dimensions.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */
class IMAGE extends RAISABLE
{
  /**
   * @var IMAGE_PROPERTIES
   */
  public $properties;

  /**
   * @param boolean $use_internal_exif Use the PHP function {@link PHP_MANUAL#exif_read_data} if available.
   */
  public function __construct ($use_internal_exif = true)
  {
    $this->properties = new IMAGE_PROPERTIES ($use_internal_exif);
  }

  /**
   * @return boolean
   */
  public function exists ()
  {
    return $this->properties->exists ();
  }

  /**
   * Direct this image to an image in a file.
   * This does not load the image: use {@link loadable()} to see if the given file is a valid image file.
   * @param string $name Fully-resolved path to the image. May be a url.
   * @param boolean $include_exif If True, the loader will attempt to extract EXIF digital camera information from the file. This uses the PHP function {@link PHP_MANUAL#exif_read_data} and is known to be slower for some images.
   */
  public function set_file ($name, $include_exif = false)
  {
    $this->properties->load_from_file ($name, $include_exif);
  }

  /**
   * Can the set file be loaded?
   * @return boolean
   */
  public function loadable ()
  {
    return $this->exists () && ($this->properties->php_type > 0) && ($this->properties->php_type <= 4);
  }

  /**
   * Can the current file be saved to a new file?
   * The image must be {@link loaded()}, a local file and {@link saveable_to()}
   * must return True.
   * @return boolean
   */
  public function saveable ()
  {
    return $this->loaded () && ! empty ($this->properties->file_name) && $this->saveable_to ($this->properties->php_type);
  }

  /**
   * Can the current file be saved to the given type?
   * Works like {@link saveable()}, but for the specified 'type'. GIFs are not
   * yet saveable because GD does not support it.
   * @param integer $type One of the PHP image type constants.
   * @return boolean
   */
  public function saveable_to ($type)
  {
    if (! $this->loaded ())
    {
      return false;
    }
    
    if (($type == Image_type_JPG) || ($type == Image_type_PNG))
    {
      return true;
    }
    
    if ($type == Image_type_GIF)
    {
      $gd_caps = gd_info ();
      return $gd_caps ['GIF Create Support'];
    }
    
    return false;
  }

  /**
   * Is the image data loaded?
   * @return boolean
   */
  public function loaded ()
  {
    return isset ($this->_data);
  }

  /**
   * Load image data from the current file name.
   */
  public function load_from_file ()
  {
    $this->assert ($this->loadable (), 'Loading images of type [' . $this->properties->mime_type . '] is not supported.', 'load_from_file', 'IMAGE');

    $php_errormsg = null;
    
    switch ($this->properties->php_type)
    {
    case 1:
      $this->_data = @imagecreatefromgif ($this->properties->file_name);
      break;
    case 2:
      $this->_data = @imagecreatefromjpeg ($this->properties->file_name);
      break;
    case 3:
      $this->_data = @imagecreatefrompng ($this->properties->file_name);
      break;
    default:
      $this->raise ('loadable() claimed support for (' . $this->properties->mime_type . ')', 'load_from_file', 'IMAGE');
    }

    if (isset ($php_errormsg))
    {
      $this->raise ('Could not load [' . $this->properties->file_name . ']: ' . $php_errormsg, 'load_from_file', 'IMAGE');
    }
  }

  /**
   * Save loaded image data to the given file name.
   * @param string $name Full path to the image file.
   * @param integer $type PHP image type constant specifying the type of image to create in that file.
   */
  public function save_to_file ($name, $type = null)
  {
    if ($this->loaded () && ! isset ($type))
    {
      $type = $this->properties->php_type;
    }

    $this->assert ($this->saveable_to ($type), 'Saving images to type [' . $this->properties->mime_type . '/' . $this->properties->php_type . '] is not supported.', 'save_to_file', 'IMAGE');

    $url = new FILE_URL ($name);
    $url->ensure_path_exists ();

    if (! isset ($type))
    {
      $type = $this->properties->php_type;
    }

    $opts = global_file_options ();
    switch ($type)
    {
    case Image_type_GIF:
      imagegif ($this->_data, $name);
      chmod ($name, $opts->default_access_mode);
    case Image_type_JPG:
      imagejpeg ($this->_data, $name);
      chmod ($name, $opts->default_access_mode);
      break;
    case Image_type_PNG:
      imagepng ($this->_data, $name);
      chmod ($name, $opts->default_access_mode);
      break;
    default:
      $this->raise ('saveable_to() claimed for (' . $this->properties->mime_type . '/' . $this->properties->php_type . ')', 'save_from_file', 'IMAGE');
    }
  }

  /**
   * @return IMAGE_METRICS
   */
  public function metrics ()
  {
    $Result = new IMAGE_METRICS ();
    $Result->set_image ($this);
    return $Result;
  }

  /**
   * Resize the loaded image to the given dimensions.
   * Aspect ratio is not preserved.
   * @param integer $width
   * @param integer $height
   */
  public function resize ($width, $height)
  {
    $this->_assert_loaded ('resize');
    $metrics = $this->metrics ();
    $metrics->resize ($width, $height);
    $this->_resize_to ($metrics);
  }

  /**
   * Make the loaded image fit the given dimensions.
   * Aspect ratio is preserved and the image width and height will not exceed the given constraints.
   * @param integer $width
   * @param integer $height
   */
  public function resize_to_fit ($width, $height)
  {
    $this->_assert_loaded ('resize_to_fit');
    $metrics = $this->metrics ();
    $metrics->resize_to_fit ($width, $height);
    $this->_resize_to ($metrics);
  }

  /**
   * Apply the metrics to this image.
   * @param IMAGE_METRICS
   * @access private
   */
  protected function _resize_to ($metrics)
  {
    $this->_assert_loaded ('_resize');

    /* Create the new image at the correct size. */

    $width = $metrics->width ();
    $height = $metrics->height ();
    $new_data = imagecreatetruecolor ($width, $height);

    /* Copy the image, preserving transparency. Paletted images
     * are automatically converted to alpha-blended images, resulting
     * in slightly larger file sizes.
     */

    if ($this->properties->php_type == Image_type_GIF)
    {
      /* GIF89a transparency is not preserved. See  */

/*
      $transparent = imagecolortransparent($this->_data);
      imagefill($new_data, 0, 0, $transparent);
      imagecolortransparent($new_data, $transparent);
*/
      imagecopyresampled ($new_data, $this->_data, 0, 0, 0, 0, $width, $height, $this->properties->width, $this->properties->height);
/*
      $colors_handle = ImageCreateTrueColor( $width, $height );
      imagecopymerge ($colors_handle, $new_data, 0, 0, 0, 0, $width, $height, 100);
      imagetruecolortopalette($new_data, true, 256);
      ImageColorMatch( $colors_handle, $new_data );
      ImageDestroy($colors_handle);
*/
    }
    else
    {
      imagealphablending ($new_data, false);
      imagecopyresampled ($new_data, $this->_data, 0, 0, 0, 0, $width, $height, $this->properties->width, $this->properties->height);
      imagesavealpha ($new_data, true);
    }

    /* Clean up and reassign the new picture. */

    imagedestroy ($this->_data);
    $this->_data = $new_data;

    /* Save the new size of the image */

    $this->properties->width = $width;
    $this->properties->height = $height;
  }

  /**
   * @param string $method_name
   * @access private
   */
  protected function _assert_loaded ($method_name)
  {
    $this->assert ($this->loaded (), 'image is not loaded (' . $this->properties->mime_type . ')', $method_name, 'IMAGE');
  }
}

/**
 * Properties of an image.
 * Reads the EXIF file information from digital photographs, if available. Not all fields
 * will be available for all cameras.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */
class IMAGE_PROPERTIES
{
  /**
   * Fully qualified local system file name.
   * May be empty if the {@link $url} is non-local.
   * @var string
   */
  public $file_name;

  /**
   * Fully qualified URL of the file
   * May be empty if the {@link $file_name} is a local file outside of the document root.
   * @var string
   */
  public $url;

  /**
   * Mime type for the loaded file.
   * Detects most common image formats; tries to use the PHP function "image_type_to_mime_type".
   * @var string
   */
  public $mime_type = '';

  /**
   * Internal PHP type for the file.
   * Used to determine the mime type by both PHP and the WebCore.
   * @var integer
   */
  public $php_type = 0;

  /**
   * Width, in pixels.
   * @var integer
   */
  public $width = 0;

  /**
   * Height, in pixels.
   * @var integer
   */
  public $height = 0;

  /**
   * Time the picture was taken.
   * There is no industry-standard way of storing this date, but every effort is made to extract
   * a usable date.
   * @var DATE_TIME
   */
  public $time_created;

  /**
   * Camera-specific aperture setting.
   * @var integer
   */
  public $aperture = 0;

  /**
   * ISO-speed at which the picture was taken.
   * @var integer
   */
  public $iso_speed = 0;

  /**
   * Was the flash used?
   * @var boolean
   */
  public $used_flash = false;

  /**
   * Is this a color photograph?
   * @var boolean
   */
  public $is_color = true;

  /**
   * Vendor-specific string describing the vendor.
   * @var string
   */
  public $camera_make = '';

  /**
   * Vendor-specific string describing the camera.
   * @var string
   */
  public $camera_model = '';

  /**
   * Reference to the full exif information returned by PHP.
   * May not be set.
   * @var array
   */
  public $exif;

  /**
   * Uses the function {@link PHP_MANUAL#exif_read_data} if <code>True</code>.
   * @var boolean
   */
  public $use_internal_exif = true;

  /**
   * @param boolean $use_internal_exif Use the PHP function {@link PHP_MANUAL#exif_read_data} if available.
   */
  public function __construct ($use_internal_exif = true)
  {
    $this->time_created = new DATE_TIME ();
    $this->time_created->clear ();
    $this->use_internal_exif = $use_internal_exif;
  }

  /**
   * @return boolean
   */
  public function exists ()
  {
    return isset ($this->php_type);
  }

  /**
   * Load image properties form the given file.
   * @param string $name Full path to the file to load. May be a URL or local file.
   * @param boolean $include_exif Load EXIF digital camera information from the file?
   */
  public function load_from_file ($name, $include_exif)
  {
    $this->php_type = null;

    if (is_file ($name))
    {
      $name = ensure_has_full_path ($name);
      $this->file_name = $name;
      $this->url = file_name_to_url ($name);
    }
    else
    {
      $this->url = $name;
      $name_as_file = url_to_file_name ($name);
      if ($name_as_file)
      {
        /* File is local. Only attempt loading if it exists (this avoids trying to
         * load through a loopback URL because the file isn't on the server).
         */

        if (@is_file ($name_as_file))
        {
          $name = $name_as_file;
          $this->file_name = $name_as_file;
        }
        else
        {
          $name = '';
          $this->file_name = null;
        }
      }
      else
      {
        $name = str_replace (' ', '%20', $name);
      }
    }

    if ($name)
    {
      if ($include_exif)
      {
        $this->_read_exif ($name);
        if (! $this->php_type)
        {
          $this->_read_image_info ($name);
        }
      }
      else
      {
        $this->_read_image_info ($name);
      }

      if (! $this->exists ())
      {
        $this->file_name = null;
      }
    }
  }

  /**
   * Read EXIF information from the file name.
   * This function is slow for some image files (using the {@link PHP_MANUAL#exif_read_data} function).
   * If the function does not exist, it uses a stripped-down EXIF parser to find just the date
   * the picture was taken and uses {@link _read_image_info()} to fill in the rest.
   * @param string $name Full path to the file to read.
   * @access private
   */
  protected function _read_exif ($name)
  {
    /** @var PROFILER $Profiler */
    global $Profiler;
    if (isset ($Profiler)) $Profiler->start ('EXIF');

    if ($this->_use_internal_exif ())
    {
      $this->exif = @exif_read_data ($name, 'IFD0');
      if ($this->exif === false)
      {
        $this->exif = null;
      }
      else
      {
        $php_errormsg = null;
        $this->exif = @exif_read_data ($name, 0, true);
        if (isset ($php_errormsg))
        {
          log_message ('[' . $name . ']: ' . $php_errormsg, Msg_type_debug_warning, Msg_channel_image);
        }

        $this->_initialize_properties_from_exif ();
      }
    }
    else
    {
      // Older versions used to have a non-native, PHP-based reader for EXIF data, but
      // it was byte-based and read a lot of the image data into memory, which doesn't work
      // so well with the larger pictures available today (ca. 2009) as opposed to the when
      // the code was written (ca. 2001). If you want to read EXIF information, you have to
      // have the PHP extension enabled, which is nearly a given these days, as opposed to
      // 8 years ago, when it was a rarity.

      log_message ('No fallback available for reading EXIF information from [' . $name . ']', Msg_type_debug_warning, Msg_channel_image);
    }

    if ($this->exists () && isset ($Profiler))
    {
      log_message ('Read EXIF for [' . $name . '] in [' . $Profiler->elapsed ('EXIF') . '] seconds.', Msg_type_debug_info, Msg_channel_image);
    }
  }

  /**
   * Read basic image information from the file name.
   * If the EXIF function fails or is not used, this function will very quickly and reliably obtain the image
   * type, width and height.
   * @param string $name Full path to the file to read.
   * @access private
   */
  protected function _read_image_info ($name)
  {
    $php_errormsg = null;
    $size = @getimagesize ($name);
    if (isset ($php_errormsg))
    {
    	log_message ('[' . $name . ']: ' . $php_errormsg, Msg_type_debug_warning, Msg_channel_image);
    }
    
    if (!is_array($size) || $size === FALSE)
    {
    	log_message ('[' . $name . ']: Could not get image size or file type (file is probably not an image).', Msg_type_warning, Msg_channel_image);
    }
    else
    {
      if (count($size) < 2 || $size[2] < 1)
      {
        log_message ('[' . $name . ']: Could not get image size or file type (file is an image, but type is unknown)', Msg_type_warning, Msg_channel_image);
      }

      if ($size && $size [2] > 0)
      {
        $this->php_type = $size [2];
        $this->mime_type = image_type_to_mime_type ($this->php_type);
        $this->width = $size [0];
        $this->height = $size [1];
      }
    }
  }

  /**
   * Read image information from the loaded EXIF record.
   * The set of properties exposed will usually be readable from an existing EXIF record. If the property
   * does not exist, it will still be set to empty in the object (so no PHP warnings are generated if read).
   * @access private
   */
  protected function _initialize_properties_from_exif ()
  {
    $this->mime_type = $this->_read_exif_value ('FILE', 'MimeType');
    $this->php_type = $this->_read_exif_value ('FILE', 'FileType');
    if (! $this->mime_type)
    {
      $this->mime_type = image_type_to_mime_type ($this->php_type);
    }

    $this->width = $this->_read_exif_value ('COMPUTED', 'Width');
    $this->height = $this->_read_exif_value ('COMPUTED', 'Height');
    $this->aperture = $this->_read_exif_value ('COMPUTED', 'ApertureFNumber');
    $this->iso_speed = $this->_read_exif_value ('EXIF', 'ISOSpeedRatings');
    if (! $this->iso_speed)
    {
      $this->iso_speed = $this->_read_exif_value ('EXIF', 'ShutterSpeedValue');
    }
    $this->used_flash = $this->_read_exif_value ('EXIF', 'Flash');
    $this->is_color = $this->_read_exif_value ('COMPUTED', 'IsColor');
    $this->camera_make = $this->_read_exif_value ('IFD0', 'Make');
    $this->camera_model = $this->_read_exif_value ('IFD0', 'Model');

    $this->time_created = $this->_time_from_exif ($this->_read_exif_value ('EXIF', 'DateTimeOriginal'));
  }

  /**
   * Read a single value from the EXIF data block.
   * @param string $section EXIF section to read.
   * @param string $name EXIF property to read.
   * @return string
   * @access private
   */
  protected function _read_exif_value ($section, $name)
  {
    return read_array_index (read_array_index ($this->exif, $section), $name);
  }

  /**
   * Read a time value from the EXIF data block.
   * Date/times are stored in a non-standard format and must be parsed to create a usable time object.
   * @param string $exif_time
   * @return DATE_TIME
   * @access private
   */
  protected function _time_from_exif ($exif_time)
  {
    $Result = new DATE_TIME ();
    $Result->clear ();

    if ($exif_time)
    {
      $exif_pieces = explode (' ', $exif_time);
      if (sizeof ($exif_pieces) == 2)
      {
        $d = str_replace (':', '-', $exif_pieces [0]);
        $t = trim ($exif_pieces [1]);
      }
      $Result->set_from_iso ($d . ' ' . $t);
     }

     return $Result;
  }

  /**
   * Should the PHP function {@link PHP_MANUAL#exif_read_data} be used?
   * @return boolean
   * @access private
   */
  protected function _use_internal_exif ()
  {
    return $this->use_internal_exif && is_callable('exif_read_data');
  }
}

/**
 * Manipulate the size of an image.
 * Use {@link resize()} or {@link resize_to_fit()} to apply a new size to an image. Use {@link as_html()} to
 * get the image in its current size (a link is added if the image has been resized).
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */
class IMAGE_METRICS
{
  /**
   * Fully-resolved URL for this picture.
   * @var string
   */
  public $url;

  /**
   * @var boolean
   */
  public $was_resized = false;

  /**
   * @var integer
   */
  public $original_width;

  /**
   * @var integer
   */
  public $original_height;

  /**
   * @var integer
   */
  public $constrained_width;

  /**
   * @var integer
   */
  public $constrained_height;

  /**
   * @param IMAGE $image
   */
  public function set_image ($image)
  {
    if ($image->exists ())
    {
      $this->_image = $image;
      $this->url = $image->properties->url;
      $this->original_width = $image->properties->width;
      $this->original_height = $image->properties->height;
    }
  }

  /**
   * @return boolean
   */
  public function loaded ()
  {
    return isset ($this->_image);
  }

  /**
   * Current width of the image.
   * If {@link resize()} or {@link resize_to_fit()} was called, the new width is returned.
   * @return integer
   */
  public function width ()
  {
    if ($this->was_resized)
    {
      return $this->constrained_width;
    }

    return $this->original_width;
  }

  /**
   * Current height of the image.
   * If {@link resize()} or {@link resize_to_fit()} was called, the new height is returned.
   * @return integer
   */
  public function height ()
  {
    if ($this->was_resized)
    {
      return $this->constrained_height;
    }

    return $this->original_height;
  }

  /**
   * Set the file name of the image to use.
   * @param string $url
   * @param boolean $load_image Loads original size from the image if
   * <code>True</code>. Turn off if you don't care about the initial size
   * and will simply set to fixed dimesions with {@link resize()}. Improves
   * speed in these cases (especially if the URL is external to the server).
   */
  public function set_url ($url, $load_image = true)
  {
    $this->url = $url;
    if ($load_image)
    {
      $img = new IMAGE ();
      $img->set_file ($url);
      $this->set_image ($img);
    }
    else
    {
      $this->original_width = 0;
      $this->original_height = 0;
    }
  }

  /**
   * Resize the image to the given dimensions.
   * Aspect ratio is not preserved.
   * @param integer $width
   * @param integer $height
   */
  public function resize ($width, $height)
  {
    $this->constrained_width = $width;
    $this->constrained_height = $height;
    $this->was_resized = ($this->original_width != $width) || ($this->original_height != $height);
  }

  /**
   * Make the image fit the given dimensions.
   * Aspect ratio is preserved and the image width and height will not exceed the given constraints.
   * @param integer $width
   * @param integer $height
   */
  public function resize_to_fit ($width, $height)
  {
    if ($this->loaded ())
    {
      $ow = $this->original_width;
      $oh = $this->original_height;
      $cw = $ow;
      $ch = $oh;

      if ($ow > $width)
      {
        if ($oh > $height)
        {
          $ch = round ($oh * ($width / $ow), 0);
          if ($ch > $height)
          {
            $cw = round ($ow * ($height / $oh), 0);
            $ch = $height;
          }
          else
          {
            $cw = $width;
          }
        }
        else
        {
          $ch = round ($oh * ($width / $ow), 0);
          $cw = $width;
        }
      }
      else
      {
        if ($oh > $height)
        {
          $cw = round ($ow * ($height / $oh), 0);
          $ch = $height;
        }
      }

      $this->resize ($cw, $ch);
    }
    else
    {
      $this->resize ($width, $height);
    }
  }

  /**
   * Return HTML code for displaying the image.
   * If the image has been resized, the image is wrapped in a link which will pop up a window with the
   * unconstrained image in it. The image tag itself is constrained to the desired size.
   * @param string $title Title to use for the image.
   * @param string $css_class CSS class to use for the image.
   * @see as_html_without_link()
   */
  public function as_html ($title = ' ', $css_class = '')
  {
    $Result = $this->as_html_without_link ($title, $css_class);
    if ($Result)
    {
      if ($this->was_resized)
      {
        $Result = "<a href=\"#\" onclick=\"open_image ('{$this->url}', {$this->original_width}, {$this->original_height}); return false;\">$Result</a>";
      }

      return $Result;
    }
    
    return '';
  }

  /**
   * Return HTML code for displaying the image.
   * If the image has been resized, the image tag itself is constrained to the desired size.
   * @param string $title Title to use for the image.
   * @param string $css_class CSS class to use for the image.
   * @see as_html()
   */
  public function as_html_without_link ($title = ' ', $css_class = '')
  {
    if (! isset ($this->_image) || $this->_image->loadable ())
    {
      $opts = global_text_options ();
      $title = $opts->convert_to_html_attribute ($title);
      $Result = '<img src="' . $this->url . '" alt="' . $title . '"';
      if ($css_class)
      {
        $Result .= ' class="' . $css_class . '"';
      }
      if ($this->was_resized)
      {
        $Result .= ' width="' . $this->constrained_width . '" height="' . $this->constrained_height . '"';
      }
      $Result .= '>';
      return $Result;
    }

    return '[<span title="' . $this->url . '">Image</span>] was not found.';
  }

  /**
   * @var IMAGE
   * @access private
   */
  protected $_image;
}

/**
 * Manipulate the size of an image.
 * Use {@link resize()} or {@link resize_to_fit()} to apply a new size to an image. Use {@link as_html()} to
 * get the image in its current size (a link is added if the image has been resized).
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.7.0
 */
class THUMBNAIL_CREATOR extends WEBCORE_OBJECT
{
  /**
   * Returns the error message, if one is set.
   * Set during the call to {@link create_thumbnail_for()}.
   * @var string
   */
  public $error_message = '';

  /**
   * Generate a thumbnail for the given file name and size.
   * Sets {@link $error_message} if anything goes wrong.
   * @see IMAGE::resize_to_fit()
   * @param string $file_name A server-local file name or URL.
   * @param integer $size Thumbnail will be no more than this many pixels on a side.
   */
  public function create_thumbnail_for ($file_name, $size)
  {
    $class_name = $this->context->final_class_name ('IMAGE', 'webcore/util/image.php');
    $img = new $class_name ();
    $img->set_file ($file_name);
    if ($img->loadable ())
    {
      $img->load_from_file ();
      if ($img->saveable ())
      {
        $img->resize_to_fit ($size, $size);
        $img->save_to_file ($this->_thumbnail_name_for ($img->properties->file_name));
      }
      else
      {
        $this->error_message = 'Cannot create thumbnails of type [' . $img->properties->mime_type . '].';
      }
    }
    else
    {
      $this->error_message = 'Could not create thumbnail from [' . $img->properties->file_name . '].';
    }
  }

  /**
   * Returns the thumbnail file name.
   * @param string $file_name
   * @return string
   * @access private
   */
  protected function _thumbnail_name_for ($file_name)
  {
    $url = new FILE_URL ($file_name);
    $url->append_to_name ('_tn');
    return $url->as_text ();
  }
}

?>