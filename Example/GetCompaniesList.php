<?php

	/*
	* RETRIEVE REQUESTID AND IDENTIFIER NECESSARY TO RETRIEVE AN ACCESS TOKEN.
	*/
	
	use  ClickWorks\FMDataAPIOAuthConnector;
	
	require_once ( '../OAuthConnector/Class.FMDataAPIOAuthConnector.php') ;
	$objFMDataAPIOAuthConnector = new FMDataAPIOAuthConnector();
	$requestId = $objFMDataAPIOAuthConnector->GetStoredRequestId();
	$identifier = $objFMDataAPIOAuthConnector->GetStoredIdentifier();
	
	/*
	* YOUR APPLICATION LOGIC BELOW.
	*/
	
	$server = 'yourserver.yourdomain.org';
	
	//--  Authenticate
	$url = "https://" . $server . "/fmi/rest/api/auth/Companies";
	$arrData['oAuthRequestId'] = $requestId ;
	$arrData['oAuthIdentifier'] = $identifier;
	$arrData['layout'] = 'Companies';
	$jsonData = json_encode($arrData) ;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json','X-FM-Data-Login-Type:oauth')) ;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST , 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	$output = curl_exec($ch) ;	
	curl_close($ch) ;
	
	// Parse the response and fetch the Access Token. Some error handling would be a great idea!
	$arrResponse = json_decode ($output, true) ;
	$accessToken = $arrResponse['token'];
	
	//--  Get data
	$url = "https://" . $server . "/fmi/rest/api/record/YourDatabase/YourLayout";
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json','FM-Data-Token:' . $accessToken)) ;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$output = curl_exec($ch) ;
	
	// Display the raw JSON output
	print_r($output);