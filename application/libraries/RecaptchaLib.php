<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class RecaptchaLib
{
	protected $verifyServer = 'www.google.com';
	protected $valid = true;
	protected $error;
	protected $privatekey = 'YOUR_PRIVATE_RECAPTCHA_KEY';

	function checkAnswer($challenge, $response)
	{
		if (empty($challenge) || empty($response))
		{
			$this->valid = false;
			$this->error = 'incorrect-captcha-sol';
		}

		$request = $this->qsencode(array(
			'privatekey' => $this->privatekey,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
			'challenge' => $challenge,
			'response' => $response,
		));
		
		$result = $this->httpPost($request);
		
		if (trim($result[0]) == 'false')
		{
			$this->valid = false;
			$this->error = $result[1];
		}
	}

	protected function qsencode($data)
	{
		$req = '';
		
		foreach ($data as $key => $val)
		{
			$req .= $key . '=' . urlencode(stripslashes($val)) . '&';
		}

		return substr($req, 0, strlen($req) - 1);
	}

	protected function httpPost($request)
	{
		$fs = fsockopen($this->verifyServer, 80, $errno, $errstr, 10);
		
		if ($fs === false)
		{
			show_error('reCAPTCHA - Could not open socket');
		}

		$http_request = 
			'POST /recaptcha/api/verify HTTP/1.0' . PHP_EOL .
			'Host: ' . $this->verifyServer . PHP_EOL .
			'Content-Type: application/x-www-form-urlencoded;' . PHP_EOL .
			'Content-Length: ' . strlen($request) . PHP_EOL .
			'User-Agent: reCAPTCHA/PHP' . PHP_EOL . PHP_EOL .
			$request
		;
		
		fwrite($fs, $http_request);

		$response = array();
		
		while (!feof($fs))
		{
			$response[] = fgets($fs, 1160);
		}
		
		fclose($fs);
		
		return array_slice($response, -2, 2);
	}

	function isValid()
	{
		return $this->valid;
	}
	
	function getError()
	{
		return $this->error;
	}
}