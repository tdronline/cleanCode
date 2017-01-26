<?php
include("conf.php");

// Get New Files from Project Theme folder

$di = new RecursiveDirectoryIterator(THEME,RecursiveDirectoryIterator::SKIP_DOTS);
$it = new RecursiveIteratorIterator($di);
file_put_contents(PROJECT . "/codepool/test-files.txt" ,'');
foreach($it as $lessfile) {
    if (pathinfo($lessfile, PATHINFO_EXTENSION) == "less") {
		file_put_contents(PROJECT . "/codepool/test-files.txt", $lessfile."\r\n", FILE_APPEND | LOCK_EX);
        // echo $file, PHP_EOL;
    }
}


//Format File
$file = file_get_contents(PROJECT . "/codepool/test-files.txt");
$file = str_replace(PROJECT . "/codepool/", '', $file);
$file = str_replace('"', '', $file);
$file = trim(str_replace('\\', '/', $file));

//Save Formatted File
file_put_contents(PROJECT."/codepool/dev/tests/static/testsuite/Magento/Test/Less/_files/changed_files.txt", $file);

@$errors = file_get_contents(PROJECT . DIRECTORY_SEPARATOR . "codepool" . DIRECTORY_SEPARATOR . "dev" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "report" . DIRECTORY_SEPARATOR . "less_report.txt");
$contents = explode("\r\n", $errors);

//TODO we need to see how we can run this command from the page it self currently it doesnt work
if (isset($_POST['runcmd'])) {
    $cmd = "php " . PROJECT . "/codepool/bin/magento dev:tests:run static";
    pclose(popen("start /B " . $cmd, "r"));
    if (exec($cmd)) {
        echo "Succss";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Less Report</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container" style="margin-top: 60px">
    <nav class="navbar navbar-default navbar-fixed-top" style="display:none;">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">LESSerrors</a>
            </div>
<!--            <ul class="nav navbar-nav">-->
<!--                <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>-->
<!--                <li><a href="--><?php //echo $errors; ?><!--">Error File</a></li>-->
<!--            </ul>-->
            <form method="post" class="pull-right">
                <button type="submit" class="btn btn-primary navbar-btn" name="runcmd">Update Errors</button>
            </form>
        </div>
    </nav>

    <ul class="list-group">
        <?php
		$fi=1;
        foreach ($contents as $content) {
            if (preg_match("/\bFILE:?\b/", $content)) {
                echo "<li class='list-group-item active'><span class='glyphicon glyphicon-file' aria-hidden='true'></span>[$fi] $content</li>";
                $path = explode('web', $content);
                $module =  explode('/', $path[0]);
                $module = array_slice($module, 0, -1, true);
                $module_name = end($module);
                if($module_name == 'mattblatt'){$module_name = '/';}else{$module_name = "/$module_name/";}
                $style_folder = THEME.$module_name. 'web' . $path[1];
				$fi = $fi+1;
                //print_r($module_name);
                //$style_folder = THEME.$style_folder;
            }

            if (!preg_match("/\bProperties sorted not alphabetically?\b/", $content)) {
                if (preg_match("/\bERROR?\b/", $content)) {
                    if (!preg_match("/\bFOUND?\b/", $content)) {
                        $error_line = '';
                        if (is_file($style_folder)) {
                            $cl = explode('|', $content);
                            $c_line = trim($cl[0]) - 1;
                            $lines = file($style_folder);//file in to an array
                            $error_line = trim($lines[$c_line]); //line 2
                            $error_line = "<pre>$error_line</pre>";
                        }

                        if (preg_match("/\bHexadecimal?\b/", $content) || preg_match("/\bUnits specified?\b/", $content) || preg_match("/\bquotes?\b/", $content) || preg_match("/\buppercase symbols?\b/", $content) || preg_match("/\buse hex?\b/", $content) || preg_match("/\bId selector?\b/", $content) || preg_match("/\bCSS colours?\b/", $content)) {
                            echo "<li class='list-group-item list-group-item-danger'>$content $error_line $style_folder</li>";
                        } else {
                            echo "<li class='list-group-item list-group-item-warning'>$content $error_line $style_folder</li>";
                        }

                    }
                }
            }
        }
        ?>
    </ul>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</body>
</html>