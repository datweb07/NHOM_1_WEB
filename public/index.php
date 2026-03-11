<?php
require_once '../config/db_module.php';

$link = null;
taoKetNoi($link);

if ($link) {
    echo "Kết nối database thành công!";
    mysqli_close($link);
}
?>
