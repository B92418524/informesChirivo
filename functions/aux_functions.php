<?php
function cG($param) {
	if (isset($_GET[$param])) {
		return $_GET[$param];
	} else {
		return '';
	}
}

function cP($param) {
	if (isset($_POST[$param])) {
		return $_POST[$param];
	} else {
		return '';
	}
}
?>