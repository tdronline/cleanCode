<?php
// Redirect to install if Configuration is not there.
if (!is_file("inc/conf.php")) {
    header("Location: install");
}


// Include Functions
include("inc/functions.php");

// TODO we need to see how we can run this command from the page it self currently it doesnt work
if (isset($_POST['runcmd'])) {
    $cmd = "php -f "  . $_COOKIE['path'] . "/bin/magento dev:tests:run static";
    echo $cmd;

    if (exec("$cmd")) {
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
    <link rel="stylesheet" href="css/default.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
                <li><span class="btn" id="run_sniffer">Run Sniffer</span></li>
            </ul>
        </div>
    </nav>
    <?php
    if (isset($_COOKIE['project'])) {
        echo "<div class='well project-info'> <h2>LESS Report of {$_COOKIE['project']}</h2><p>Click to expand.</p></div>";

        //Read Report File
        $reportFile = $_COOKIE['path'] . "/dev/tests/static/report/less_report.txt";
        displayReport($reportFile,5000);
    }
    ?>
    <!-- Modal -->
    <div class="modal fade fixErrorModal" id="fixErrors" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <img src="img/loader.gif" class="loader" />
            </div>
        </div>
    </div>
    <footer>
        <p>Copyright TDr&copy; <?php echo date("Y"); ?></p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <script type="text/javascript">
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

        // AJAX run CodeSniffer
        $("#run_sniffer").click(function () {
            $("#run_sniffer").html("RUNNING!!!");
            $(".modal-content").html("<img src='img/loader.gif' class='loader' />");
            $('#fixErrors').modal('show');
            $.post("inc/ajax.php", {fn: "code_sniffer"})
                .done(function (data) {
                    alert(data);
                    $(".modal-content").fadeOut(
                        function(){
                            $(".modal-content").html("<h2>Successfully Complete</h2><div class='btn btn-success' id='sniffer-ok'>Reload Page</div>").fadeIn();
                        });
                    $("#run_sniffer").html("Run Sniffer");
                    location.reload();
                });
        });

        // AJAX save Less File
        $(".modal-content").on('click','#save_less',function() {
            var lessfile = $("#lessfile").html();
            $.post("inc/ajax.php", {fn: "save_file", file: lessfile})
                .done(function (data) {
                    alert(data);
                });
        });

        // Fix errors
        $(".fix-btn").click(function(){
            var link = $(this).attr('href');
            $(".modal-content").html("<img src='img/loader.gif' class='loader' />");
            $(".modal-content").load(link);
        });

    </script>
</body>
</html>