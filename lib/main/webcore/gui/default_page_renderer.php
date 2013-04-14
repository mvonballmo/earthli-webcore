<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage page
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

/***/
require_once ('webcore/gui/webcore_page_renderer.php');

/**
 * Uses the {@link PAGE_TEMPLATE_OPTIONS} to render an HTML page.
 * Use the options to configure which content is rendered. Replace the default
 * page renderer entirely to use a significantly different layout. Affects only
 * the page header and footer.
 * @package webcore
 * @subpackage page
 * @version 3.4.0
 * @since 2.2.1
 */
class DEFAULT_PAGE_RENDERER extends WEBCORE_PAGE_RENDERER
{
  /**
   * Show the browser warning on top.
   * If {@link PAGE_HEADER_OPTIONS::$check_browser} is True, and {@link browser_is_supported()} returns
   * False, a warning is displayed in the header if this is True. If False, the warning
   * shows up in the page footer.
   * @var boolean
   */
  public $browser_warning_in_header = true;

  protected function _start_body ()
  {
    $page = $this->page;
    $env = $this->env;
    $browser = $env->browser();

    $options = $page->template_options;
?>
  <div class="page">
<?php
    if ($options->header_visible && !$browser->is(Browser_previewer))
    {
?>
    <div class="banner">
      <div class="banner-header">
      <?php
        if ($options->logo_file)
        {
          $root_url = $page->path_to (Folder_name_root);
          $logo_url = $page->resolve_file ($options->logo_file);
      ?>
        <div class="banner-logo" style="background-image: url(<?php echo $logo_url; ?>)"><a href="<?php echo $root_url; ?>"></a></div>
      <?php } ?>
        <div class="banner-content">
          <?php
          if ($options->icon)
          {
            ?>
            <div class="banner-icon" style="background-image: url(<?php echo $page->get_icon_url ($options->icon, '50px'); ?>)"></div>
          <?php
          }
          ?>
          <div class="banner-title">
            <?php echo $options->title; ?>
          </div>
          <div class="login-status">
            <?php echo $this->_login_theme_status ($options); ?>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <?php
        $this->_handle_client_data_warnings ($options);
        $this->_handle_browser_warnings ($options, true);

        if ($page->location->size ())
        {
          ?>
          <div class="nav-box">
            <?php $page->location->display (); ?>
          </div>
          <?php
        }
      ?>
    </div>
<?php
    }
?>
    <div class="page-body">
<?php
  }

  protected function _finish_body ()
  {
    $page = $this->page;
    $options = $page->template_options;
    $browser = $this->env->browser();
    
    if ($options->close_logger)
    {
      $this->env->logs->close_all ();
    }
?>
    </div>  <!-- end of page-body -->
    <?php
      if ($options->footer_visible && !$browser->is(Browser_previewer))
      {
    ?>
    <div class="footer">
    <?php
      /* Build the left side of the footer with the copyright and version info. */

      $lines = array ();

      if ($options->show_links)
      {
        $lines [] = '<div class="links">' . $this->_links_as_text ($options) . '</div>';
      }

      if ($options->copyright)
      {
        $lines [] = '<div class="copyright">' . $options->copyright . '</div>';
      }

      if ($options->show_versions)
      {
        $lines [] = '<div class="versions">' . $this->_versions_as_text ($options) . '</div>';
      }

      if ($options->show_last_time_modified)
      {
        $date = new DATE_TIME (getlastmod ());
        $f = $date->formatter ();
        $f->set_type_and_clear_flags (Date_time_format_short_date_and_time);

        $lines [] = '<div class="modification-time">Last modified on ' . $date->format ($f) . '</div>';
      }

      if ($options->show_statistics)
      {
        $lines [] = '<div class="statistics">' . $this->_page_statistics_as_text ($options) . '</div>';
      }

      echo '<div class="footer-data">';
      echo join ('</div><div class="footer-data">', $lines);
      echo '</div>';

      $this->_handle_browser_warnings ($options, false);
    ?>
    </div>
    <?php
      }
    ?>
  </div>  <!-- end of page -->
  <?php
  }

