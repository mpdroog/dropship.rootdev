<?php
require '/var/www/dropship/core/bol.php';
use core\Res;
use core\Taint;
function xml($res) {
    $xml = new SimpleXMLElement($res);
    return json_decode(json_encode($xml), true);
}

$key = Taint::getField("key", ["slug"]);
$xml = Taint::postField("data", []);
if ($key === false || $xml === false) {
    Res::error(400);
    echo "ERR: Missing key|xml field";
    exit;
}
if ($key !== "FLyyAcrd7qKmjdJMYwpBW1AJs") {
    Res::error(400);
    echo "ERR: Invalid key given.";
    exit;
}

/*$xml = '<?xml version="1.0" encoding="utf-8"?>
<order> <ordernumber>EG190611222023461</ordernumber> <own_ordernumber>2373120980</own_ordernumber> <tracktrace>3STCRH1465214</tracktrace> <shipper>TNT</shipper>
<status>shipped</status> </order>';*/
error_log("Shipping cb=$xml");

$req = xml($xml);
if (! is_array($req)) {
    Res::error(400);
    echo "ERR: Failed decoding input.";
    error_log("xml($xml) fail");
    exit;
}
/* array(5) {
  ["ordernumber"]=>
  string(14) "EG110414234502"
  ["own_ordernumber"]=>
  string(10) "9393920209"
  ["tracktrace"]=>
  string(15) "3SYNTZ009101575"
  ["shipper"]=>
  string(3) "TNT"
  ["status"]=>
  string(7) "shipped"
} */
if (strtoupper($req["shipper"]) !== "POSTNL") {
    user_error(sprintf("Shipper unsupported. Shipper=" . $req["shipper"]));
}
if (strtolower($req["status"]) === "backorder") {
    error_log("WARN: Order(%s) has status backorder", $req["own_ordernumber"]);
    exit;
}
if (strtolower($req["status"]) !== "shipped") {
    user_error(sprintf("Shipper-status unsupported. Status=" . $req["status"]));
}
list($order, $head) = bol_http("GET", sprintf("/orders/%s", $req["own_ordernumber"]));
foreach ($order["orderItems"] as $item) {
    list($res, $head) = bol_http("PUT", sprintf("/orders/%s/shipment", $item["orderItemId"]), [
        "shipmentReference" => date("Y-m-d H:i:s"),
        "transport" => [
            "transporterCode" => "TNT",
            "trackAndTrace" => $req["tracktrace"]
        ]
    ]);
    error_log(sprintf("shipment=%s", print_r($res, true)));
}

