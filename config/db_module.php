<?php
require_once "config.php";

function taoKetNoi(&$link)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (mysqli_connect_errno()) {
        echo "Lỗi kết nối đến máy chủ: " . mysqli_connect_error();
        exit();
    }
}

function chayTruyVanTraVeDL($link, $q)
{
    $result = mysqli_query($link, $q);
    return $result;
}

function chayTruyVanKhongTraVeDL($link, $q)
{
    $result = mysqli_query($link, $q);
    return $result;
}

function giaiPhongBoNho($link, $result)
{
    try {
        mysqli_close($link);
        mysqli_free_result($result);
    } catch (TypeError $e) {
    }
}
?>