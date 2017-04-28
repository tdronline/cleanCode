<?php
// Redirect to install if Configuration is not there.
if (!is_file("inc/conf.php")) {
    header("Location: install");
}


// Include Functions
include("inc/functions.php");

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

        .less-error {
            font-size: 12px;
            display: inline-block;
            padding: 5px 0;
            margin-right: 20px;
            text-shadow: 1px 1px 1px #ccc;
            min-width: 250px;
        }

        .less-correct {
            font-size: 12px;
            display: inline-block;
            color: #689f38;
            text-shadow: 1px 1px 1px #ccc;
            padding: 5px;
        }

        .error {
            margin-bottom: 6px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #bbb;
        }

        .project li {
            cursor: pointer;
            padding: 0 10px;
        }
        .panel-group .pan-title-con{
            padding: 10px 20px;
            display: block;
            width: 100%;
            font-weight: bold;
        }
        .panel-group .panel-heading {
            padding: 0;
        }
        .list-group-item-danger {
            border: 1px solid #900b0b;
        }
        .nerror {
            color: #8a6d3b;
            margin-left: 15px;
        }
        .navbar-brand {
            padding: 0 20px;
        }
        .navbar-default {
            background: #fff;
            border-color: #294a77;
        }
        .project-link {
            cursor: pointer;
            color: #000;
        }
        .project-info h2{
            margin-top: 0;
        }
        .project-info p{
            font-size: 12px;
            color: #888;
            margin-bottom: 0;
        }
        footer {
            border-top:1px solid #e0e0e0;
            text-align: center;
            padding: 10px 0;
            color: #9e9e9e;
            font-size: 11px;
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
                <a class="navbar-brand" href="#"><img src="img/mag-less-cci.png" alt="mage-less" /></a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" id="refresh_files">Update LESS</a></li>
                <li><a href="install/add-project.php">Add Project</a></li>
                <li class="dropdown">
                    <a id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="project-link">
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
    <?php
    if (isset($_COOKIE['project'])) {
        echo "<div class='well project-info'> <h2>LESS Report of {$_COOKIE['project']}</h2><p>Click to expand.</p></div>";

        //Read Report File
        $reportFile = $_COOKIE['path'] . "/dev/tests/static/report/less_report.txt";
        displayReport($reportFile);
    }
    ?>
    <footer>
        <p>Copyright TDr&copy; <?php echo date("Y"); ?></p>
    </footer>
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