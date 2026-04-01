<?php
foreach ($_SERVER as $k => $v) {
    if (strpos($k, "REQUEST") !== false || strpos($k, "PATH") !== false || strpos($k, "SCRIPT") !== false) {
        echo "$k: $v\n";
    }
}
