<?php
// https://dropship.rootdev.nl/action/acct/billing?key=oNqjD8f29DkrVwEA
use core\Taint;
use core\Res;
use core\Unsafe;

$key = Taint::getField("key", ["slug"]);
if ($key !== "oNqjD8f29DkrVwEA") {
    Res::error(400);
    echo "ERR: Invalid key given.";
    exit;
}

$input = Unsafe::post();
file_put_contents(__DIR__."/tmp.txt", json_encode($input));
