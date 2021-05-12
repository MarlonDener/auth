<?php

if($_SERVER['SERVER_NAME'] == 'localhost:70'){
    require __DIR__. "/Minify.php";
}

    define("SITE",[
        "name"=> "AUTH em MVC",
        "desc"=> "Login com Facebook e Google são uma das possibilidades de autenticação",
        "domain"=> "localauth.com",
        "locale"=> "pt_BR",
        "root"=> "http://localhost:70/codigoaberto/t1"
    ]);

       //CONEXÃO COM O BANCO DE DADOS

       define("DATA_LAYER_CONFIG", [
        "driver" => "mysql",
        "host" => "localhost:70",
        "port" => "3306",
        "dbname" => "auth",
        "username" => "root",
        "passwd" => "",
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);


    define("MAIL", ["EMAIL"=>"email"]);
    
    define("FACEBOOK_LOGIN", ["EMAIL"=>"email"]);
    
    define("GOOGLE_LOGIN", ["EMAIL"=>"email"]);

?>