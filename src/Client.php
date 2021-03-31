<?php namespace commishes\sdk\portfolio;

use magic3w\http\url\reflection\URLReflection;
use magic3w\phpauth\sdk\SSO;
use magic3w\phpauth\sdk\Token;
use spitfire\io\request\Request;

/* 
 * The MIT License
 *
 * Copyright 2021 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * The client provides context to the SDK, letting all objects in it interact 
 * with the server properly.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Client
{
	/**
	 * A client credential token allows the application to authenticate itself 
	 * against Chad if needed. Some endpoints require the application to be 
	 * authenticated, some require the user to be authenticated.
	 * 
	 * The application should construct the client with the appropriate credential
	 * for the user it wishes to "impersonate".
	 * 
	 * This also means that a client created for a user cannot be used to access
	 * the application's own accounts.
	 *
	 * @var Token
	 */
	private $credentials;
	
	/**
	 * The URL providing access to chad. This endpoint should point to the base url
	 * of the chad installation.
	 *
	 * @var string 
	 */
	private $endpoint;
	
	/**
	 * Instances a new client. The client allows to construct a CHAD instance that
	 * is bound to a certain user or client.
	 * 
	 * @param SSO|Token $credentials
	 * @param string $endpoint
	 */
	public function __construct($credentials, $endpoint) 
	{
		if ($credentials instanceof SSO) 
		{
			$reflection = URLReflection::fromURL($endpoint);
			$appid = $reflection->getUser();

			$this->endpoint  = (string)$reflection->stripCredentials();
			$this->credentials = $credentials->credentials($appid);
		}
		else {
			$this->credentials = $credentials;
			$this->endpoint = $endpoint;
		}
	}
	
	/**
	 * Prepares a authenticated request that all objects can use to interact with
	 * the API.
	 * 
	 * @param string $url
	 * @return Request
	 */
	public function request($url) : Request
	{
		$request = new Request($this->endpoint . $url);
		$request->header('Authorization', sprintf('Bearer %s', $this->credentials->getId()));
		
		return $request;
	}

}
