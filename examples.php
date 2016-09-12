<?php
// --- general settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . 'class.cpanel_uapi.php';

$domain_name = 'example.com';

$email = 'example@example.com'; // Email address registered with CloudFlare
$api_key = 'abf7f1bcd58fbe65749605956e928ee88f39'; // Global API key in CloudFlare > Your Account > My Settings > Account
$cloudflare_api = new CloudFlare_API($email, $api_key);

try {
	// get zone id
	$function = 'zones'; // API function without the first foward flash (e.g. /zones)
	$params = array(
		'name' => 'example.com',
		);
	$zone_id = $cloudflare_api->send_GET_request('zones', $params);

	// get IP address for DNS record
	$ip_address = $cloudflare_api->send_GET_request('zones/:zones_id/dns_records',
		array('name' => 'example.com', 'type' => 'A'));

	$dns_data = array(
		'type' => 'A',
		'name' => $domain_name,
		'content' => $ip_address['result'][0]['content'],
		);

	// add new DNS record
	$add_dns_record_result = $cloudflare_api->send_POST_request('POST' , 'zones/:zones_id/dns_records', $dns_data);

	// update DNS record to use proxy
	$dns_data['proxied'] = true;
	$cloudflare_api->send_POST_request('PUT' , 'zones/:zones_id/dns_records/' . $add_dns_record_result['result']['id'], $dns_data);

	echo PHP_EOL . 'Add new DNS record in CloudFlare success: ' . $domain_name . PHP_EOL;
} catch (Exception $e) {
	echo PHP_EOL . 'Add new DNS record in CloudFlare failed:' . PHP_EOL . $e->getMessage();
	exit();
}


exit();
// eof