<?php
    namespace Source\Controllers;
    use CoffeeCode\Optimizer\Optimizer;
    use CoffeeCode\Router\Router;
    use League\Plates\Engine;

    abstract class Controller{
        //ENGINER
        protected $view;
        protected $router;
        protected $seo;


        public function __construct($router)
        {
            $this->router = $router;
            $this->view = Engine::create();
        }

    }

?>