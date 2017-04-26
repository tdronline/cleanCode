<?php
if(!is_file("../inc/conf.php")){header("Location: .");}
if (isset($_POST['install'])) {
    $projectName = trim($_POST['project-name']);
    $projectPath = trim($_POST['project-path']);
    $themePath = trim($_POST['theme-path']);

    // Create Config File
    $projectPath = str_replace('\\', '/', $projectPath);
    $themePath = str_replace('\\', '/', $themePath);
    file_put_contents("../inc/conf.php", "
    \$project[\"$projectName\"][\"path\"] = \"$projectPath\";
    \$project[\"$projectName\"][\"theme\"] = \"$themePath\";",FILE_APPEND | LOCK_EX);

    //Request Functions File
    require_once ("../inc/functions.php");

    //Get Less Files
    getNewLessFiles($themePath);

    // Format and Save file
    $savePath = $projectPath."/dev/tests/static/testsuite/Magento/Test/Less/_files/changed_files.txt";
    formatTXT($savePath);

    //Rename and Copy XML to config folder
    @rename("$projectPath/dev/tests/static/phpunit.xml.dist", "$projectPath/dev/tests/static/phpunit.xml.dist-original");
    copy("../inc/phpunit.xml.dist", "$projectPath/dev/tests/static/phpunit.xml.dist");
    file_put_contents("../$projectName-run.bat", "php $projectPath/bin/magento dev:tests:run static");

    // Clear Blacklist files
    @file_put_contents("$projectPath/dev/tests/static/testsuite/Magento/Test/Less/_files/blacklist/old.txt", "");
    @file_put_contents("$projectPath/dev/tests/static/testsuite/Magento/Test/Less/_files/blacklist/ee.txt", "");

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Project</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <h1>Add New Project</h1>
    <p>This will add a new project to your configuration file.</p>
    <p>Enter below information and proceed.</p>
    <?php if (!isset($_POST['install'])) { ?>
        <div class="well well-lg">
            <form method="post">
                <div class="form-group">
                    <label>Project name</label>
                    <input type="text" class="form-control" name="project-name" placeholder="Matt Blatt">
                </div>
                <div class="form-group">
                    <label>Project path</label>
                    <input type="text" class="form-control" name="project-path" placeholder="D:\magento\matt-blatt\codepool">
                </div>
                <div class="form-group">
                    <label>Project Theme path</label>
                    <input type="text" class="form-control" name="theme-path"
                           placeholder="D:\magento\matt-blatt\codepool\app\design\frontend\Netstarter\mattblatt">
                </div>
                <button type="submit" name="install" class="btn btn-default">Create</button>
            </form>
        </div>
    <?php } else { ?>
        <p>Project added successfully Selected as current project.</p>
        <a href="../"  class="btn btn-success btn-xs">View Report</a>
    <?php
        setcookie("project", $projectName, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie("path", $projectPath, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie("theme", $themePath, time() + (86400 * 30), "/"); // 86400 = 1 day
    } ?>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

</body>
</html>