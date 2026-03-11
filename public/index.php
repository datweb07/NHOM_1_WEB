<?php

require_once '../app/core/Session.php';
require_once '../config/db_module.php';

\App\Core\Session::start();

$link = null;
taoKetNoi($link);

if ($link) {
    echo "Kết nối database thành công!";
    mysqli_close($link);
}
?>
