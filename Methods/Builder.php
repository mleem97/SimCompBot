<?php
include_once 'Communicators/Getter.php';
include_once 'Communicators/Poster.php';
include_once 'Methods/Storage.php';

class Builder{
	
	public function __construct($csrf, $sessionid) {
		$this->csrf = $csrf;
		$this->sessionid = $sessionid;
	}
	public function cancelall(){
		$get = new Getter($this->csrf,$this->sessionid);
		$buildings = $get->get_mybuildings();
		if($buildings == null){
			return;
		}
		$del = new Deleter($this->csrf,$this->sessionid);
		foreach ($buildings as $building){
			if(array_key_exists('busy', $building) === true){
				$del->cancel($building['id']);
			}
		}
	}
	public function scrapall(){
		$get = new Getter($this->csrf,$this->sessionid);
		$buildings = $get->get_mybuildings();
		if($buildings == null){
			return;
		}
		$del = new Deleter($this->csrf,$this->sessionid);
		foreach ($buildings as $building){
			if(array_key_exists('busy', $building) === false){
				$del->scrap($building['id']);
			}
		}
	}
	public function buildBuilding($kind){
		$get = new Getter($this->csrf, $this->sessionid);
		$post = new Poster($this->csrf, $this->sessionid);
		
		$allbuildings = file_get_contents('DataSets/allbuildings.json');
		$allbuildings = json_decode($allbuildings, true);
		foreach($allbuildings as $build){
			if ($build['kind'] == $kind){
				$building = $build;
			}
		}
		
		
		if($this->resources($building['costUnits']) == true){
			$mybuildings = $get->get_mybuildings();
			$me = $get->get_me();
			$max = $me['levelInfo']['maxBuildings'];
			$position = null;
			$blocked = array();
			
			if($mybuildings == null){
				$position = 0;
			} else{
				foreach ($mybuildings as $bld){
					$blocked[] = $bld['position'];
				}
				for($i = 0; $i < ($max); $i++){
					$in = in_array($i, $blocked, false);
					if($in == false){
						$position = $i;
						continue;
					}
				}
			}
			
			$post->build($kind, $position);
			return true;
		} else{
			return false;
		}
	}
	private function resources($amount){
		$get = new Getter($this->csrf, $this->sessionid);
		$post = new Poster($this->csrf, $this->sessionid);
		$wh = $get->get_warehouse();
		
		$needed101 = $amount * 4;
		$needed102 = $amount * 55;
		$needed108 = $amount * 16;
		$needed111 = $amount;
		
		if($wh != null){
			foreach ($wh as $item){
				if($item['kind']['db_letter'] == 101){
					if($item['amount'] <= $needed101){
						$needed101 -= $item['amount'];
					} else{
						$needed101 = 0;
					}
				} else if($item['kind']['db_letter'] == 102){
					if($item['amount'] <= $needed102){
						$needed102 -= $item['amount'];
					} else{
						$needed102 = 0;
					}
				} else if($item['kind']['db_letter'] == 108){
					if($item['amount'] <= $needed108){
						$needed108 -= $item['amount'];
					} else{
						$needed108 = 0;
					}
				} else if($item['kind']['db_letter'] == 111){
					if($item['amount'] <= $needed111){
						$needed111 -= $item['amount'];
					} else{
						$needed111 = 0;
					}
				}
			}
		}
		if($needed101 === 0){
			$c101[0]['price'] = 0;
		} else {
		    $c101 = $get->get_marketItem(101);
		}
		if($needed102 === 0){
			$c102[0]['price'] = 0;
		} else {
		    $c102 = $get->get_marketItem(102);
		}
		if($needed108 === 0){
			$c108[0]['price'] = 0;
		} else {
		    $c108 = $get->get_marketItem(108);
		}
		if($needed111 === 0){
			$c111[0]['price'] = 0;
		} else {
	    	$c111 = $get->get_marketItem(111);
		}
		
		$totalcost = ($c101[0]['price']*$needed101) + ($c102[0]['price']*$needed102) + ($c108[0]['price']*$needed108) + ($c111[0]['price']*$needed111);
		
		if($this->buildPossible($totalcost) == true){
			if($needed101 > 0){
				$post->buy(0, $needed101, 101);
			}
			if($needed102 > 0){
				$post->buy(0, $needed102, 102);
			}
			if($needed108 > 0){
				$post->buy(0, $needed108, 108);
			}
			if($needed111 > 0){
				$post->buy(0, $needed111, 111);
			}			
			return true;
		} else {
			return false;
		}
		
	}
	private function buildPossible($totalcost){
		$get = new Getter($this->csrf, $this->sessionid);
	    $me = $get->get_me();
	    $buildings = $get->get_mybuildings();
		if(($me['authCompany']['money'] * 0.95) > $totalcost){
			if($me['levelInfo']['maxBuildings'] > count($buildings)){
				return true;
			}
		} else {
			return false;
		}
		
	}
}