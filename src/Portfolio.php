<?php namespace commishes\sdk\portfolio;

use Exception;

/**
 * 
 */
class Portfolio
{
	
	/**
	 *
	 * @var \auth\SSO
	 */
	private $sso;
	
	private $appid;
	
	private $endpoint;
	
	public function __construct($endpoint, $appid, $sso) {
		$this->endpoint = $endpoint;
		$this->sso = $sso;
		$this->appid = $appid;
	}
	
	public function upload($userid, \spitfire\storage\drive\File$file, $rating, $token = null, $created = null) {
		$url = $this->endpoint . '/upload/index.json?' .http_build_query([
			'signature' => strval($this->sso->makeSignature($this->appid)),
			'user' => $userid,
			'token'  => $token
		]);
		//signature=' . urlencode($this->sso->makeSignature($this->appid)) . '&user=' . $userid;
		
		$ch = curl_init($url);
		
		if (!$file->getPath()) {
			return null;
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array_filter(Array(
			 'upload' => new \CURLFile($file->getPath()),
			 'rating' => $rating,
			 'created'=> $created??time()
		)));
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		
		$response = curl_exec($ch);

		$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ($http_response_code !== 200) {
			throw new \Exception('Portfolio rejected the request' . $response, 1605141533);
		}
		
		return $response;
		
	}
	
	public function edit(\auth\Token$token, $uploadid, $title, $description) {
		
		$url = $this->endpoint . '/upload/edit/' . \Base32Int::encode($uploadid) . '.json?' .http_build_query([
			'signature' => strval($this->sso->makeSignature($this->appid)),
			'token' => $token->getId()
		]);
		
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array_filter(Array(
			 'title' => $title,
			 'description'=> $description
		)));
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		
		$response = curl_exec($ch);

		$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ($http_response_code !== 200) {
			throw new \Exception('Portfolio rejected the request' . $response, 1605141533);
		}
		
		return $response;
	}
	
	public function account(\auth\Token$token) {
		$url      = $this->endpoint . '/account.json?token=' . $token->getTokenInfo()->token;
		$response = file_get_contents($url);
		
		if (!strstr($http_response_header[0], '200')) { throw new Exception('Invalid response: ' . $response, 1703301441); }
		
		return json_decode($response)->payload;
	}
	
	public function get($uploadid) {
		$url      = $this->endpoint . '/upload/show/' . $uploadid . '.json';
		$response = request($url)->send()->expect(200)->json();
		
		return $response;
	}
}
