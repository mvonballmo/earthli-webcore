<?php
	require_once ('webcore/init.php');

  define ('Show_latest_pictures', TRUE);
  define ('Num_picture_columns', 4);
  define ('Num_picture_rows', 1);
  $Themes_to_show = array (1, 2, 3, 4, 5, 6, 7);

	$Page->start_display ();

  $browser = $Env->browser ();
  if ($browser->supports (Browser_CSS_2))
    $width_style = 'min-width: 35em';
  else
    $width_style = 'width: 700px';
?>
<div style="<?php echo $width_style; ?>">
  <div style="float: left; width: 12em">
    <div class="side-bar">
      <div class="side-bar-title">Headlines</div>
      <div class="side-bar-body">
        <?php
        include_once ('news/init.php');
        $news_app =& make_news_application ();
        $entry_query =& $news_app->login->all_entry_query ();
        $entry_query->set_filter (Visible);

        include_once ('news/gui/tiny_article_grid.php');
        $grid =& new TINY_ARTICLE_GRID ($news_app);
        $grid->set_ranges (5, 1);
        $grid->show_paginator = FALSE;
        $grid->display ($entry_query);
      ?>
      </div>
    </div>
    <br>
    <div class="side-bar">
      <div class="side-bar-title">Theme</div>
      <div class="side-bar-body">
        <div class="notes" style="margin-bottom: 1em">Choose the theme that best suits you.</div>
        <?php
          $theme_query =& $Page->theme_query ();
          $theme_query->restrict_by_op ('id', $Themes_to_show, Operator_in);
          include_once ('webcore/gui/theme_selector_grid.php');
          $grid =& new THEME_SELECTOR_GRID ($Page);
          $grid->set_ranges (sizeof ($Themes_to_show), 1);
          $grid->show_paginator = FALSE;
          $grid->display ($theme_query);
        ?>
        <p class="detail" style="text-align: right">[<a href="settings.php">More</a>]</p>
      </div>
    </div>
    <br>
    <div class="detail" style="text-align: center">
      <p>Proud to be a standards-compliant web site.</p>
      <?php $path_to_powered_by = $Page->expand_resource_from_string ('{root}'); ?>
      <p>
        <a href="http://validator.w3.org/check/referer"><img src="<?php echo $path_to_powered_by; ?>valid-html401.png" alt="Valid HTML 4.01! (Strict)"></a>
      </p>
      <p>Find out more about <a href="browser.php">your browser</a>.</p>
    </div>
  </div>
  <div class="box" style="margin-left: 13em">
    <div class="box-body">
      <p>Welcome to the earthli WebCore Suite!</p>
      <p>There are a lot of <a href="albums/">pictures</a>, <a href="news/">articles</a>
        and <a href="recipes/">recipes</a>; use the icons below to browse around.</p>
      <div style="margin: auto; display: table">
        <div style="float: left; margin: 1em">
          <a href="albums/"><?php echo $Page->icon_as_html ('general/albums', 'earthli Albums', '50px'); ?></a>
        </div>
        <div style="float: left; margin: 1em">
          <a href="news/"><?php echo $Page->icon_as_html ('general/news', 'earthli News', '50px'); ?></a>
        </div>
        <div style="float: left; margin: 1em">
          <a href="recipes/"><?php echo $Page->icon_as_html ('general/recipes', 'earthli Recipes', '50px'); ?></a>
          </div>
        <div style="float: left; margin: 1em">
          <a href="projects/"><?php echo $Page->icon_as_html ('general/projects', 'earthli Projects', '50px'); ?></a>
        </div>
      </div>
      <?php
        if (Show_latest_pictures)
        {
      ?>
      <p>The <a href="albums/">Albums</a> are pretty popular; with <a href="news/">News</a> a
        close second. Check out the
        <a href="albums/?panel=pictures&amp;time_frame=recent">latest pictures</a> below.</p>
      <?php
          include_once ('albums/init.php');
          $albums_app =& make_album_application ($Page);

          $entry_query =& $albums_app->login->all_entry_query ();
          $entry_query->set_type ('picture');
          $entry_query->set_filter (Visible);
          $entry_query->set_order ('time_created DESC');

          include_once ('albums/gui/tiny_picture_grid.php');
          $grid =& new TINY_PICTURE_GRID ($albums_app);
          $grid->set_ranges (Num_picture_rows, Num_picture_columns);
          $grid->show_paginator = FALSE;
          $grid->max_width = 75;
          $grid->max_height = 50;
          $grid->display ($entry_query);
        }
      ?>
    </div>
    <div style="clear: both">
      <!-- Main content must be longer for IE, else floats aren't fully rendered -->
    </div>
  </div>
</div>
<?php $Page->finish_display (); ?>