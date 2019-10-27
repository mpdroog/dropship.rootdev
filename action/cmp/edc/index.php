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

$req = xml($xml);
if (! is_array($req)) {
    Res::error(400);
    echo "ERR: Failed decoding input.";
    error_log("xml($xml) fail");
    exit;
}
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
    echo sprintf("EDC-order queued to Bol.com with id=%s (url=%s)\n", $res["id"], $res["links"][0]["href"]);
}

