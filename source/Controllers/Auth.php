<?php

//VERIFICAR O ARRAY DATA PARA MELHORE ENTENDIMENTO DO PROCESSO


namespace Source\Controllers;

//Use League\OAuth2\Client\Provider\Facebook;
Use League\OAuth2\Client\Provider\FacebookUser;

use Source\Models\User;
use Source\Support\Email;



class Auth extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function login(array $data) : void 
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

        if(!$email || !$passwd)
        {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Dados inválidos"
            ]);
            return;
        }
        //BUSCAR OS DADOS NO BANCO DE DADOS
        $user = (new User())->find("email = :e", "e={$email}")->fetch();

        //COMPARANDO OS DADOS DE SENHA DO INPUT COM A SENHA DO BANCO
        if(!$user || !password_verify($passwd, $user->passwd))
        {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "E-mail ou senha errada"
            ]);
            return;
        }
        //social validate
        $this->socialValidate($user);

        $_SESSION["user"] = $user->id;
        echo $this->ajaxResponse("redirect", ["url"=>$this->router->route("app.home")]);

      /*  echo $this->ajaxResponse("message", [
            "type" => "success",
            "message" => "Login efetuado com sucesso"
        ]);
        return;
        */
    }

    public function register($data) : void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if(in_array("", $data))
        {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Por favor, preencha todos os campos"
            ]);
            return;
        }
        //colar aqui se não der certo
        if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
        {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Por favor, informe um email válido"
            ]);
            return;
        }
        $CHECK_USER_EMAIL = (new User())->find("email = :e", "e={$data["email"]}")->count();

        if($CHECK_USER_EMAIL)
        {   
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Já existe esse usuário cadastrado"
            ]);
            return;
        }

        if(empty($data["passwd"]) || strlen($data["passwd"]) < 5)
        {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Senha muito fraca"
            ]);
            return;
        }
      
        $user = new User();
        $user->first_name = $data["first_name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->passwd = password_hash($data["passwd"], PASSWORD_DEFAULT);

        $this->socialValidate($user);

         //Atenção com essa função aqui   
        if(!$user->save()){
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => $user->fail()->getMessage()
            ]);
            return;
        }
        

        $_SESSION["user"] = $user->id;

        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("app.home")
        ]);
    }


    public function forget($data) : void
    {      
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        if(!$email)
        {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe o SEU E-MAIL para recuperar a senha"
            ]);
            return;
        } 
        $user = (new User())->find("email = :e", "e={$email}")->fetch();
        if(!$user)
        {    
            echo $this->ajaxResponse("message", [
            "type" => "error",
            "message" => "O email informado não é cadastrado"
        ]);
        return;

        }

        $user->forget = (md5(uniqid(rand(), true)));
        $user->save();

        $_SESSION["forget"] = $user->id;

        $email = new Email();
        $email->add(
            "Recupere a sua senha |" .site("name"),
            $this->view->render("emails/recover", [
                "user"=> $user,
                "link" => $this->router->route("web.reset", [
                    "email" => $user->email,
                    "forget" => $user->forget
                ])
            ]),
            "{$user->first_name} {$user->last_name}",
             $user->email
        )->send();

        flash("success", "Enviamos um link de recurepação para o seu e-mail");

        echo $this->ajaxResponse("redirect",[
              "url" => $this->router->route("web.forget")  
        ]);

    }
    
    //Função de reset

    public function reset($data):void
    {   
        if(empty($_SESSION["forget"]) || !$user = (new User())->findById($_SESSION["forget"]))
        {
            flash("error", "Não foi possível recuperar, tente novamente");
            echo $this->ajaxResponse("redirect",[
                "url" => $this->router->route("web.forget")
            ]);
            return;
        }

        //VERIFICAR SE OS CAMPOS ESTÃO VAZIOS

        if(empty($data["password"]) || empty($data["password_re"]))
        {
            echo $this->ajaxResponse("message",[
                "type"=>"alert",
                "message"=>"Informe e repita sua nova senha"
            ]);
            return;
        }

        //VERIFICAR SE AS SENHAS ESTÃO DIFERENTES

        if($data["password"] != $data["password_re"])
        {
            echo $this->ajaxResponse("message",[
                "type"=>"alert",
                "message"=>"Você informou duas senhas diferentes"
            ]);
            return;
        }

        if(strlen($data["password"]) < 5 || strlen($data["password_re"]) < 5)
        {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Senha muito fraca"
            ]);
            return;
        }

        
        $user->passwd = password_hash($data["password"], PASSWORD_DEFAULT);
        $user->forget = null;

        if(!$user->save())
        {
            echo $this->ajaxResponse("message",[
                "type"=>"alert",
                "message"=> $user->fail()->getMessage()
            ]);
            return;

        }
    
        unset($_SESSION["forget"]);

        flash("success", "Sua senha foi atualizada com sucesso");
        echo $this->ajaxResponse("redirect",[
            "url" => $this->router->route("web.login")
        ]);
            
    
    }
    //LOGIN COM O FACEBOOK
    public function facebook():void
    {   //JA ENTRA CONFIGURANDO OS DADOS INFORMADOS NO CONFIG

        $facebook = new \League\OAuth2\Client\Provider\Facebook([
            'clientId'          => 'seu id',
            'clientSecret'      => 'seu secret',
            'redirectUri'       => 'http://localhost:70/codigoaberto/t1/facebook',
            'graphApiVersion'   => 'version',
        ]);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        //se eu não tiver uma intereção eu automaticamente gero um URL e redireciono
        if(!$error && !$code)
        {
            $auth_url = $facebook->getAuthorizationUrl(["scope" => "email"]);
            header("Location: {$auth_url}");
            return;
        }

        if($error)
        {
            flash("error","Não foi possível logar com o Facebook");
            $this->router->redirect("web.login");
        }
        //eu recebi o codigo e não abrir a sessão ainda
        if($code && empty($_SESSION["facebook_auth"]))
        {
            try{
                  //Pega o código de autorização e vai gerar um Token  
                $token = $facebook->getAccessToken("authorization_code", ["code" => $code]);
                //Armazena um objeto serializado para usar em outra função  
                $_SESSION["facebook_auth"] = serialize($facebook->getResourceOwner($token)); 
                //Serialize, leva junto o objeto na aplicação, cria uma espécia de memória no PHP, que assim consigo acessar o objeto

            }catch(\Exception $exception){
                flash("error","Nãoo foi possível logar com o Facebook");
               
            }
        }
        /** $facebook_user FacebookUser */

        //Para usar o objeto SERIALIZE gerado aqui em baixo
        $facebook_user = unserialize($_SESSION["facebook_auth"]);
        //verifica se existe o usuario
        $user_by_id = (new User())->find("facebook_id = :id", "id={$facebook_user->getId()}")->fetch();
        if($user_by_id){
            unset($_SESSION["facebook_auth"]);
            $_SESSION["user"] = $user_by_id->id;
            $this->router->redirect("app.home");
        }

        //LOGIN POR E-MAIL
        $user_by_email = (new User())->find("email = :e", "e={$facebook_user->getEmail()}");

        if($user_by_email)
        {
            flash("info", "Olá {$facebook_user->getFirstName()}, faça login para conectar");
            $this->router->redirect("web.register");
        }
        //REGISTRAR SE NÃO EXISTE
        
        $link = $this->router-route("web.login");
        flash("info", "Olá {$facebook_user->getFirstName()}, <b>Se já tem uma conta clique em <a href='{$link}' title='fazer login'></a>fazer login, ou completo o seu cadastro</b>");
        $this->router->redirect("web.register");


    }

    public function google() : void 
    {
        $google = new \League\OAuth2\Client\Provider\Google(GOOGLE_LOGIN);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        //se eu não tiver uma intereção eu automaticamente gero um URL e redireciono
        if(!$error && !$code)
        {
            $auth_url = $google->getAuthorizationUrl();
            header("Location: {$auth_url}");
            return;
        }

        if($error)
        {
            flash("error","Não foi possível logar com o Google");
            $this->router->redirect("web.login");
        }
        //eu recebi o codigo e não abrir a sessão ainda
        if($code && empty($_SESSION["google_auth"]))
        {
            try{
                  //Pega o código de autorização e vai gerar um Token  
                $token = $google->getAccessToken("authorization_code", ["code" => $code]);
                //Armazena um objeto serializado para usar em outra função  
                $_SESSION["google_auth"] = serialize($google->getResourceOwner($token)); 
                //Serialize, leva junto o objeto na aplicação, cria uma espécia de memória no PHP, que assim consigo acessar o objeto

            }catch(\Exception $exception){
                flash("error","Não foi possível logar com o Google");
               
            }
        }
        /** $facebook_user FacebookUser */

        //Para usar o objeto SERIALIZE gerado aqui em baixo
        $google_user = unserialize($_SESSION["google_auth"]);
        //verifica se existe o usuario
        $user_by_id = (new User())->find("google_id = :id", "id={$google_user->getId()}")->fetch();
        if($user_by_id){
            unset($_SESSION["google_auth"]);
            $_SESSION["user"] = $user_by_id->id;
            $this->router->redirect("app.home");
        }

        //LOGIN POR E-MAIL
        $user_by_email = (new User())->find("email = :e", "e={$google_user->getEmail()}");

        if($user_by_email)
        {
            flash("info", "Olá {$google_user->getFirstName()}, você ja tem uma conta aqui, informe seu login para sicronizar a sua conta com o seu gmail");
            $this->router->redirect("web.login");
        }
        //REGISTRAR SE NÃO EXISTE
        
        $link = $this->router-route("web.login");
        flash("info", "Olá {$google_user->getFirstName()}, <b>Se já tem uma conta clique em <a href='{$link}' title='fazer login'></a>fazer login, ou completo o seu cadastro</b>");
        $this->router->redirect("web.register");


    }
    

    public function socialValidate($user) : void 
    {
         //FACEBOOK   

        if(!empty($_SESSION["facebook_auth"]))
        {
            $facebook_user = unserialize($_SESSION["facebook_auth"]);

            $user->facebook_id = $facebook_user->getId();
            $user->photo = $facebook_user->getPictureUrl();
            $user->save();

            unset($_SESSION["facebook_auth"]);
        }
        
        //GOOGLE

        if(!empty($_SESSION["google_auth"]))
        {
            $google_user = unserialize($_SESSION["google_auth"]);

            $user->google_id = $google_user->getId();
            $user->photo = $google_user->getAvatar();
            $user->save();

            unset($_SESSION["google_auth"]);
        }
    }

}
?>