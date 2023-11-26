<?php
include_once 'Methods/makeCompany.php';
include_once 'Methods/Storage.php';
include_once 'Communicators/TelegramBot.php';
include_once 'Methods/Company.php';

class BotFather{
	
	public function __construct($db_table_name) {
		$this->db_table_name = $db_table_name;
		$this->main($db_table_name);
	}
	
    public function main($db_table_name){
		$storage = new Storage($db_table_name);
		
		while(true){
			$this->checkCompanies($storage);
	
			$companies = $storage->getCompanies();
			if(count($companies) === 0){
				break;
			}
			$i = 1;
			foreach ( $companies as $singlecompany ){
				echo "\n--- Company {$i} ---\n\n";
				$company = new Company();
				$storage->editCompany($singlecompany['email'], $company->run($singlecompany));
				$i++;
			}
			echo "\n--- Restarting in 124 seconds. ---\n\n";
			sleep(124);
		}
    }
	
	public function checkCompanies($storage){
		$companies = $storage->getCompanies();
		
		$bot = new TelegramBot();
		
		foreach($companies as $company){
			//var_dump($company);
			if($company['done'] === "1"){
				$storage->deleteCompany($company['email']);
				$message = "company with email: {$company['email']} has been deleted.";
				$bot->sendMessage($message);
				echo $message."\n";
			}
		}
			
		$companies = $storage->getCompanies();
		
		$amount = 1;
		
		//creates new companies if less than $amount
		if(count($companies) < $amount){
			$c = new makeCompany();
			for($count = 0; $count < ($amount-count($companies)); $count++ ){
				$array = $c->make();
				$storage->addCompany($array[0],$array[1],$array[2],$array[3]);
				die();
			}
			$companies = $storage->getCompanies();
		}
		
		//gives status updates on all companies to console and telegram
		$i = 1;
		$message = "--- $this->db_table_name Status ---";
		foreach($companies as $company){
			$message .= "\n\ncompany {$i} with email: {$company['email']} has status: {$company['status']}";
			$i++;
		}
		echo "\n".$message."\n";
		$bot->quietMessage($message);
	}
}
