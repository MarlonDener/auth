<?php

    namespace Source\Controllers;

    class Web extends Controller
    {

        public function __construct($router)
        {
            parent::__construct($router);
            $_SESSION['user'] = "teste";
           if(!isset($_SESSION["user"])){
               echo 'ok';
            }
        }

        public function login(): void
        {
            $head = $this->seo->optimize(
                "Logar sua conta". site("name"),
                site("desc"),
                $this->router->route("web.login"),
                routeImage("Logar")
            )->render();
            //render tranforma todo o objeto em uma string

            echo $this->view->render("theme/login",[
               "head"=>$head 
            ]);
        }

        public function register(): void{
            $head = $this->seo->optimize(
                "Registre a sua conta em ". site("name"),
                site("desc"),
                $this->router->route("web.register"),
                routeImage("Registro")
            )->render();
            //render tranforma todo o objeto em uma string

            $form_user = new \stdClass();
            $form_user->first_name = null;
            $form_user->last_name = null;
            $form_user->email = null;

            echo $this->view->render("theme/register",[
               "head"=>$head,
               "user"=>$form_user
            ]);
        }
        public function forget(): void{

            $head = $this->seo->optimize(
                "Recupere a sua senha ". site("name"),
                site("desc"),
                $this->router->route("web.forget"),
                routeImage("Forget")
            )->render();
            //render tranforma todo o objeto em uma string

            echo $this->view->render("theme/forget",[
               "head"=>$head 
            ]);
        }
        public function reset($data){
              
            $head = $this->seo->optimize(
                "Crie sua nova senha ". site("name"),
                site("desc"),
                $this->router->route("web.reset"),
                routeImage("reset")
            )->render();
            //render tranforma todo o objeto em uma string

            echo $this->view->render("theme/reset",[
               "head"=>$head 
            ]);
        }
        
        public function error($data){
         $error = filter_var($data["errcode"],FILTER_VALIDATE_INT);  
         $head = $this->seo->optimize(
            "OPS{$error}". site("name"),
            site("desc"),
            $this->router->route("web.error", ["errcode" => $error]),
            routeImage($error)
        )->render();
        //render tranforma todo o objeto em uma string

        echo $this->view->render("theme/error",[
           "head"=>$head,
           "error"=> $error
        ]);
        }

    }


?>