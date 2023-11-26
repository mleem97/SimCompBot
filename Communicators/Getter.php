<?php
include_once 'Communicators/Curler.php';

class Getter extends Curler{
	
	public function __construct($csrf = '', $sessionid = '') {
		parent::__construct($csrf,$sessionid);
	}
	function get_allachievements(){
		$url = 'https://www.simcompanies.com/api/v2/companies/me/achievements/';
		$response = $this->get($url);
		echo "\033[32mG\033[39m getting all achievements\n";
		return $response;
	}
    function get_mybuildings(){
        $url = 'https://www.simcompanies.com/api/v2/companies/me/buildings/';
        $response = $this->get($url);
		echo "\033[32mG\033[39m getting all buildings\n";
        return $response;
    }
    function get_me(){
        $url = 'https://www.simcompanies.com/api/v2/companies/me/';
        $response = $this->get($url);
		echo "\033[32mG\033[39m getting company stats\n";
        return $response;
    }
    function get_warehouse(){
        $url = 'https://www.simcompanies.com/api/v2/resources/';
        $response = $this->get($url);
		echo "\033[32mG\033[39m getting warehouse\n";
        return $response;
    }
    function get_marketItem($id){
        $url = "https://www.simcompanies.com/api/v3/market/1/{$id}/";
        $response = $this->get($url);
		echo "\033[32mG\033[39m getting price of {$id}\n";
        return $response;
    }
	function get_newachievements(){
        $url = 'https://www.simcompanies.com/api/v2/no-cache/companies/me/achievements/';
        $response = $this->get($url);
		echo "\033[32mG\033[39m getting new achivements\n";
        return $response;
	}
	function get_contracts(){
		$url = 'https://www.simcompanies.com/api/v2/contracts-outgoing/';
		$response = $this->get($url);
		echo "\033[32mG\033[39m getting all contracts\n";
		return $response;
	}
}