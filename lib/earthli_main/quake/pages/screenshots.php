<?php
	$Page->title->subject = 'Screenshots';

	$Page->location->append ("Quake III Arena", "./");
	$Page->location->append ("Screenshots");

	$Page->start_display ();
?>
<div class="box">
	<div class="box-title">
    Quake III Arena Screenshots
	</div>
	<div class="box-body">
		<?php
			$Page->database_options->name = 'earthli';

			include_once ('albums/init.php');
			$albums_app =& $Page->make_application (Album_application_id);

			$folder_query = $albums_app->login->folder_query ();
			$folder =& $folder_query->object_at_id (128);
			if ($folder)
			{
				$pic_query = $folder->entry_query ();
				$pic_query->set_type ('picture');

        $class_name = $albums_app->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
        $grid = new $class_name ($albums_app);
				$grid->set_ranges (4, 2);
				$grid->set_query ($pic_query);
				$grid->display ();
			}
			else
				echo "<div class=\"error\">The Quake screenshots album is unavailable.</div>";
		?>
	</div>
</div>
<?php $Page->finish_display (); ?>