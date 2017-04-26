<?php
// Redirect to install if Configuration is not there.
if (!is_file("inc/conf.php")) {
    header("Location: install");
}


// Include Functions
include("inc/functions.php");
//Read Report File
$reportFile = $_COOKIE['path'] . "/dev/tests/static/report/less_report.txt";
if (is_file($reportFile)) {
    $errors = file_get_contents($reportFile);
    $contents = explode("\r\n", $errors);
}

// TODO we need to see how we can run this command from the page it self currently it doesnt work
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
    <style type="text/css">
        * {
            border-radius: 0 !important;
        }

        .error-line {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .less-error {
            display: inline-block;
            padding: 5px 0;
            margin-right: 20px;
            min-width: 250px;
        }

        .less-correct {
            display: inline-block;
            color: #4CAF50;
            background-color: #fff;
            padding: 5px;
        }
        .project li{
            cursor: pointer;
            padding: 0 10px;
        }
    </style>
</head>
<body>
<div class="container" style="margin-top: 60px">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">MAGerrors</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" id="refresh_files">Update LESS</a></li>
                <li><a href="install/add-project.php">Add Project</a></li>
                <li class="dropdown">
                    <a id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Project
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu project" aria-labelledby="dLabel">
                        <?php foreach ($project as $pr => $p_name) {
                            echo "<li title='$pr'>$pr</li>";
                        } ?>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <?php if (isset($_COOKIE['project'])) {
        echo "<h2>LESS Report of {$_COOKIE['project']}</h2>";
    } ?>
    <ul class="list-group">
        <?php
        if (is_file($reportFile)) {
            $fi = 1;
            foreach ($contents as $content) {
                if (preg_match("/\bFILE:?\b/", $content)) {
                    $fi = $fi + 1;
                    echo "<li class='list-group-item active' data-toggle='itm_$fi'><span class='glyphicon glyphicon-file' aria-hidden='true'></span> [$fi] $content <span class='glyphicon glyphicon-chevron-up pull-right' aria-hidden='true'></span></li>";
                    $path = explode('web', $content);
                    $module = explode('/', $path[0]);
                    $module = array_slice($module, 0, -1, true);
                    $module_name = end($module);
                    $themefolder = explode('/',$_COOKIE['theme']);
                    $folder_name = end($themefolder);
                    if ($module_name == $folder_name) {
                        $module_name = '/';
                    } else {
                        $module_name = "/$module_name/";
                    }
                    $style_folder = $_COOKIE['theme'] . $module_name . 'web' . $path[1];
                }

                if (!preg_match("/\bProperties sorted not alphabetically?\b/", $content)) {
                    if (preg_match("/\b \| ERROR \| ?\b/", $content)) {

                        //Error Correction
                        $errorFix = errorSuggession($style_folder, $content);
                        $errorReported = "<div class='error-line'>$content</div>";

                        if (preg_match("/\bHexadecimal?\b/", $content) || preg_match("/\bUnits specified?\b/", $content) || preg_match("/\bquotes?\b/", $content) || preg_match("/\buppercase symbols?\b/", $content) || preg_match("/\buse hex?\b/", $content) || preg_match("/\bId selector?\b/", $content) || preg_match("/\bCSS colours?\b/", $content)) {
                            echo "<li class='list-group-item list-group-item-danger itm_$fi'>$errorReported $errorFix</li>";
                        } else {
                            echo "<li class='list-group-item list-group-item-warning itm_$fi'>$errorReported $errorFix</li>";
                        }
                    }
                }
            }
        } else {
            echo "<div class='alert alert-danger' role='alert'>Report Not Generated.</div>";
        }
        ?>
    </ul>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <script type="text/javascript">
        // Fold content
        $('.active').click(function () {
            cls = $(this).attr("data-toggle");
            $("." + cls).slideToggle();
        });
        // Drop down
        $('.dropdown-toggle').dropdown();

        // AJAX calls
        $(".project li").click(function () {
            var project = $(this).attr('title');
            $.post("inc/ajax.php", {fn: "select_project", project: project})
                .done(function (data) {
                    //alert( "Data Loaded: " + data );
                    location.reload();
                });
        });

        // AJAX refresh Files
        $("#refresh_files").click(function () {
            $.post("inc/ajax.php", {fn: "refresh_less"})
                .done(function (data) {
                    alert(data)
                });
        });

    </script>
</body>
</html>