<?php 
if(isset($_POST['install'])){
	$projectPath = trim($_POST['project-path']);
	$phpPath = trim($_POST['php-path']);
	
	// Get New Files from Project Theme folder
	$cmdNewFiles = "FORFILES /P $projectPath\codepool\app\design /S /M *.less /C \"cmd /c echo @path\"  > test-files.txt";
	$changeFilesPath = $projectPath."\codepool\dev\tests\static\testsuite\Magento\Test\Less\_files";
	exec($cmdNewFiles);

	//Format File
	$file = file_get_contents("test-files.txt");
	$file = str_replace($projectPath."\codepool\\",'',$file);
	$file = str_replace('"','',$file);
	$file = trim(str_replace('\\','/',$file));

	//Save Formatted File
	file_put_contents("$projectPath\\codepool\dev\\tests\static\\testsuite\\Magento\\Test\\Less\\_files\\changed_files.txt",$file);


	//Copy Pear Install script to PHP Folder
	copy("includes/go-pear.phar", "$phpPath.\go-pear.phar");

	//Rename and Copy XML to config folder
	rename ("$projectPath\\codepool\\\dev\\tests\\static\\phpunit.xml.dist", "$projectPath\\codepool\\\dev\\tests\\static\\phpunit.xml.dist-original");
	copy("includes/phpunit.xml.dist","$projectPath\\codepool\\\dev\\tests\\static\\phpunit.xml.dist");
	file_put_contents ("../run.bat","php $projectPath\codepool\bin\magento dev:tests:run static");
	
	$projectPath = str_replace('\\','/',$projectPath);
	file_put_contents ("../conf.php","<?php define(\"PROJECT\", \"$projectPath\") ?>");

	// Clear Blacklist files
	file_put_contents("$projectPath\\codepool\\dev\\tests\\static\\testsuite\\Magento\\Test\\Less\\_files\\blacklist\\old.txt", "");
	@file_put_contents("$projectPath\\codepool\\dev\\tests\\static\\testsuite\\Magento\\Test\\Less\\_files\\blacklist\\ee.txt", "");
	
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Codesniffer Installer</title>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="container">
    <h1>Ready to Install</h1>
	<?php if(!isset($_POST['install'])){?>
	<div class="well well-lg">
	<form method="post">
	<div class="form-group">
    <label>Project path</label>
    <input type="text" class="form-control" name="project-path" placeholder="D:\magento\matt-blatt">
  </div>
  <div class="form-group">
    <label>PHP path</label>
    <input type="text" class="form-control" name="php-path" placeholder="D:\wamp64\bin\php\php7.0.6">
  </div>
  <button type="submit" name="install" class="btn btn-default">Submit</button>
	</form>
	</div>
	<?php } else {?>
<p>All Files are created.</p>
<p>1. Run below command from your PHP folder. Follow instructions and install it as System.</p>
<pre>php go-pear.phar</pre> 
<p>2. Run below command to install Code Sniffer</p>
<pre>pear install PHP_CodeSniffer</pre>
<p>3. Run the bat file in your myerror folder to update the error report file.</p>
<a href="../run.bat" class="btn btn-primary btn-xs">Get Bat File</a> <a href="../" class="btn btn-success btn-xs">View Report</a>
	<?php } ?>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>