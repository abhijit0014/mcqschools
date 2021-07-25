<?php
    // Report all errors
    error_reporting(E_ALL);

    require_once __DIR__.'/property.php';
    require_once __DIR__.'/rb-mysql.php';
    require_once __DIR__.'/session.php';
    require_once __DIR__.'/view_config.php';
    require_once __DIR__.'/security.php';

    // session manager
    SessionManager::setup();


    // set database
    //R::setup( 'mysql:host=localhost; dbname=mcqschoolsdb', 'mcqschoolsdbuser', 'kmp#2323@DB' );
    R::setup( 'mysql:host=localhost:3306; dbname=projectdb', 'root', 'master' );
    R::useFeatureSet( 'novice/latest' );
    R::ext('xdispense', function($type){
        return R::getRedBean()->dispense($type);
    });
    R::freeze( true );



    // get endpoint url
    $url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'],'/')) : '/';
    
    // endpoint security
    $securityManager = new SecurityManager();
    $securityManager->autoLogin();
    if ($url != '/')
    $securityManager->authenticate('/'.implode("/",$url));


    //routing 
    if ($url == '/')
    {
        require_once __DIR__.'/controller/index_controller.php';
        $indexController = New IndexController();
        print $indexController->index();

    }else{

        // This is not home page
        // Initiate the appropriate controller
        // and render the required view

        //controller
        $requestedController = $url[0]; 

        // If a second part is added in the URI, 
        // method
        $requestedAction = isset($url[1])? $url[1] :'';

        // The remain parts are considered as 
        // arguments of the method
        $requestedParams = array_slice($url, 2); 

        // Check if controller exists. NB: 
        // You have to do that for the model and the view too
        $ctrlPath = __DIR__.'/controller/'.$requestedController.'_controller.php';

        if (file_exists($ctrlPath))
        {
            require_once __DIR__.'/controller/'.$requestedController.'_controller.php';

            $controllerName = ucfirst($requestedController).'Controller';
            $controllerObj  = new $controllerName();

            // If there is a method - Second parameter
            if ($requestedAction != '')
            {
                // then we call the method via the view
                // dynamic call of the view
                if(method_exists($controllerObj,$requestedAction)){
                    print $controllerObj->$requestedAction($requestedParams);
                }
                else{
                    header('HTTP/1.1 404 Not Found');
                    $view = new view('404');
                }
            }

        }else{

            header('HTTP/1.1 404 Not Found');
            $view = new view('404');
            //die('404 - The file - '.$ctrlPath.' - not found');
        }
    }