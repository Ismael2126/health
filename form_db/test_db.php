<?php
require __DIR__ . '/config.php';
$r = $conn->query("SELECT NOW() AS nowtime");
echo "Connected OK. Server time: " . $r->fetch_assoc()['nowtime'];
