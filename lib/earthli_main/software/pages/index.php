<?php
	include_once ('news/init.php');
	$news_app = $Page->make_application (News_application_id);
	$folder_query = $news_app->login->folder_query ();
	$folder =& $folder_query->object_at_id (27);
	$entry_query = $folder->entry_query ();
	$entry_query->set_filter (Visible);
	$num_objects = $entry_query->size ();

	$Page->location->append ('Software');

	$Page->start_display ();
?>
<div class="box">
	<div class="box-body">
    <div style="float: right; width: 15em">
      <div class="side-bar">
       <div class="side-bar-body" style="text-align: center">
          <p>The <a href="webcore/">earthli WebCore</a> is available here.
            Click the box to learn more!</p>
          <p><a href="webcore/"><img src="webcore/media/images/webcore_box_green_small.png" alt="WebCore Box"></a></p>
          <p>Version <span class="field"><?php echo $Webcore_version; ?></span><br>
          	was	released on<br>
          	<span class="field"><?php echo $Webcore_release_date->format ($f); ?></span><br>
          	(<a href="<?php echo $Webcore_release_link; ?>">Release Notes</a>)</p>
          <p>
          <?php
            $renderer = $Page->make_controls_renderer ();
            echo $renderer->button_as_html ('Download', 'webcore/download.php', '{icons}buttons/download', '32px');
          ?>
          </p>
        </div>
      </div>
    </div>
    <div class="text-flow" style="margin-right: 16em">
    <p>earthli Software primarily develops web site software in PHP. You can grab
      a copy of that from the <a href="webcore/">WebCore</a> section.</p>
    <p>Some components of the WebCore are also available here as individual packages.
      Why do we do this?</p>
    <ul>
      <li>It shows off cool features of the WebCore in bite-size chunks.</li>
      <li>These components stand on their own and are hard to get right; we just want to
        help you avoid reinventing the wheel.</li>
      <li>It's the "crack" marketing strategy&mdash;once you get a taste, you'll be back for more.</li>
    </ul>
    <p>There are currently <span class="field"><?php echo $num_objects; ?></span> components available.</p>
  </div>
  <div class="box-body" style="margin-right: 16em">
    <?php
      include_once ('plugins/news/earthli_article_grid.php');
      $grid = new EARTHLI_SOFTWARE_ARTICLE_GRID ($news_app);
      $grid->set_ranges (5, 1);
        // 'num_objects' in a 10 x 1 grid - list of 10 articles, paginated
      $App->display_options->expand_links_to_root = TRUE;
      $grid->set_query ($entry_query);
      $grid->display ();
		?>
    </div>
  </div>
</div>
<?php $Page->finish_display (); ?>