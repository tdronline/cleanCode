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
    $action = '';
    $less_error_line = "<div class='less-error'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> $error_line</div>";


    if (preg_match("/\bHexadecimal?\b/", $content) || preg_match("/\buse hex?\b/",
            $content) || preg_match("/\bCSS colours?\b/", $content)
    ) {
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
        $action = 'new-line';
    }

    return $less_error_line . $error_fix;
}

function displayReport($reportFile, $lines = 0)
{
    if (is_file($reportFile)) {
        $errors = file_get_contents($reportFile);
        $contents = explode("\r\n", $errors);
        if($lines > 0) {
            $contents = array_slice($contents, 0, $lines);
        }


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
                $cCount = "<span class='pull-right cerror'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> $criticalCount</span>";
            } else {
                $panClass = '';
                $cCount = "";
            }

            if ($normalCount > 0) {
                $nCount = "<span class='pull-right nerror'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> $normalCount</span>";
            } else {
                $nCount = "";
            }

            $lessLink = base64_encode($lessFile);

            $fixLink = "<a href='fixes.php?file=$lessLink' class='fix-btn btn btn-xs btn-warning' data-toggle='modal' data-target='.fixErrorModal'>Fix Errors</a>";

            echo "<div class='panel panel-default $panClass'>
            <div class='panel-heading' role='tab' id='heading_$i'>
                <h4 class='panel-title'>
                    <div class='pan-title-con'>
                        <span class='glyphicon collapsed expand' role='button' data-toggle='collapse' data-parent='#accordion' href='#collaps_$i' aria-expanded='true' aria-controls='collaps_$i'></span>
                        $fixLink
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
                echo "<div class='list-group-item list-group-item-warning other-errors' data-toggle='collapse' data-target='#sub_$i' aria-expanded='false' aria-controls='sub_$i'> <strong>Other Errors</strong> </div>";
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
    $lessFilePath = getfilePath($themeFile);
    $lessFile = file($lessFilePath);
    return $lessFile;
}

function getfilePath($themeFile)
{
    $themeFolder = explode('/', $_COOKIE['theme']);
    $themeName = end($themeFolder);
    $themeFile = str_replace($themeName, '', $themeFile);
    $lessFilePath = $_COOKIE['theme'] . $themeFile;
    if(!is_file($lessFilePath)){
        // if the file cannot be identified from theme file use the Magento_ or Netstarter_ folders to create the path. Add the vendors as necessary.
        if (substr_count($themeFile, 'Magento_') > 0) {
            $pathMagento = explode('Magento_', $themeFile);
            if (!empty($pathMagento)) {
                $lessFilePath = $_COOKIE['theme'] . '/Magento_' . $pathMagento[1];
            }
        }
        if (substr_count($themeFile, 'Netstarter_') > 0) {
            $pathNS = explode('Netstarter_', $themeFile);
            if (!empty($pathNS)) {
                $lessFilePath = $_COOKIE['theme'] . '/Netstarter_' . $pathNS[1];
            }
        }
    }
    return $lessFilePath;
}

function getLessErrorLine($themeFile, $line)
{
    $lessFileLine = getLessFile($themeFile);
    $error_line = trim($lessFileLine[$line]);
    return $error_line;
}

function fixLESS($CodeLine)
{
    $Codeline = trim($CodeLine);
    // Remove Double Quotes
    if (substr_count($Codeline, '"') > 0) {
        $Codeline = str_replace('"', '\'', $Codeline);
    }// ------------

    // Fix comment spacing
    if ((strpos(trim($Codeline), '//') === 0) && (substr(trim($Codeline), 0, 3) != '//  ')) {
        $tempString = substr($Codeline, 2);
        $tempString = '// ' . $tempString;

        // Add a line break to last comment line
        $tempString = trim($tempString);
        if (substr_count($tempString, '-') > 30) {
            $tempString = $tempString . "\r\n";
        }
        $Codeline = $tempString;
    } // -----------

    $css = cssProp($Codeline);
    if (!empty($css)) {
        $prop = trim($css[0]);
        $val = trim($css[1]);

        // Remove 0px
        if (preg_match('/\b(0px)\b/', $val)) {
            $val = str_replace('0px', '0', $val);
            $Codeline = "$prop: $val";
        } // -----------

        // Add Background Mixin
        if (($prop == 'background') && preg_match('/\b(url)\b/', $val)) {
            $matches = [];
            $url = '';
            $p1 = '';
            preg_match('/url\((.*)\)/', $val, $matches);
            $full = $matches[0];
            if ($full != 'url("@{url}")') {
                if (!empty($matches[1])) {
                    $url = trim($matches[1], "'../");
                    $p1 = sprintf(".lib-url('%s');\n", $url);
                }
                if (!empty($full)) {
                    $CodeLine = str_replace($full, 'url("@{url}")', $CodeLine);
                }

                $Codeline = $p1 . $CodeLine;
            }

            /**
             * .lib-url('images/button_texture.svg');
             * background: @color-white  url("@{url}") 0 20%;
             */
        }

        // Add .lib-css()
        if ((substr_count($prop, '//') == 0) && (substr_count($val, '//') == 0) && (substr_count($val,
                    '@') > 0) && (substr_count($prop, '@') == 0)
        ) {
            $val = str_replace(";", '', $val);
            if (!in_array($prop, ['background', 'filter'])) {
                $Codeline = ".lib-css(" . $prop . ", " . $val . ");";
            }
        }
    }


    if (!empty($Codeline) && !hash_equals($Codeline, trim($CodeLine))) {
        return $Codeline;
    }
}

// Fixes
function cssProp($Codeline)
{
    // Break CSS to value and property =============
    if ((substr_count($Codeline, ':') == 1)) {
        $css = explode(":", $Codeline);
        if (count($css) === 2) {
            return $css;
        }
    }
}

?>