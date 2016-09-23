# README #

Class for executing calls to CloudFlare API

### What is this repository for? ###

* Just-do-the-job class for executing calls to CloudFlare API
* Version 0.0.1

### How do I get set up? ###


```
#!php
<?php
require 'class.cloudflare_api.php';
```

### Usage ###
See examples.php for full examples.

Initialize:

```
#!php
<?php
$email = 'example@example.com'; // Email address registered with CloudFlare
$api_key = 'abf7f1bcd58fbe65749605956e928ee88f39'; // Global API key in CloudFlare > Your Account > My Settings > Account

$cloudflare_api = new CloudFlare_API($email, $api_key);
```
Make a simple GET call to API:

```
#!php
<?php
$function = 'zones'; // API function without the first foward flash (e.g. /zones)
$params = array(
	'name' => 'example.com',
	);
$zone_id = $cloudflare_api->send_GET_request($function, $params);
```

Make a RESTful call to API:

```
#!php
<?php
$function = 'zones/:zones_id/dns_records/:dns_record_id';
$params = array(
		'type' => 'A',
		'name' => 'exmaple.com',
		'content' => $content,
		'proxied' => true,
		);
$cloudflare_api->send_POST_request('PUT', $function, $params);
```

### Who do I talk to? ###

* Repo owner or admin
