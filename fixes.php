<?php
$file = base64_decode($_REQUEST['file']);
require_once("inc/functions.php");
$themePath = $_COOKIE['theme'];
$filePath = getfilePath($file);
$code = file_get_contents($filePath);
$code = explode("\n", $code);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <div class="btn btn-success btn-xs" id="fix_only">View Fixes Only</div>
    <h4 class="modal-title" id="myModalLabel">Auto Fix Errors <?php echo $_COOKIE['project']; ?></h4>
</div>
<div class="container-fluid">
    <div class="alert alert-info" role="alert" id="lessfile">
        <?php echo $filePath; ?>
    </div>
    <div class="fixes row">
        <div class="col-md-6">
            <?php
            $i = 1;
            foreach ($code as $c) {
                echo "<pre><span>$i</span>$c</pre>";
                $i++;
            }
            ?>
        </div>
        <div class="col-md-6 autofix">
            <?php
            $fixFile = '';
            foreach ($code as $c) {
                $line = fixLESS($c);
                if (!empty($line)) {
                    $class = "class='alert alert-success'";
                    $fixFile .= $line . "\n";
                } else {
                    $class = '';
                    $fixFile .= $c . "\n";
                    $line = trim($c);
                }
                echo "<pre $class>$line</pre>";

                file_put_contents("temp/temp.less",trim($fixFile));
            }
            ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="save_less">Save changes</button>
</div>
<script>
    $('#fix_only').click(function(){
        $(".autofix pre").fadeOut('fast');
        $(".autofix .alert-success").fadeIn();
    });
</script>