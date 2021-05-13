<?php


function site(string $param = null){
    if($param && !empty(SITE[$param])){
        return SITE[$param];
    }

    return SITE['root'];
}

//rotornar imagem de acordo com a rota

function routeImage(string $imageUrl)
{
    return "http://via.placeholder.com/1220x628/231bb5/FFF?text={$imageUrl}";
}


//renderização

function asset(string $path, $time = true){
    $file =  SITE["root"] . "/views/assets/{$path}";
    $fileOnDir = dirname(__DIR__, 1) . "/views/assets/{$path}";
    if($time && file_exists($fileOnDir)){
        $file.= "?time=" . filemtime($fileOnDir);
    }
    return $file;
}

function flash(string $type = null, string $message = null){
    if($type && $message){
        $_SESSION["flash"] = [
            "type" => $type,
            "messagem"=> $message
        ];
        return null;
    }
    if(!empty($_SESSION["flash"]) && $flash = $_SESSION["flash"]){
        unset($_SESSION["flash"]);
        return "<div class=\"message {$flash["type"]}\">{$flash["message"]}</div>";
    }
    return null;
}

?>