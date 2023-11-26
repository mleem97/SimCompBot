<?php
include_once 'Communicators/Curler.php';

class Deleter extends Curler{
	
	public function __construct($csrf, $sessionid) {
		parent::__construct($csrf,$sessionid);
	}
	
    function del_achievement($id){
        $url = "https://www.simcompanies.com/api/v2/no-cache/companies/achievements/{$id}/";
        $response = $this->del($url);
		echo "\033[34mD\033[39m claimed achievement with id: {$id}\n";
        return $response;
	}
	function scrap($id){
        $url = "https://www.simcompanies.com/api/v2/companies/me/buildings/{$id}/";
        $response = $this->del($url);
		echo "\033[34mD\033[39m scrapped building with id: {$id}\n";
        return $response;
	}
	function cancel($id){
		$url = "https://www.simcompanies.com/api/v1/buildings/{$id}/busy/";
        $response = $this->del($url);
		echo "\033[34mD\033[39m canceled construction of building with id: {$id}\n";
        return $response;
	}
}