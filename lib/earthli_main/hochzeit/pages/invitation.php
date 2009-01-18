<h3>Wedding Documents</h3>
<p>All pieces for the wedding were created in Photoshop 6.01 and all pieces
	are reproduced here as fairly high-quality JPG files.</p>
<p class="notes">The images and descriptions are stored in an <a href="/albums/">earthli Albums</a>
	photo album. The picture browser that comes up if you click an image may or may not appear
	in the same colors as you see now. Don't panic.</p>
<?php
	require_once ('albums/init.php');
  $Page->database_options->name = 'earthli';
	$albums_app = $Page->make_application (Album_application_id);
	$folder_query = $albums_app->login->folder_query ();
	$folder =& $folder_query->object_at_id (115);

	if ($folder)
	{
		$pic_query = $folder->entry_query ();
		$pic_query->set_type ('picture');

    $class_name = $albums_app->final_class_name ('PICTURE_GRID', 'albums/gui/picture_grid.php');
    $grid = new $class_name ($albums_app);
    $grid->set_query ($pic_query);
		$grid->set_ranges (3, 2);
		$grid->display ();
	}
	else
		echo "<span class=\"error\">The invitation album is unavailable.</span>";
?>