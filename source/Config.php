<?php

if($_SERVER['SERVER_NAME'] == 'localhost'){
    require __DIR__. "/Minify.php";
}

    // CONEXÃO COM EMAIL

    define("MAIL",[
        "host"=>"seu host de email",
        "port" => "587",
        "user"=> "apikey",
        "passwd" => "sua chave de email",
        
        "from_name"=>"Marlon Dener",
        "from_email"=>"m.dener01@uni9.edu.br"
    ]);

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
        "host" => "localhost",
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

    define("SOCIAL",[
        "facebook_page"=>"Marlondener9",
        "facebook_author"=> "MarlonDener",
        "facebbok_appId"=>"teste",
        "twitter_site"=>"MarlonDener",
        "twitter_creator"=>"@MarlonDener"
    ]);

    

    
    define("GOOGLE_LOGIN", [

        "clientId" => "Seu id do google",
        "clientSecret"=> "seu secret",
        "redirectUri" => SITE["root"]."/google",
        

    ]);

?>