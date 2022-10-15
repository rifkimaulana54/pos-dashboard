<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class OrderApiHelper 
{
	public static function postData($data='')
	{
		$base_uri = config('app.api_order_url');

		$return = array();
		$data['form_params']['url'] = url('/');
		$data['url'] = $base_uri.$data['endpoint'];
		$data['typeRequest'] = 'POST';
		// eof post data param

		$api_result = self::callApi($data);

		return $api_result;
	}

	public static function getData($data='')
	{
		$base_uri = config('app.api_order_url');
		// get data param below
		$return = array();
		$data['form_params']['url'] = url('/');
		$data['url'] = $base_uri.$data['endpoint'];
		$data['typeRequest'] = 'GET';
		// eof get data param

		$api_result = self::callApi($data);

		return $api_result;
	}

	public static function updateData($data='')
	{
		$base_uri = config('app.api_order_url');
		// get data param below
		$return = array();
		$data['form_params']['url'] = url('/');
		$data['url'] = $base_uri.$data['endpoint'];
		$data['typeRequest'] = 'PUT';

		// eof get data param

		$api_result = self::callApi($data);

		return $api_result;
	}

	public static function deleteData($data='')
	{
		$base_uri = config('app.api_order_url');
		// get data param below
		$return = array();
		$data['form_params']['url'] = url('/');
		$data['url'] = $base_uri.$data['endpoint'];
		$data['typeRequest'] = 'DELETE';

		// eof get data param

		$api_result = self::callApi($data);

		return $api_result;
	}

	private static function callApi($data)
	{
		// fire guzzlehttp below
		$base_uri = config('app.api_order_url');
		$client = new Client( ['base_uri' => $base_uri] );
		// $currentUser = auth()->user();

		// echo $base_uri.'<br><br>';

		$body = [
			'headers' => [
	            'Content-Type'     => 'application/x-www-form-urlencoded',
	            // 'username' => $currentUser->name,
	            // 'userid' => $currentUser->id
	         ]
	     ];

	     if(!empty($data['headers']))
	     	$body['headers'] = array_merge($body['headers'],$data['headers']);

		if(!empty($data['form_params']))
		{
			if(empty($data['form_params']['multipart']))
				$body['form_params'] = $data['form_params'];
			else
			{
				unset($body['headers']['Content-Type']);// = 'multipart/form-data';
				$body['multipart'] = $data['form_params']['multipart'];
			}
		}

		// dd($data);
		// echo '$base_uri ='.$base_uri;

		// $response = $client->request($data['typeRequest'], $data['url'], $body  );
		// echo $response->getBody()->getContents();
		// echo '<pre>';
		// print_r($body);
		// echo '</pre>';
		// echo '<pre>';
		// print_r($response);
		// echo '</pre>';

		// die();

		try
		{
			$response = $client->request($data['typeRequest'], $data['url'], $body  );
			return $response->getBody()->getContents();
		}
		catch (RequestException $e) 
		{
			$return = $e->getResponse()->getBody(true);
			$decode = json_decode($return);
			
			if(!empty($decode->code) && $decode->code == 500)
			{
				if(!empty($decode->message) && !is_object($decode->message))
				{
					if(strtolower($decode->message) == 'token has expired')
					{
						Auth::logout();
		  				return redirect('/login');
		  			}
	  			}
	  			else
	  			{
	  				$decode->message = $decode->message->errorInfo[2];
	  				$return = json_encode($decode);
	  			}
			}

			return $return;
		}
		catch (ClientException $e) 
		{
			return $e->getResponse()->getBody(true);
		}
	}
}