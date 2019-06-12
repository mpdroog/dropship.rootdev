<?php
require '/var/www/dropship/core/bol.php';

list($res, $head) = bol_http("GET", sprintf("/process-status/?entity-id=%s&event-type=CONFIRM_SHIPMENT", "2373671790"));
var_dump($res);

