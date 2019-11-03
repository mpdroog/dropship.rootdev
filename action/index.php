<?php
namespace prj;
require __DIR__ . "/vendor/core/Unsafe.php";
require __DIR__ . "/vendor/core/Env.php";
\core\Unsafe::init();
\core\Env::init();

require __DIR__ . "/vendor/core/init-browser.php";
require __DIR__ . "/vendor/core/Taint.php";
require __DIR__ . "/vendor/core/Res.php";

trait ProjectValidators {}
\core\Taint::init();

$d = __DIR__ . "/cmp/" . str_replace("..", "", $_CLIENT["path"]) . "/index.php";
if (! file_exists($d)) {
  header('HTTP/1.1 404 Not Found');
  exit("Page not found.");
}
require $d;
