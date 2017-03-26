<?php

$db_url = getenv("DATABASE_URL");
$db_url_parts = parse_url($db_url);
$db_url_parts["db"] = str_replace(array("/", "\\"), "", $db_url_parts["path"]);

$db_host     = $db_url_parts["host"] . ":" . $db_url_parts["port"];
$db_database = $db_url_parts["db"];
$db_username = $db_url_parts["user"];
$db_password = $db_url_parts["pass"];

?>
