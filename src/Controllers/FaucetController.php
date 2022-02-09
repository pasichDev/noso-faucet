<?php
	
	namespace NosoProject\Controllers;
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	use NosoProject\Model\FaucetModel;
    
	final class FaucetController {
	    protected $container;
		protected $FaucetModel;

	    public function __construct($container){
		    $this->container = $container;
			$this->FaucetModel = new FaucetModel($container->get('db'));
	     
	
	    }

	    public function index(Request $request, Response $response){
			return $this->container->view->render($response, 'faucet.twig', $this->FaucetModel->OptionsArray());
	
			}
		
	}