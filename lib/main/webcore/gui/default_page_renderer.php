<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage page
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

/***/
require_once ('webcore/gui/webcore_page_renderer.php');

/**
 * Uses the {@link PAGE_TEMPLATE_OPTIONS} to render an HTML page.
 * Use the options to configure which content is rendered. Replace the default
 * page renderer entirely to use a significantly different layout. Affects only
 * the page header and footer.
 * @package webcore
 * @subpackage page
 * @version 3.0.0
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
  var $browser_warning_in_header = TRUE;

  function _start_body ()
  {
    $page =& $this->page;
    $env =& $this->env;

    $options = $page->template_options;
?>
  <div class="page">
<?php
    if ($options->header_visible)
    {
?>
    <div class="banner">
      <div class="banner-box">
        <div class="banner-header">
        <?php
          if ($options->logo_file)
          {
            $root_url = $page->path_to (Folder_name_root);
            $logo_url = $page->resolve_icon_as_html ($options->logo_file, $options->logo_title);
        ?>
          <div class="banner-logo"><a href="<?php echo $root_url; ?>"><?php echo $logo_url; ?></a></div>
        <?php } ?>
          <div class="banner-content">
            <h1 class="banner-title"><?php echo $options->title; ?>
            <?php
              if ($options->icon)
                echo $page->resolve_icon_as_html ($options->icon, ' ', '50px', 'vertical-align: text-bottom');
            ?>
            </h1>
            <div class="login-status">
              <?php echo $this->_login_theme_status ($options); ?>
            </div>
          </div>
          <div style="clear: both"></div>
        </div>
        <?php
          $this->_handle_client_data_warnings ($options);
          $this->_handle_browser_warnings ($options, TRUE);
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
    </div>
<?php
    }
?>
    <div class="page-body">
<?php
  }

  function _finish_body ()
  {
    $page =& $this->page;
    $options = $page->template_options;

    if ($options->close_logger)
      $this->env->logs->close_all ();
?>
    </div>  <!-- end of page-body -->
    <?php
      if ($options->footer_visible)
      {
    ?>
    <div class="footer">
    <?php
      /* Build the left side of the footer with the copyright and version info. */

      $lines = array ();

      if ($options->copyright)
        $lines [] = $options->copyright;

      if ($options->copyright && $options->show_versions)
        $lines [] = '';

      if ($options->show_versions)
        $lines [] = $this->_versions_as_text ($options);

      echo '<div style="float: left">';
      echo join ('<br>', $lines);
      echo '</div>';

      /* Build the right side of the footer with support/contact/privacy links
       * and statistics.
       */

      $links = $this->_links_as_text ($options);
      $stats = $this->_page_statistics_as_text ($options);

      if ($links || $stats)
      {
        echo '<div style="text-align: right">';
        $lines = array ();
        $lines [] = $links;
        $lines [] = $stats;
        echo join ('<br>', $lines);
        echo '</div>';
      }

      echo '<div style="clear: both"></div>';

      $this->_handle_browser_warnings ($options, FALSE);
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
  function _login_theme_status (&$options)
  {
    /* Access the application through the page here since the parent
     * of the renderer is always a PAGE, not an APPLICATION.
     */

    $app_exists = isset ($this->page->app);
    $show_login = $options->show_login && $app_exists;
    if ($app_exists)
      $app =& $this->page->app;
    if ($show_login)
      $logged_in = $app_exists && ! $app->login->is_anonymous ();

    if ($options->show_links || $show_login)
    {
      /* Make a copy. */
      $menu = $this->context->make_menu ();

      if ($show_login)
      {
        if (! $logged_in)
        {
          $anon =& $app->anon_user ();
          $menu->append ('Not logged in');
          $menu->append ('Log in...', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->log_in));
          if ($anon->is_allowed (Privilege_set_user, Privilege_create))
            $menu->append ('Register...', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->user_create));
        }
        else
        {
          $menu->append ('Logged in as ' . $app->login->title_as_link ());
          $menu->append ('Log out', $app->resolve_file_for_alias (Folder_name_application, $app->page_names->log_out));
        }
      }

      if ($options->show_links)
      {
        if ($show_login && $this->page->app->login->is_allowed (Privilege_set_global, Privilege_configure))
        {
          $url = $app->resolve_file ('{app}' . $app->page_names->configure);
          $menu->append ('Configure', $url);
        }

        $res =& $this->page->resources ();
        if ($options->settings_url)
        {
          $url = $res->resolve_file ($options->settings_url);
          if ($this->page->theme->title)
            $menu->append ('<a href="' . $url . '">Theme</a>' . ': ' . $this->page->theme->title);
          else
            $menu->append ('Theme', $url);
        }

        if ($options->show_source_url)
        {
          $url = $res->resolve_file ($options->show_source_url);
          $menu->append ('View source', $url . '?page_name=' . urlencode ($this->env->url (Url_part_no_args)));
        }
      }

      return $menu->as_html ();
    }
  }

  /**
   * Return Contact/Support/Privacy links.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
	*/
  function _links_as_text (&$options)
  {
    if ($options->show_links)
    {
      /* Make a copy. */
      $menu = $this->context->make_menu ();
      $res =& $this->page->resources ();

      if ($options->contact_url)
        $menu->append ('Contact', $res->resolve_file ($options->contact_url));
      if ($options->support_url)
        $menu->append ('Support', $res->resolve_file ($options->support_url));
      if ($options->privacy_url)
        $menu->append ('Privacy', $res->resolve_file ($options->privacy_url));
      if ($options->rights_url)
        $menu->append ('Rights', $res->resolve_file ($options->rights_url));

      return $menu->as_html ();
    }
  }

  /**
   * Return the WebCore and application versions.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
	*/
  function _versions_as_text ($options)
  {
    $versions = array ();

    if ($options->app_info)
      $versions [] = $options->app_info;

    $versions [] = '<a href="http://earthli.com/software/webcore">' . $this->env->description () . '</a>';

    return join ($this->page->display_options->menu_separator, $versions);
  }

  /**
   * Return the WebCore and application versions.
   * @param PAGE_TEMPLATE_OPTIONS $options
   * @return string
   * @access private
	*/
  function _page_statistics_as_text ($options)
  {
    if ($options->show_statistics)
    {
      $profiler =& $this->env->profiler;

      if (isset ($profiler) && $profiler->exists ('global'))
      {
        $t = $profiler->elapsed ('global');
        $values [] = "{$t}s";
      }

      $query_data = "{$this->env->num_queries_executed} queries";
      if (isset ($profiler) && $profiler->exists ('db'))
      {
        $t = $profiler->elapsed ('db');
        $query_data .= " ({$t}s)";
      }
      $values [] = $query_data;

      $values [] = "{$this->env->num_webcore_objects} objects";

      if (isset ($profiler) && $profiler->exists ('text'))
      {
        $t = $profiler->elapsed ('text');
        $values [] = "{$t}s (text)";
      }

      if (isset ($profiler) && $profiler->exists ('ui'))
      {
        $t = $profiler->elapsed ('ui');
        $values [] = "{$t}s (ui)";
      }
    }

    if ($options->show_last_time_modified)
    {
      $date = new DATE_TIME (getlastmod ());
      $f = $date->formatter ();
      $f->set_type_and_clear_flags (Date_time_format_short_date_and_time);
      $values [] = $date->format ($f);
    }

    if (! empty ($values))
      return join ($this->page->display_options->menu_separator, $values);
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
  function _handle_browser_warnings (&$options, $in_header)
  {
    if (! ($in_header ^ $this->browser_warning_in_header)
        && $options->check_browser
        && ! $this->browser_supported ())
    {
      include_once ('webcore/util/options.php');
      $opt_ignore_warning = new STORED_OPTION ($this->page, "ignore_browser_warning");
      if (! $opt_ignore_warning->value ())
      {
        $res =& $this->page->resources ();
  ?>
    <div class="caution page-message notes">
      <?php echo $res->resolve_icon_as_html ('{icons}/indicators/warning', '', '32px', 'float: left'); ?>
      <div style="margin-left: 48px">
      <?php
        if ($options->browser_url)
        {
          $browser_url = $res->resolve_file ($options->browser_url);
      ?>
        <div>Your <a href="<?php echo $browser_url; ?>">browser</a> may have trouble rendering this page.</div>
      <?php
        }
        else
        {
      ?>
        <div>Your browser may have trouble rendering this page.</div>
      <?php
        }

        $url = $opt_ignore_warning->setter_url_as_html (! $opt_ignore_warning->value ());
      ?>
			<p>
			  To get the maximum amount of pleasure while browsing this and other websites you can download for free any one of these 
			  <a href="http://getfirefox.com">FireFox</a>, 
			  <a href="http://google.com/chrome">Chrome</a>, 
			  <a href="http://opera.com">Opera</a> or 
			  <a href="http://apple.com/safari">Safari</a>.
			  If you are using a work computer where you are not allowed to install software yourself you 
			  can use <a href="http://portableapps.com/">Portable Apps</a> to make it possible to run FireFox without even having to "install" it...      
			</p>      
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
  function _handle_client_data_warnings (&$options)
  {
    $page = $this->page;

    if (! $page->stored_theme_is_valid)
    {
      $res =& $this->page->resources ();
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