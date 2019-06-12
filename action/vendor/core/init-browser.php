<?php
header('Content-Type: text/html; charset=UTF-8');
require dirname(__FILE__) . "/init.php";

function report($errno, $errstr, $errfile, $errline) {
  header('HTTP/1.1 500 Internal Server Error');
  // TODO: Report error to devsys

  $msg = "($errfile:$errline) $errno: $errstr";
  error_log($msg);
  exit("Error written to error log.\n");
}

# Paranoia (try to expose as less as possible)
$uniq = "";
foreach (["HTTP_ACCEPT_LANGUAGE", "HTTP_USER_AGENT", "HTTP_ACCEPT"] as $key) {
	if (isset($_SERVER[$key])) {
		$uniq .= $_SERVER[$key];
	}
}

$path = str_replace('/action/', '', $_SERVER["DOCUMENT_URI"]);
$_CLIENT = [
	"path" => $path,
	"today" => date("Y-m-d"),
	"ip" => $_SERVER["REMOTE_ADDR"],
	"uniq" => sha1($uniq),
	"encoding" => isset($_SERVER["HTTP_ACCEPT"]) && $_SERVER["HTTP_ACCEPT"] === "application/json" ? "json" : "html",
        "http_method" => $_SERVER['REQUEST_METHOD'],
	"protocol" => $_SERVER["SERVER_PROTOCOL"]
];
# Remove SERVER to force clean code
unset($_SERVER);
