<?php require_once('conf.php'); ?>

<?php
function formatTXT($savePath)
{
    //Format File
    if (isset($_COOKIE['path'])) {
        $current_project = $_COOKIE['path'];
    } else {
        global $projectPath;
        $current_project = $projectPath;
    }

    $file = file_get_contents("../temp-files.txt");
    $file = str_replace($current_project . '/', '', $file);
    $file = str_replace('"', '', $file);
    $file = trim(str_replace('\\', '/', $file));

    //Save Formatted File
    file_put_contents($savePath, $file);
    unlink("../temp-files.txt");
}

function getNewLessFiles()
{
    if (isset($_COOKIE['theme'])) {
        $current_theme = $_COOKIE['theme'];
    } else {
        global $themePath;
        $current_theme = $themePath;
    }

    // Get New Files from Project Theme folder
    $di = new RecursiveDirectoryIterator($current_theme, RecursiveDirectoryIterator::SKIP_DOTS);
    $it = new RecursiveIteratorIterator($di);
    //file_put_contents("../temp-files.txt" ,'');
    foreach ($it as $lessfile) {
        if (pathinfo($lessfile, PATHINFO_EXTENSION) == "less") {
            file_put_contents("../temp-files.txt", $lessfile . "\r\n", FILE_APPEND | LOCK_EX);
            // echo $file, PHP_EOL;
        }
    }
}

function errorSuggestion($themeFile, $content)
{
    $lessFile = getLessFile($themeFile);
    $cl = explode('|', $content);
    $c_line = trim($cl[0]) - 1;
    $error_line = trim($lessFile[$c_line]); //line 2
    $error_fix = '';
    $less_error_line = "<div class='less-error'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> $error_line</div>";


    if (preg_match("/\bHexadecimal?\b/", $content) || preg_match("/\buse hex?\b/", $content) || preg_match("/\bCSS colours?\b/", $content)) {
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> Use Color Variable with <strong>.lib-css();</strong></div>";
    }
    if (preg_match("/\bUnits specified?\b/", $content)) {
        $fix = str_replace('0px', '0', $error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
    if (preg_match("/\bquotes?\b/", $content)) {
        $fix = str_replace('"', '\'', $error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
    if (preg_match("/\buppercase symbols?\b/", $content)) {
        $fix = strtolower($error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
    if (preg_match("/\bId selector?\b/", $content)) {
        $fix = str_replace('#', '.', $error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
    if (preg_match("/\bnewline character?\b/", $content)) {
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> Keep a blank line below code</div>";
    }

    return $less_error_line . $error_fix;
}

function displayReport($reportFile)
{
    if (is_file($reportFile)) {
        $errors = file_get_contents($reportFile);
        $contents = explode("\r\n", $errors);

        // Iterate through each Line
        foreach ($contents as $content) {

            // Panel Title Content
            if (preg_match("/\bFILE:?\b/", $content)) {
                $themeNameArray = explode('/', $_COOKIE['theme']);
                $themeName = end($themeNameArray);
                $getfileName = explode($themeName, $content);
                $fileName = $themeName . end($getfileName);
            }

            // Panel Body Content
            if (!preg_match("/\bProperties sorted not alphabetically?\b/", $content)) {
                if (preg_match("/\b \| ERROR \| ?\b/", $content)) {
                    if (preg_match("/\bHexadecimal?\b/", $content) ||
                        preg_match("/\bUnits specified?\b/", $content) ||
                        preg_match("/\bquotes?\b/", $content) ||
                        preg_match("/\buppercase symbols?\b/", $content) ||
                        preg_match("/\buse hex?\b/", $content) ||
                        preg_match("/\bId selector?\b/", $content) ||
                        preg_match("/\bCSS colours?\b/", $content)
                    ) {
                        $lessErrors[$fileName]['critical'][] = $content;

                    } else {
                        $lessErrors[$fileName]['normal'][] = $content;
                    }
                }

            }
        }

        // Display Results
        echo "<div class='panel-group' id='accordion' role='tablist' aria-multiselectable='true'>";
        $i = 1;

        // For Each File Display Errors
        foreach ($lessErrors as $lessFile => $cErr) {
            $criticalCount = @sizeof($cErr['critical']);
            $normalCount = @sizeof($cErr['normal']);

            if ($criticalCount > 0) {
                $panClass = 'panel-danger';
                $cCount = "<span class='pull-right'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> $criticalCount</span>";
            } else {
                $panClass = '';
                $cCount = "";
            }

            if ($normalCount > 0) {
                $nCount = "<span class='pull-right nerror'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> $normalCount</span>";
            } else {
                $nCount = "";
            }

            echo "<div class='panel panel-default $panClass'>
            <div class='panel-heading' role='tab' id='heading_$i'>
                <h4 class='panel-title'>
                    <div class='pan-title-con' role='button' data-toggle='collapse' data-parent='#accordion' href='#collaps_$i' aria-expanded='true' aria-controls='collaps_$i'>
                       <span class='glyphicon glyphicon-file' aria-hidden='true'></span>
                       $lessFile  $nCount   $cCount                   
                    </div>
                </h4>
            </div>
            <div id='collaps_$i' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading_$i'>
                <div class='panel-body'>";

            //Display Critical Errors
            if ($criticalCount > 0) {
                echo "<ul class='list-group'>";
                foreach ($cErr['critical'] as $cError) {
                    $suggestion = errorSuggestion($lessFile, $cError);
                    echo "<li class='list-group-item list-group-item-danger' ><div class='error'> $cError </div> $suggestion</li>";
                }
                echo "</ul>";
            }

            // Display Normal Errors
            if ($normalCount > 0) {
                $expand = 'in';
                if ($criticalCount > 0) {
                    $expand = '';
                }
                echo "<div class='list-group-item list-group-item-warning' data-toggle='collapse' data-target='#sub_$i' aria-expanded='false' aria-controls='sub_$i'> <strong>Other Errors</strong> </div>";
                echo "<ul class='list-group collapse $expand' id='sub_$i'>";
                foreach ($cErr['normal'] as $nError) {
                    $suggestion = errorSuggestion($lessFile, $nError);
                    echo "<li class='list-group-item list-group-item-warning' > <div class='error'>$nError</div> $suggestion</li>";
                }
                echo "</ul>";
            }
            echo "</div>
            </div>
        </div>";
            $i = $i + 1;
        }

        echo "</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Report Not Generated.</div>";
    }
}

function getLessFile($themeFile)
{
    $themeFolder = explode('/', $_COOKIE['theme']);
    $themeName = end($themeFolder);
    $themeFile = str_replace($themeName, '', $themeFile);
    $lessFilePath = $_COOKIE['theme'] . $themeFile;
    $lessFile = file($lessFilePath);
    return $lessFile;
}

?>

