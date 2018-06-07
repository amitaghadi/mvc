<?php
	class Twig_library {
        public  $twig;
        function __construct($templates_path=''){
            
            require_once $_SERVER['DOCUMENT_ROOT'].'vendor/autoload.php';
            if(isset($templates_path) && $templates_path!=""){
                $twigloader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'techmvc/views/'.$templates_path."/");
            } else {
                $twigloader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'techmvc/views/');
            }
            $this->twig = new Twig_Environment($twigloader, array('debug'=>true));
            $this->twig->addExtension(new Twig_Extension_Debug());
            $this->twig->setCache($_SERVER['DOCUMENT_ROOT'] . 'techmvc/views/html/twigcache/');
            // return $twig;
        }

    }
