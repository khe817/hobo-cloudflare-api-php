<?php
/**
 * Class for executing calls to CloudFlare API
 */
class CloudFlare_API
{
	private $url = 'https://api.cloudflare.com/client/v4/';
	private $email = '';
	private $api_key = '';
	private $request_headers = '';

	/**
	 * Contruct
	 * @param string $email   Email address registered with CloudFlare
	 * @param string $api_key Global API key in CloudFlare > Your Account > My Settings > Account
	 */
	public function __construct( $email, $api_key )
	{
		$this->email = $email;
		$this->api_key = $api_key;

		$this->request_headers = array(
			'X-Auth-Email: ' . $this->email,
			'X-Auth-Key: '   . $this->api_key,
			'Content-Type: application/json',
			);
	}

	/**
	* send_GET_request
	*
	* @param string $function API function without the first foward flash (e.g. /zones/:zone_identifier/dns_records)
	* @param array  $params
	* @return mixed
	*/
	public function send_GET_request( $function, $params = array() )
	{
		$curl = curl_init();
		$url = $this->url . $function . '?' . http_build_query($params);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->request_headers );
		curl_setopt( $curl, CURLOPT_URL, $url );

		$result = json_decode(curl_exec($curl) , true);

		if ( curl_errno($curl) ) {
			throw new Exception('Error Processing Request: ' . curl_error($curl), 1);
		}
		curl_close($curl);

		if ( $result['success'] != true && !empty($result['errors']) ) {
			throw new Exception('Error Processing Request: ' . json_decode($result['errors']) . "\n" . json_decode($result['messages']), 1);
		}

		return $result;
	}


	/**
	* send_POST_request
	*
	* @param string $method   request method (POST, PUT, DELETE)
	* @param string $function API function without the first foward flash (e.g. /zones/:zone_identifier/dns_records)
	* @param array  $params
	* @return mixed
	*/
	public function send_POST_request( $method, $function, $params = array() )
	{
		if (!empty($params)) $json_data = json_encode($params);

		$headers = $this->request_headers;
		$headers[] = 'Content-Length: ' . strlen($json_data);

		$curl = curl_init();
		$url = $this->url . $function;
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
		if (!empty($params)) curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_data );
		curl_setopt( $curl, CURLOPT_URL, $url );

		$result = json_decode(curl_exec($curl) , true);

		if ( curl_errno($curl) ) {
			throw new Exception('Error Processing Request: ' . curl_error($curl), 1);
		}
		curl_close($curl);

		if ( $result['success'] != true && !empty($result['errors']) ) {
			throw new Exception('Error Processing Request: ' . var_export($result['errors'], 1) . "\n" . var_export($result['messages'], 1), 1);
		}

		return $result;
	}
}
// eof