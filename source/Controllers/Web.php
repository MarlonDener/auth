<?php

    namespace Source\Controllers;

    use Source\Models\User;

    class Web extends Controller
    {

        public function __construct($router)
        {
            parent::__construct($router);
           if(!empty($_SESSION["user"])){
              $this->router->redirect("app.home");
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

        public function reset($data) : variant_round{

            if(empty($_SESSION["forget"])){
                flash("info", "Informe o seu E-mail para recuperar a senha");
                $this->router->redirect("web.forget");
            }
            //se caso for vazio os dados do banco, não vai ser possivel recuperar
            
            $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
            $forget = filter_var($data["forget"], FILTER_DEFAULT);
            
            
            if(!$email || !$forget){

                flash("error", "Erro ao recuperar, tente novamente");
                $this->router->redirect("web.forget");
            }
            //BUSCAR DADOS NO BANCO DE DADOS 

            $user = (new User())->find("email = :e AND forget = :f", "e={$email}&f={$forget}")->fetch();
            
            if(!$user)
            {
                flash("error", "Erro ao recuperar, tente novamente");
                $this->router->redirect("web.forget");
            }
              
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
        return;
        }

    }


?>