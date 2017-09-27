<?php
set_time_limit(0);
ini_set('max_execution_time', 0);
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

if($fn == "code_sniffer"){
    $projectPath = $_COOKIE['path'];
    $cmd = "php -f $projectPath/bin/magento dev:tests:run static";
    if (exec("$cmd")) {
        echo "Succss";
    }
}

// ERROR Correcting
####################################

// Add New Line
if($fn == 'resolve-error'){
    $lineNo = trim($_REQUEST['line']);
    $themeFile = trim($_REQUEST['less']);
    $lessFileLine = getLessFile($themeFile);
    $error_line = trim($lessFileLine[$lineNo]);
    $fixedLine = errorFix($error_line);
    $lessFileLine[$lineNo] = $fixedLine;
    echo "$fixedLine -- $error_line";


    if($fixedLine != $error_line) {
        $lessFilePath = getfilePath($themeFile);
        //file_put_contents($lessFilePath, $lessFileLine);
    }
}

// Save File
if($fn == 'save_file') {
    $file = trim($_REQUEST['file']);
    if(is_file("../temp/temp.less")) {
        //file_put_contents("temp/temp_file.less", $fixFile);
        //echo $filePath;
        if(rename($file,$file."_bk")){
            echo "OK";
            copy ("../temp/temp.less",$file);
        }else {
             echo "Fail";
        }
    }
}