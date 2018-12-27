<?php
$query_str = "";
if (! empty($_SERVER['QUERY_STRING'])) {
	$query_str = "?" . $_SERVER['QUERY_STRING'];
}
header("Location: app/" . $query_str);
exit();
