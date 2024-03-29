<?php
/**
 * Initialize the basics.
 */

# Error handling
error_reporting(E_STRICT);
function report_ex($e) {
	report($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
}
function report_fatal() {
	$error = error_get_last();
	if( $error !== NULL) {
		report(
			E_CORE_ERROR, $error["message"],
			$error["file"], $error["line"]
		);
	}
}
set_error_handler("report");
set_exception_handler("report_ex");
register_shutdown_function("report_fatal");

# Encoding
mb_internal_encoding("UTF-8");

define("ROOT", realpath(dirname(__FILE__) . "/../../../"));
