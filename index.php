<?php
include("conf.php");
@$errors = file_get_contents (PROJECT.DIRECTORY_SEPARATOR."codepool".DIRECTORY_SEPARATOR."dev".DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."report".DIRECTORY_SEPARATOR."less_report.txt");
$contents = explode("\r\n",$errors);

//TODO we need to see how we can run this command from the page it self currently it doesnt work
if(isset($_POST['runcmd'])){
  $cmd = "php ".PROJECT."/codepool/bin/magento dev:tests:run static";
  pclose(popen("start /B ". $cmd, "r")); 
  if(exec($cmd)){
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

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="container">
    <form method="post" class="pull-right"><button type="submit" class="btn btn-primary affix" name="runcmd">Update Errors</button> </form>
    <ul class="list-group">
    <?php 
		foreach($contents as $content){
			if(preg_match ("/\bFILE:?\b/", $content)){
				echo "<li class='list-group-item active'><span class='glyphicon glyphicon-file' aria-hidden='true'></span> $content</li>";
                $path = explode('/',$content);
                $b_path = array_slice($path, -6, count($path), true);
              $style_folder = '';
              foreach ($b_path as $p){
                $style_folder .= DIRECTORY_SEPARATOR."$p";
              }
              $style_folder = PROJECT.DIRECTORY_SEPARATOR."codepool\\app\\design\\frontend\\Netstarter\\mattblatt".$style_folder;
			}
			
			if(!preg_match ("/\bProperties sorted not alphabetically?\b/", $content)){
				if(preg_match ("/\bERROR?\b/", $content)){
					if(!preg_match ("/\bFOUND?\b/", $content)){
                        $error_line = '';
                        if(is_file($style_folder)){
                          $cl = explode('|',$content);
                          $c_line = trim($cl[0])-1;
                          $lines = file($style_folder);//file in to an array
                          $error_line = trim($lines[$c_line]); //line 2
                          $error_line = "<pre>$error_line</pre>";
                        }

						if(preg_match ("/\bHexadecimal?\b/", $content) || preg_match ("/\bUnits specified?\b/", $content) || preg_match ("/\bquotes?\b/", $content) || preg_match ("/\buppercase symbols?\b/", $content) || preg_match ("/\buse hex?\b/", $content) || preg_match ("/\bId selector?\b/", $content) || preg_match ("/\bCSS colours?\b/", $content)){
                          echo "<li class='list-group-item list-group-item-danger'>$content $error_line $style_folder</li>";
						}else{
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