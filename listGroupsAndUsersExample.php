<?php
	$publicId = "--> your api key <--";
	$magic = "--> your magic word <--";
	$user = 'admin';

	$apiUrl = "--> The api url <--";
	$getTokenUrl = $apiUrl."/api/token";
	$listUsersUrl = $apiUrl."/json/user/list";
	$listGroupsUrl = $apiUrl."/json/groups/list";


	// handshake
	$ip = $_SERVER["REMOTE_ADDR"];
	$timestamp = time();
	$hash = md5($publicId . $ip . $timestamp . $magic);
	$data = array(
			"id"	=> $publicId,
			"uid"	=> $user,
			"ip"	=> $ip,
			"ts"	=> $timestamp,
			"hash"	=> $hash
	);
	$tokenResult = json_decode(sendRequest("POST", $getTokenUrl, $data), TRUE);
	$token = ($tokenResult['token']) ? $tokenResult['token'] : FALSE;
	$tokenValidation = 'Token validated: '.$token;
	print $tokenValidation;
	print '<br>';

	// list users
	$listUsersUrl.= '?token='.$token.'&user='.$user;
	$userListResult = 'Users list result: '.sendRequest("GET", $listUsersUrl);
	print($userListResult);

	print '<br>';
	// list groups
	$listGroupsUrl.= '?token='.$token.'&user='.$user;
	$groupsListResult = 'Groups list result: '.sendRequest("GET", $listGroupsUrl);
	print($groupsListResult);

	print '<br>';

	/// utils
	function sendRequest($method, $url, $data) {

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => $method,
		        'content' => http_build_query($data),
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$response = parse_http_response_header($http_response_header);

		$statusCode = $response[0]['status']['code'];

		if($statusCode === 200) {
			return $result;
		} else {
			die(print($statusCode));			
		}
	}

	function parse_http_response_header(array $headers)
	{
	    $responses = array();
	    $buffer = NULL;
	    foreach ($headers as $header)
	    {
	        if ('HTTP/' === substr($header, 0, 5))
	        {
	            // add buffer on top of all responses
	            if ($buffer) array_unshift($responses, $buffer);
	            $buffer = array();

	            list($version, $code, $phrase) = explode(' ', $header, 3) + array('', FALSE, '');

	            $buffer['status'] = array(
	                'line' => $header,
	                'version' => $version,
	                'code' => (int) $code,
	                'phrase' => $phrase
	            );
	            $fields = &$buffer['fields'];
	            $fields = array();
	            continue;
	        }
	        list($name, $value) = explode(': ', $header, 2) + array('', '');
	        // header-names are case insensitive
	        $name = strtoupper($name);
	        // values of multiple fields with the same name are normalized into
	        // a comma separated list (HTTP/1.0+1.1)
	        if (isset($fields[$name]))
	        {
	            $value = $fields[$name].','.$value;
	        }
	        $fields[$name] = $value;
	    }
	    unset($fields); // remove reference
	    array_unshift($responses, $buffer);

	    return $responses;
	}
?>