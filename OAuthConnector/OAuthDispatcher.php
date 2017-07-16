<?php
	/**
	 * @package ClickWorks\FMDataAPIOAuthConnector
	 *
	 * @author Jeroen Aarts <jeroen.aarts@clickworks.eu>
	 * @copyright 2017 ClickWorks bvba
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */
	use  ClickWorks\FMDataAPIOAuthConnector;

	require_once ( 'Class.FMDataAPIOAuthConnector.php') ;
	
	$server = $_SERVER['SERVER_NAME'] ;
	
	/**
	* The action parameter (used in a GET or POST request) tells the dispatcher script what to do
	*/
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'GetIdentifier' ;
	
	switch ( $action) {
		
		/**
		*	ENDPOINT FOR LOGIN BUTTON ACTION
		*	After a user chooses a OAuth provider, this action first fetches a Request Id from FileMaker Server and 
		*	then redirects to the  OAuthprovider for the Authorisation grant request.
		*/
		case 'GetRequestId';
			$provider = isset($_REQUEST['provider']) ? $_REQUEST['provider'] : '' ;
			$trackingID = isset($_REQUEST['trackingID']) ? $_REQUEST['trackingID'] : '' ;
			$returnURL =  'https://' . $server . $_SERVER['PHP_SELF'] ;  //'https://' . SERVER . $currentDirectory . '/oAuthDispatcher.php?action=GetIdentifier'
			$objFMDataAPIOAuthConnector = new FMDataAPIOAuthConnector($server);
			$authURL = $objFMDataAPIOAuthConnector->GetRequestId($returnURL,$provider,$trackingID);
			$objFMDataAPIOAuthConnector->AuthenticateWithOAuthProvider($authURL);
			
			break;
		
		/**
		*	ENDPOINT FOR CALLBACK BY FILEMAKER SERVER 
		*	After the Resource Owner (i.e. the end user) grants authorisation and is authenticated, the OAuath first 
		*	redirects to FileMAker Server and then FileMaker Server redirects to the OAuth dispatcher here so we can fetch the Identifier.
		*/
		case 'GetIdentifier';
			$identifier = isset($_REQUEST['identifier']) ? $_REQUEST['identifier'] : '' ;
			
			$objFMDataAPIOAuthConnector = new FMDataAPIOAuthConnector($server);
			$objFMDataAPIOAuthConnector->StoreIdentifier($identifier) ;
			$callbackURL = $objFMDataAPIOAuthConnector->GetApplicationCallbackURL() ;
			
			header("Location:$callbackURL") ;
			
			break;
		
	}
	