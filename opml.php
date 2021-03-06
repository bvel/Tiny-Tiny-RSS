<?php
	set_include_path(get_include_path() . PATH_SEPARATOR .
		dirname(__FILE__) . "/include");

	function __autoload($class) {
		$file = "classes/".strtolower(basename($class)).".php";
		if (file_exists($file)) {
			require $file;
		}
	}

	require_once "functions.php";
	require_once "sessions.php";
	require_once "sanity_check.php";
	require_once "config.php";
	require_once "db.php";
	require_once "db-prefs.php";

	$link = db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if (!init_connection($link)) return;

	$op = $_REQUEST['op'];

	if ($op == "publish"){
		$key = db_escape_string($_REQUEST["key"]);

		$result = db_query($link, "SELECT owner_uid
				FROM ttrss_access_keys WHERE
				access_key = '$key' AND feed_id = 'OPML:Publish'");

		if (db_num_rows($result) == 1) {
			$owner_uid = db_fetch_result($result, 0, "owner_uid");

			$opml = new Opml($link, $_REQUEST);
			$opml->opml_export("", $owner_uid, true, false);

		} else {
			print "<error>User not found</error>";
		}
	}

	db_close($link);

?>
