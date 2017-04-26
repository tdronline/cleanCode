<?php require_once('conf.php'); ?>

<?php 
function formatTXT($savePath){
	//Format File
    $current_project = $_COOKIE['path'];
	$file = file_get_contents("../temp-files.txt");
	$file = str_replace($current_project.'/', '', $file);
	$file = str_replace('"', '', $file);
	$file = trim(str_replace('\\', '/', $file));
	
	//Save Formatted File
	file_put_contents($savePath, $file);
	unlink("../temp-files.txt");
}

function getNewLessFiles() {
    $current_theme = $_COOKIE['theme'];

	// Get New Files from Project Theme folder
	$di = new RecursiveDirectoryIterator($current_theme,RecursiveDirectoryIterator::SKIP_DOTS);
	$it = new RecursiveIteratorIterator($di);
	//file_put_contents("../temp-files.txt" ,'');
	foreach($it as $lessfile) {
		if (pathinfo($lessfile, PATHINFO_EXTENSION) == "less") {
			file_put_contents("../temp-files.txt", $lessfile."\r\n", FILE_APPEND | LOCK_EX);
			// echo $file, PHP_EOL;
		}
	}
}

function errorSuggession($style_file,$content){
	$less_error_line = ''; $error_fix = ''; $error_line = '';
	if (is_file($style_file)) {
		$cl = explode('|', $content);
		$c_line = trim($cl[0]) - 1;
		$lines = file($style_file);//file in to an array
		$error_line = trim($lines[$c_line]); //line 2
		$less_error_line = "<div class='less-error'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> $error_line</div>";
	}
	
    if(preg_match("/\bHexadecimal?\b/", $content) || preg_match("/\buse hex?\b/", $content) || preg_match("/\bCSS colours?\b/", $content)){
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> Use Color Variable with <strong>.lib-css();</strong></div>";
	}
	if(preg_match("/\bUnits specified?\b/", $content)){
		$fix = str_replace('0px','0',$error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
	if(preg_match("/\bquotes?\b/", $content)){
		$fix = str_replace('"','\'',$error_line);
        $error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
	if(preg_match("/\buppercase symbols?\b/", $content)){
		$fix = strtolower($error_line);
		$error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
	if(preg_match("/\bId selector?\b/", $content)){
		$fix = str_replace('#','.',$error_line);
		$error_fix = "<div class='less-correct'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span> $fix</div>";
    }
	
	return $less_error_line . $error_fix;
}

function abc(){
    global $project;
    print_r($project);
}
?>