<?php
require_once ("functions.php");

$fn = trim($_REQUEST['fn']);

if($fn == "select_project"){
    $proj = trim($_REQUEST['project']);
    setcookie("project", $proj, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("path", $project[$proj]['path'], time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("theme", $project[$proj]['theme'], time() + (86400 * 30), "/"); // 86400 = 1 day
}

if($fn == "refresh_less"){
    $themePath = $_COOKIE['theme'];
    $projectPath = $_COOKIE['path'];
    ///Get Less Files
    getNewLessFiles($themePath);

    // Format and Save file
    $savePath = $projectPath."/dev/tests/static/testsuite/Magento/Test/Less/_files/changed_files.txt";
    formatTXT($savePath);

    echo "Please Run code check new LESS files loaded.";
}