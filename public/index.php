<?php

require_once '../app/core/Session.php';
require_once '../config/db_module.php';
require_once '../app/routes/client/client.php';
require_once '../app/routes/admin/admin.php';
require_once '../app/models/mailer/Exception.php';
require_once '../app/models/mailer/PHPMailer.php';
require_once '../app/models/mailer/SMTP.php';
require_once '../app/core/functions.php';

// sendMail('dattruong.31241024873@st.ueh.edu.vn', 'Test', 'Test');

\App\Core\Session::start();

$link = null;
taoKetNoi($link);

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = trim(parse_url($requestUri, PHP_URL_PATH) ?? '/', '/');

if ($path === 'admin' || strpos($path, 'admin/') === 0) {
    adminRoute($requestUri);
} else {
    clientRoute($requestUri);
}

if ($link) {
    mysqli_close($link);
}


