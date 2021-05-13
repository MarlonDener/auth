<?php

    namespace Source\Controllers;
    use Source\Models\User;

    class App extends Controller
    {
        //variavel de acesso aos dados do usuario
        protected $user;    

        public function __construct($router)
        {
            parent::__construct($router);
            
                if(empty($_SESSION["user"]) || !$this->user = (new User())->findById($_SESSION["user"])){
                    unset($_SESSION["user"]);
                    flash("error", "Acesso negado. Favor logue-se");
                    $this->router->redirect("web.login");

                }
                var_dump($_SESSION["user"]);
            
        }

        public function home() : void
        {
            var_dump($this->user);
        }

        
        public function logoff() : void
        {
            unset($_SESSION["user"]);
            flash("info", "Você saiu com sucesso, volte logo {$this->user->first_name}");
            $this->router->redirect("web.login");
        }

    }


?>