  /**
   * Return Login/Theme/etc. information.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
  */
  protected function _login_theme_status ($options)
  {
    /* Access the application through the page here since the parent
     * of the renderer is always a PAGE, not an APPLICATION.
     */

    /** @var $app APPLICATION */
    $app = null;
    $logged_in = false;
    $app_exists = isset ($this->page->app);
    $show_login = $options->show_login && $app_exists;
    if ($app_exists)
    {
      $app = $this->page->app;
    }
    if ($show_login)
    {
      $logged_in = $app_exists && ! $app->login->is_anonymous ();
    }

    if ($options->show_links || $show_login)
    {
      $menu = $this->context->make_menu ();

      if ($show_login)
      {
        if (! $logged_in)
        {
          $anon = $app->anon_user ();
          $menu->append ('Not logged in');
          $menu->append ('Log in...', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->log_in));
          if ($anon->is_allowed (Privilege_set_user, Privilege_create))
          {
            $menu->append ('Register...', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->user_create));
          }
        }
        else
        {
          $menu->append ('Logged in as ' . $app->login->title_as_link ());
          $menu->append ('Log out', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->log_out));
        }
      }

      return $menu->as_html ();
    }
    
    return '';
  }

  /**
   * Return Contact/Support/Privacy links.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
  */
  protected function _links_as_text ($options)
  {
    $menu = $this->context->make_menu ();
    $res = $this->page->resources ();

    if ($options->contact_url)
    {
      $menu->append ('Contact', $res->resolve_file ($options->contact_url));
    }
    if ($options->support_url)
    {
      $menu->append ('Support', $res->resolve_file ($options->support_url));
    }
    if ($options->privacy_url)
    {
      $menu->append ('Privacy', $res->resolve_file ($options->privacy_url));
    }
    if ($options->rights_url)
    {
      $menu->append ('Rights', $res->resolve_file ($options->rights_url));
    }

    if (isset($this->page->app))
    {
      /** @var $app APPLICATION */
      $app = $this->page->app;

      if ($app->login->is_allowed (Privilege_set_global, Privilege_configure))
      {
        $url = $app->resolve_file ('{app}' . $app->page_names->configure);
        $menu->append ('Configure', $url);
      }
    }

    $res = $this->page->resources ();
    if ($options->settings_url)
    {
      $url = $res->resolve_file ($options->settings_url);
      /** @var $page THEMED_PAGE */
      $page = $this->page;
      if ($page->theme->title)
      {
        $menu->append ('Theme: ' . $page->theme->title, $url);
      }
      else
      {
        $menu->append ('Theme', $url);
      }
    }

    if ($options->show_source_url)
    {
      $url = $res->resolve_file ($options->show_source_url);
      $menu->append ('View source', $url . '?page_name=' . urlencode ($this->env->url (Url_part_no_args)));
    }

    return $menu->as_html ();
  }

  /**
   * Return the WebCore and application versions.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
  */
  protected function _versions_as_text ($options)
  {
    $menu = $this->page->make_menu();

    if ($options->app_info)
    {
      $menu->append($options->app_info);
    }

    $menu->append($this->env->description (), "http://earthli.com/software/webcore");

    return 'Powered by ' . $menu->as_html();
  }

  /**
   * Return the WebCore and application versions.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
  */
  protected function _page_statistics_as_text ($options)
  {
    $profiler = $this->env->profiler;

    $menu = $this->page->make_menu();

    if (isset ($profiler) && $profiler->exists ('global'))
    {
      $t = $profiler->elapsed ('global');
      $menu->append("{$t}s");
    }

    $query_data = "{$this->env->num_queries_executed} queries";
    if (isset ($profiler) && $profiler->exists ('db'))
    {
      $t = $profiler->elapsed ('db');
      $query_data .= " ({$t}s)";
    }

    $menu->append($query_data);
    $menu->append("{$this->env->num_webcore_objects} objects");

    if (isset ($profiler) && $profiler->exists ('text'))
    {
      $t = $profiler->elapsed ('text');
      $menu->append("{$t}s (text)");
    }

    if (isset ($profiler) && $profiler->exists ('ui'))
    {
      $t = $profiler->elapsed ('ui');
      $menu->append("{$t}s (ui)");
    }

    return $menu->as_html();
  }

  /**
   * Issue browser warnings for non-supported browsers.
   * Uses the {@link $browser_warning_in_header} option to determine where to
   * show the warning. Only display is
   * {@link PAGE_TEMPLATE_OPTIONS::$check_browser} is true.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @param boolean $in_header True is rendering in the header.
   * @access private 
  */
  protected function _handle_browser_warnings ($options, $in_header)
  {
    if (! ($in_header ^ $this->browser_warning_in_header) && $options->check_browser && ! $this->browser_supported ())
    {
      include_once ('webcore/util/options.php');
      $opt_ignore_warning = new STORED_OPTION ($this->page, "ignore_browser_warning");
      if (! $opt_ignore_warning->value ())
      {
        $res = $this->page->resources ();
  ?>
    <div class="caution page-message notes">
      <?php echo $res->resolve_icon_as_html ('{icons}/indicators/warning', '', '32px', 'float: left'); ?>
      <div style="margin-left: 48px">
      <?php
        if ($options->browser_url)
        {
          $browser_url = $res->resolve_file ($options->browser_url);
      ?>
        <div>Your browser may have trouble rendering this page. See <a href="<?php echo $browser_url; ?>">supported browsers</a> for more information.</div>
      <?php
        }
        else
        {
      ?>
        <div>Your browser may have trouble rendering this page. To avoid any issues, please use
          <a href="http://opera.com">Opera</a>
          <a href="http://google.com/chrome">Chrome</a>,
          <a href="http://getfirefox.com">FireFox</a>,
          <a href="http://apple.com/safari">Safari</a> or any other modern, standards-compliant browser.</div>
      <?php
        }

        $url = $opt_ignore_warning->setter_url_as_html (! $opt_ignore_warning->value ());
      ?>
      <div style="margin-top: 1em">
        <input id="ignore_browser_warning" type="checkbox" value="<?php echo $opt_ignore_warning->value (); ?>" onclick="window.location='<?php echo $url; ?>'" style="vertical-align: middle">
        <label for="ignore_browser_warning">Do not show this message again.</label>
      </div>
    </div>
  </div>
  <?php
      }
    }
  }

  /**
   * Issue warnings if client data is no longer valid.
   * If client data storage format changes, the WebCore may need to reset
   * locally stored values. Issue those warnings here. Themes, for example,
   * may become invalid if they no longer exist on the server.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @access private
   */
  protected function _handle_client_data_warnings ($options)
  {
    /** @var $page THEMED_PAGE */
    $page = $this->page;

    if (! $page->stored_theme_is_valid)
    {
      $res = $this->page->resources ();
  ?>
  <div class="warning page-message notes">
    <?php echo $res->resolve_icon_as_html ('{icons}/indicators/warning', '', '32px', 'float: left'); ?>
    <div style="margin-left: 48px">
      Your theme settings are out-dated. Please go to
      <a href="<?php echo $res->resolve_file ($options->settings_url); ?>">theme settings</a>
      to update them.
    </div>
    <div style="clear: both"></div>
  </div>
  <?php
    }
  }
}

?>
