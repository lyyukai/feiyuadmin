<?php
require __DIR__ . '/../vendor/autoload.php';

use app\controller\admin\Login;

$ctrl = new Login();
$result = $ctrl->login();
echo $result->getContent();
