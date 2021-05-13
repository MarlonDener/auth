<?php

//CSS
$minCSS = new \MatthiasMullie\Minify\CSS();

$minCSS->add(dirname(__DIR__, 1) . "/views/assets/css/style.css");
$minCSS->add(dirname(__DIR__, 1) . "/views/assets/css/form.css");
$minCSS->add(dirname(__DIR__, 1) . "/views/assets/css/button.css");
$minCSS->add(dirname(__DIR__, 1) . "/views/assets/css/message.css");
$minCSS->add(dirname(__DIR__, 1) . "/views/assets/css/load.css");
$minCSS->minify(dirname(__DIR__, 1). "/views/assets/style.min.css");


                //PARA FAFAZER 3-15
//JS
    

//$minJs = new \MatthiasMullie\Minify\CSS();
//$minJs->add(dirname(__DIR__, 1)."/views/assets/js/jquery.js");
//$minJs->add(dirname(__DIR__, 1)."/views/assets/js/jquery-ui.js");
//$minJs->minify(dirname(__DIR__,1)."/views/assets/scripts.min.js");

?>