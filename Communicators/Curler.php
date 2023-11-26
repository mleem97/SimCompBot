<?php
include_once 'Methods/Storage.php';

class Curler{

	public function __construct($csrf = '', $sessionid = '') {
		$this->csrf = $csrf;
		$this->sessionid = $sessionid;
	}
	protected function get($url){
        $met = 'GET';
        $cont = null;
        $response = $this->communicate($url, $met, $cont);
		if ( is_string( $response ) ) {
	        $json = json_decode($response, true);
	        return $json;
		}
        return false;
    }
    protected function post($url, $content){
        $met = 'POST';
        $response = $this->communicate($url, $met, $content);
        return $response;
    }
	
	protected function del($url){
        $met = 'DELETE';
        $cont = null;
        $response = $this->communicate($url, $met, $cont);
        return $response;
	}
    
    protected function communicate($url, $method, $content){
		$t = intval(time().substr(microtime(),2, 3));
		$uri = parse_url($url, PHP_URL_PATH);
		$host = parse_url($url, PHP_URL_HOST);
		$md5 = md5($uri.$t);
		
		$headers = array();
		$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:106.0) Gecko/20100101 Firefox/106.0';
		$headers[] = 'Referer: https://www.simcompanies.com/';
		$headers[] = "Cookie: csrftoken={$this->csrf}; sessionid={$this->sessionid}";
		$headers[] = "X-Csrftoken: {$this->csrf}";
		$headers[] = 'X-Tz-Offset: -60';
		$headers[] = "X-Ts: {$t}";
		$headers[] = "X-Prot: {$md5}";
		
		$ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	    curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $method);
        if ($content != null){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			$proxy = $this->getproxy();
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			//echo "used proxy {$proxy}\n";
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		//print_r($proxy."\n");
        $response = curl_exec($ch);
		
    
		//var_dump(curl_getinfo($ch, CURLINFO_HEADER_OUT));
		//var_dump($response);
		//print_r(curl_getinfo($ch, CURLINFO_PRIMARY_IP));
		
		//var_dump($error);
		
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		
		curl_close($ch);
		
		if (!empty($error)) {
			
			error_log($error . ' ' . $code);
			error_log('got ' . $response . ', return communicate');
			return $this->communicate($url, $method, $content);
		}
		
		else if (  !is_null( $response ) && !empty( $response ) ) {
			
			error_log('got ' . gettype($response) . ', return response');
			return $response;
			
		}
		
		else {
			
			error_log('no error but no response as well ' . gettype($response) . ', return communicate ');
			return $this->communicate($url,$method,$content);
		}
		
		exit();
    }	
	protected function getproxy(){
		$prx = file_get_contents('DataSets/proxylist.txt');
		$prx = explode("\n", $prx);
		$nr = rand(0,count($prx)-1);
		//var_dump($prx[$nr]);
		return $prx[$nr];
	}
}
