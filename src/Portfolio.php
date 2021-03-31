<?php namespace commishes\sdk\portfolio;

use CURLFile;
use magic3w\support\base32\Base32Int;
use spitfire\io\request\BadStatusCodeException;

/**
 * 
 */
class Portfolio
{
	
	/**
	 * 
	 * @var Client
	 */
	private $client;
	
	public function __construct(Client $client) 
	{
		$this->client = $client;
	}
	
	/**
	 * 
	 * @param CURLFile $file
	 * @param int $rating
	 * @param int $created 
	 * 
	 * @return object The parsed JSON from the server
	 * 
	 * @throws BadStatusCodeException If the server did not respond with a 200 code
	 */
	public function upload(CURLFile $file, int $rating, int $created = null) 
	{
		$request = $this->client->request('/upload/index.json');
		
		$request->post('upload', $file);
		$request->post('rating', $rating);
		$request->post('created', $created?? time());
		
		return $request->send()->expect(200)->json();
	}
	
	/**
	 * 
	 * @param int $uploadid
	 * @param string $title
	 * @param string $description
	 * 
	 * @return object The parsed JSON from the server
	 * 
	 * @throws BadStatusCodeException If the server did not respond with a 200 code
	 */
	public function edit(int $uploadid, string $title, string $description) 
	{
		$request = $this->client->request(sprintf('/upload/edit/%s.json', Base32Int::encode($uploadid)));
		
		$request->post('title', $title);
		$request->post('description', $description);
				
		return $request->send()->expect(200)->json();
	}
	
	/**
	 * 
	 * @return object The parsed JSON from the server
	 * @throws BadStatusCodeException If the server did not respond with a 200 code
	 */
	public function account() 
	{
		$request = $this->client->request('/account.json');
		
		return $request->send()->expect(200)->json()->payload;
	}
	
	/**
	 * @param int $uploadid
	 * 
	 * @return object The parsed JSON from the server
	 * @throws BadStatusCodeException If the server did not respond with a 200 code
	 */
	public function get(int $uploadid) 
	{
		$request = $this->client->request('/upload/show/' . $uploadid . '.json');
		$response = $request->send()->expect(200)->json();
		
		return $response;
	}
}
