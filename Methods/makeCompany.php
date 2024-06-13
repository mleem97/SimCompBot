<?php
include_once 'Communicators/Curler.php';
class makeCompany extends Curler
{

	public function __construct()
	{
	}

	public function make()
	{
		$csrf = 'MASTERMINDiiiiMASTERMINDiiiMASTERMINDiiiMASTERMINDiiiiMASTERMIND';

		$headers = array();
		$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:106.0) Gecko/20100101 Firefox/106.0';
		$headers[] = 'Referer: https://www.simcompanies.com/';
		$headers[] = "Cookie: csrftoken={$csrf}";
		$headers[] = "X-Csrftoken: {$csrf}";

		$ch = curl_init();

		$content = [
			'csrfmiddlewaretoken' => $csrf,
		];
		$json = json_encode($content);

		curl_setopt($ch, CURLOPT_URL, 'https://www.simcompanies.com/tutorial/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		$proxy = $this->getproxy();
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$response = curl_exec($ch);

		curl_close($ch);

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
		//var_dump($response);
		//var_dump($matches);
		if (array_key_exists(1, $matches[1]) === false) {
			return $this->make();
		}

		$newcsrf = $matches[1][0];
		$newcsrf = substr($newcsrf, 10);
		$newsesid = $matches[1][1];
		$newsesid = substr($newsesid, 10);

		$return = $this->skipTutorial($newcsrf, $newsesid, $proxy);
		return $return;
	}

	private function skipTutorial($csrf, $sessionid, $proxy)
	{
		$url = 'https://www.simcompanies.com/api/v2/auth/email/connect/';

		$t = intval(time() . substr(microtime(), 2, 3));
		$uri = parse_url($url, PHP_URL_PATH);
		$host = parse_url($url, PHP_URL_HOST);
		$md5 = md5($uri . $t);

		$headers = array();
		$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:106.0) Gecko/20100101 Firefox/106.0';
		$headers[] = 'Referer: https://www.simcompanies.com/';
		$headers[] = "Cookie: csrftoken={$csrf};sessionid={$sessionid};Objectives-tutorial={'encyclopediaVisited':true,'applesProduced':true,'retailStarted':true};Objectives-achievements={'achievementsVisited':true};";
		$headers[] = "X-Csrftoken: {$csrf}";
		$headers[] = 'X-Tz-Offset: -60';
		$headers[] = "X-Ts: {$t}";
		$headers[] = "X-Prot: {$md5}";

		$ch = curl_init();

		$email = $this->generateRandomString() . '@' . $this->generateRandomString() . '.com';
		$password = $this->generateRandomString();

		$content = [
			'email' => $email,
			'password' => $password,
			'timezone_offset' => '-60',
		];
		$json = json_encode($content);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$response = curl_exec($ch);

		curl_close($ch);

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
		//var_dump($response);
		//var_dump($matches);
		if (array_key_exists(1, $matches[1]) === false) {
			$proxy = $this->getproxy();
			return $this->skipTutorial($csrf, $sessionid, $proxy);
		}

		$newcsrf = $matches[1][0];
		$newcsrf = substr($newcsrf, 10);
		$newsessionid = $matches[1][1];
		$newsessionid = substr($newsessionid, 10);

		//var_dump($newcsrf);
		//var_dump($newsesid);

		echo "new company with email {$email} \n";

		$newsessionid = $this->switchRealm($csrf, $newsessionid, $proxy);

		$return = array($newcsrf, $newsessionid, $email, $password);
		return $return;
	}

	private function switchRealm($csrf, $sessionid, $proxy)
	{
		$url = 'https://www.simcompanies.com/api/v1/realm-create-company/1/';

		$t = intval(time() . substr(microtime(), 2, 3));
		$uri = parse_url($url, PHP_URL_PATH);
		$host = parse_url($url, PHP_URL_HOST);
		$md5 = md5($uri . $t);

		$headers = array();
		$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:106.0) Gecko/20100101 Firefox/106.0';
		$headers[] = 'Referer: https://www.simcompanies.com/';
		$headers[] = "Cookie: csrftoken={$csrf};sessionid={$sessionid};";
		$headers[] = "X-Csrftoken: {$csrf}";
		$headers[] = 'X-Tz-Offset: -60';
		$headers[] = "X-Ts: {$t}";
		$headers[] = "X-Prot: {$md5}";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$response = curl_exec($ch);

		curl_close($ch);

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
		//var_dump($response);
		//var_dump($matches);
		if (array_key_exists(0, $matches[1]) === false) {
			$proxy = $this->getproxy();
			return $this->switchRealm($csrf, $sessionid, $proxy);
		}

		$newsessionid = $matches[1][0];
		$newsessionid = substr($newsessionid, 10);

		return $newsessionid;
	}

	private function generateRandomString($length = 6)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
