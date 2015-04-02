<?php
use GuzzleHttp\Client;
use GuzzleHttp\Pool;

class GuzzleController extends Yaf_Controller_Abstract {
	public function testAction() {
		$arrUrl = [
			'http://www.baidu.com',
			'http://www.google.com.hk',
			'http://www.tuicools.net',
			'http://www.shukugang.com/sam/index/BCCA',
		];
		// $response = $client->get('http://www.baidu.com');
		// echo $response->getBody();

		// $requests = [
		// 	$client->createRequest('GET', 'http://httpbin.org'),
		// 	$client->createRequest('DELETE', 'http://httpbin.org/delete'),
		// 	$client->createRequest('PUT', 'http://httpbin.org/put', ['body' => 'test']),
		// ];

		// // Create a pool. Note: the options array is optional.
		// $pool = new Pool($client, $requests, $options);

		// // Send the requests
		// var_dump($pool->wait());

		$result = [];
		$result1 = [];
		echo xdebug_time_index() . "\n<br/>";
		$client = new Client();
		$options = [];
		$requests = [];
		foreach ($arrUrl as $url) {
			$requests[] = $client->createRequest('GET', $url);
		}

		$g_results = Pool::batch($client, $requests);
		foreach ($g_results->getSuccessful() as $response) {
			$result[] = $response->getBody()->getContents();
		}
		echo xdebug_time_index() . "\n<br/>";
		// exit;

		// try {

		// 	// Results is a GuzzleHttp\BatchResults object.
		// 	$results = Pool::batch($client, $requests);

		// } catch (\Exception $e) {
		// 	var_dump($e->getMessage());
		// 	die;
		// }
		// var_dump($results[0]);
		// Can be accessed by index.
		// echo $results[0]->getStatusCode();

		// Can be accessed by request.
		// echo $results->getResult($requests[0])->getStatusCode();

		// Retrieve all successful responses
		// foreach ($results->getSuccessful() as $response) {
		// 	echo $response->getStatusCode() . "\n<br/>";
		// 	echo $response->getBody() . "\n<br/>";
		// }

		// // Retrieve all failures.
		// foreach ($results->getFailures() as $requestException) {
		// 	echo $requestException->getMessage() . "\n";
		// }
		echo xdebug_time_index() . "\n<br/>";
		foreach ($arrUrl as $url) {
			$result1[] = \Helper::curl_get($url, 30);
		}
		echo xdebug_time_index() . "\n<br/>";

		var_dump($result);
		var_dump($result1);

		return false;
	}
}