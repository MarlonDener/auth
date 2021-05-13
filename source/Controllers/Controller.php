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
            //Diretório atual, e o segundo índice é para voltar 2 niveis para acessar a views
            $this->view = Engine::create(dirname(__DIR__, 2) . "/views", "php");
            $this->view->addData(["router" => $this->router]);

            $this->seo = new Optimizer();
            $this->seo->openGraph(site("name"),site("locale"),"article")->publisher(SOCIAL["facebook_page"],SOCIAL["facebook_author"])->twitterCard(SOCIAL["twitter_creator"],SOCIAL["twitter_site"],SITE["domain"])
            ->facebook(SOCIAL["facebbok_appId"]);
        }

        public function ajaxResponse(string $param, array $values){
            return json_encode([$param => $values]);
        }

    }

?>