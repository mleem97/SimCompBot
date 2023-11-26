<?php
include_once 'Communicators/Getter.php';
include_once 'Communicators/Poster.php';
include_once 'Communicators/Deleter.php';
include_once 'Methods/Builder.php';

class Company{
	
	public function __construct() {
		
	}
	
	public function run($company){
		$this->csrf = $company['csrf'];
		$this->sessionid = $company['sessionid'];
		$this->company = $company;
		
		
		$this->get = new Getter($this->csrf, $this->sessionid);
		$this->post = new Poster($this->csrf, $this->sessionid);
		
		$this->checkAchievements();
		$this->newAchievements();
		$this->checkAchievements();
		
		
		if($this->company['clearwarehouse'] === "0"){
			$this->clearwarehouse();
			
		} else if($this->company['buysupply'] === "0"){
			$this->buysupply();
			
		} else if($this->company['morestores'] === "0"){
			$this->morestores();
			
		} else if($this->company['sellcoffee'] === "0"){
			$this->sellcoffee();
			
		} else if($this->company['contracts'] === "0"){
			$this->contracts();
		} else{
			$this->company['done'] = "1";
			$this->company['status'] = "done.";
		}
		
		return $this->company;
	}
	private function clearwarehouse(){
		$this->company['status'] = "started clearing warehouse.";
		
		$warehouse = $this->get->get_warehouse();
		$me = $this->get->get_me();
		
		if(count($warehouse) > 0){
			if($me['authCompany']['money'] > 12000){
				$post = new Poster($this->csrf, $this->sessionid);
				$kind = $warehouse[count($warehouse)-1]['kind']['db_letter'];
				
				$cost = $this->get->get_marketItem($kind);
				$amount = $me['authCompany']['money'] * 0.95;
				$amount /= $cost[2]['price'];
				
				$amount = intval($amount);
				
				$this->post->buy(0,$amount,$kind);
				$warehouse = $this->get->get_warehouse();
				
				foreach($warehouse as $item){
					if($item['kind']['db_letter'] === $kind){
						$cost = $this->get->get_marketItem($kind);
						$this->post->sell($kind, $cost[0]['price']-0.001, $item['quality'], $item['amount'], $item['id']);
					}
				}
			}
		} else {
			$this->company['clearwarehouse'] = "1";
			$this->company['status'] = "finished clearing warehouse.";
		}
	}
	private function buysupply(){
		$this->company['status'] = "buying and supplying.";
		
		$warehouse = $this->get->get_warehouse();
		foreach($warehouse as $item){
			if($item['kind']['db_letter'] === 1){
				$cost = $this->get->get_marketItem(1);
				$this->post->sell(1, $cost[0]['price']-0.001, $item['quality'], $item['amount'], $item['id']);
			}
		}
		$me = $this->get->get_me();
		
		if(count($warehouse) === 0){
			if($me['authCompany']['money'] > 20000){
				
				$cost = $this->get->get_marketItem(1);
				$amount = $me['authCompany']['money'] * 0.95;
				$amount /= $cost[2]['price'];
				
				$amount = intval($amount);
				
				$this->post->buy(0,$amount,1);
				$warehouse = $this->get->get_warehouse();
				foreach($warehouse as $item){
					if($item['kind']['db_letter'] === 1){
						$cost = $this->get->get_marketItem(1);
						$this->post->sell(1, $cost[0]['price']-0.001, $item['quality'], $item['amount'], $item['id']);
					}
				}
			}
		}
	}
	private function morestores(){
		$this->company['status'] = "building more stores.";
		$builder = new Builder($this->csrf, $this->sessionid);
		$builder->buildBuilding('G');
		$builder->buildBuilding('G');
		$this->post = new Poster($this->csrf, $this->sessionid);
		$this->post->buy(0,1,119);
		$buildings = $this->get->get_mybuildings();
		foreach($buildings as $building){
			if(array_key_exists('busy', $building) == false){
				if($building['kind'] === 'G'){
					$this->post->work($building['id'], 1, 119, 28);
					$this->newAchievements();
				}
			}
		}
		foreach($buildings as $building){
			if(array_key_exists('busy', $building) == true){
				$this->post->rush($building['id']);
			}
		}
		$this->company['morestores'] = "1";
	}
	private function sellcoffee(){
		$this->company['status'] = "selling coffee.";
		
				
		$buildings = $this->get->get_mybuildings();
		$busy = false;
		foreach($buildings as $building){
			if(array_key_exists('busy', $building ) == true){
				$busy = true;
			}
		}
		if($busy === false){
			$this->post->buy(0,945,119);
			foreach ($buildings as $building){
				if($building['kind'] === 'G'){
					$this->post->work($building['id'], 315, 119, 28);
				}
			}
		}
	}
	private function contracts(){
		$this->company['status'] = "sending contracts.";
		
		$builder = new Builder($this->csrf, $this->sessionid);
		$builder->scrapall();
		$builder->cancelall();
		$buildings = $this->get->get_mybuildings();
		if(count($buildings) === 0){
			$warehouse = $this->get->get_warehouse();
			foreach($warehouse as $item){
				if($item['kind']['db_letter'] === 111){
					$price = $item['cost']['market'];
					$price /= $item['amount'];
					$price = ceil($price);
					$this->post->contract('Mastermind', 111, $price, $item['quality'], $item['amount'], $item['id']);
				}
			}
			$contracts = $this->get->get_contracts();
			if(count($contracts) === 0){
				$builder->buildBuilding('W');
				$builder->buildBuilding('H');
				$builder->cancelall();
				$warehouse = $this->get->get_warehouse();
				$buildings = $this->get->get_mybuildings();
				$me = $this->get->get_me();
				if(count($buildings) === 0){
					if($me['authCompany']['money'] < 21800){
						$available = false;
						foreach($warehouse as $item){
							if($item['kind']['db_letter'] === 111){
								$available = true;
							}
						}
						if($available === false){
							$this->company['contracts'] = "1";
							$this->company['done'] = "1";
							$this->company['status'] = "finished contracts.";
							return;
						}
					}
				}
				$this->contracts();
			}
		}
	}
	
	private function checkAchievements(){
		$achievements = $this->get->get_allachievements();
		foreach($achievements as $achievement){
			if($achievement['label'] == 'Supplier'){
				if($achievement['stars'] >= 5 && $this->company['buysupply'] === "0"){
					$this->company['buysupply'] = "1";
					$this->company['status'] = "finished buyer and supplier achievements.";
				}
			}
			if($achievement['label'] == 'Retailer'){
				if($achievement['stars'] >= 5 && $this->company['sellcoffee'] === "0"){
					$this->company['sellcoffee'] = "1";
					$this->company['status'] = "finished selling coffee.";
				}
			}
		}
	}
	private function newAchievements(){
		$del = new Deleter($this->csrf, $this->sessionid);
		$achievements = $this->get->get_newachievements();
		if ($achievements != null){
			foreach ($achievements as $achievement){
				$del->del_achievement($achievement['id']);
			}
		}
	}
}