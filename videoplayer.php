<?php

if (isset($_GET['fileid']) && isset($_GET['nonce'])) {
    $tmp = base64_decode(str_replace(" ", "+", $_GET['fileid']));
    $title = "";
    if (strpos($tmp, '||') === false)
        die();
    else
        list($src, $title) = explode("||", $tmp);

    if (empty($src))
        die('Unknown file.');
}
else
    die();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />

        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript">
        //<![CDATA[
           jQuery(function($){
                $('#giml-video').attr('width',$(window).width()-20);
                $('#giml-video').attr('height',$(window).height()-20);
                $(window).resize(function(){
                   $('#giml-video').attr('width',$(window).width()-20);
                   $('#giml-video').attr('height',$(window).height()-20);
                });
            });
        //]]>
        </script>
    </head>
    <body>
        <?php echo $src; ?>		
    </body>

</html>