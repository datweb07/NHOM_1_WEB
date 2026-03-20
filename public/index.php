<?php

require_once '../app/core/Session.php';
require_once '../config/db_module.php';
require_once '../app/routes/client/client.php';

\App\Core\Session::start();

$link = null;
taoKetNoi($link);

clientRoute($_SERVER['REQUEST_URI'] ?? '/');

if ($link) {
    mysqli_close($link);
}
?>
