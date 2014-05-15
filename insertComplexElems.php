<?php

	$publicId = "TESTAPIKEY";
	$magic = "XSDE422RSDJQDJW8QADM31SMA";
	$user = 'admin';

	$getTokenUrl = "http://127.0.0.1:3001/api/token";
	$addComplexElemToUsersUrl = "http://127.0.0.1:3001/json/complexElem/addComplexElemToUsers";
	$addComplexElemToGroupUrl = "http://127.0.0.1:3001/json/complexElem/addComplexElemToGroup";
	$addComplexElemUrl = "http://127.0.0.1:3001/json/complexElem/addComplexElem";

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
	$tokenResult = json_decode(sendPostRequest($getTokenUrl, $data), TRUE);
	$token = ($tokenResult['token']) ? $tokenResult['token'] : FALSE;
	$tokenValidation = 'Token validated: '.$token;
	
	print $tokenValidation;
	print '<br>';

	// add complexElems to user
	$complexElems = array();
	$complexElems['userIds'] = array();
	$complexElems['userIds'][] = 'pluginTest';
	$complexElems['userIds'][] = '';
	$complexElems['items'] = array();
	$complexElems['items'][] = array('title' => 'complexTestElem1', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem2', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem3', 'doc' => '{}');
	

	$addComplexElemToUsersUrl.= '?token='.$token.'&user='.$user;
	$addComplexElemToUsersResult = 'AddComplexElemToUsers result: '.sendPostRequest($addComplexElemToUsersUrl, $complexElems);
	print($addComplexElemToUsersResult);
	print '<br>';

	// add complexElems to group
	$complexElems = array();
	$complexElems['groupId'] = '53454cf5061bb3873bd90b8d';

	$complexElems['items'] = array();
	$complexElems['items'][] = array('title' => 'complexTestElem1', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem2', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem3', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem4', 'doc' => '{}');

	$addComplexElemToGroupUrl.= '?token='.$token.'&user='.$user;
	$addComplexElemToGroupResult = 'AddComplexElemToGroupResult result: '.sendPostRequest($addComplexElemToGroupUrl, $complexElems);
	print($addComplexElemToGroupResult);
	print '<br>';

	// add complexElems to apiPartner
	$complexElems = array();

	$complexElems['items'] = array();
	$complexElems['items'][] = array('title' => 'complexTestElem1', 'doc' => '{}');
	$complexElems['items'][] = array('title' => 'complexTestElem2', 'doc' => '{}');

	$addComplexElemUrl.= '?token='.$token.'&user='.$user;
	$addComplexElemResult = 'AddComplexElemToApiPartner result: '.sendPostRequest($addComplexElemUrl, $complexElems);
	print($addComplexElemResult);
	print '<br>';

	/// utils
	function sendPostRequest($url, $data) {

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
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