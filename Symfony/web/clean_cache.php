<?php
//just for see if there are errors
error_reporting(E_ALL);
ini_set('display_errors', '1');

//erases a directory recursively
function rrmdir($dir) {
	if (is_dir($dir))
	{
		$objects = scandir($dir);
		foreach ($objects as $object) {
		if ($object != "." && $object != "..")
		{
			echo("Deleting: $dir$object<br />\n");
			if (filetype($dir."/".$object) == "dir")
			{
				rrmdir($dir."/".$object);
				rmdir($dir."/".$object);
			}
			else
				unlink($dir."/".$object);
			}
		}
		reset($objects);
		// rmdir($dir);
	}
}
//erases the cache
rrmdir('../app/cache/');
?>
