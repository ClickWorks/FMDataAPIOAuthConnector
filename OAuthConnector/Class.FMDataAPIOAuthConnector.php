<?php
	namespace ClickWorks;
	/**
	* @package ClickWorks\FMDataAPIOAuthConnector
	*
	* @author Jeroen Aarts <jeroen.aarts@clickworks.eu>
	* @copyright 2017 ClickWorks bvba
	* @license http://opensource.org/licenses/gpl-license.php GNU Public License
	*/
	
	/**
	* FMDataAPIOAuthConnector
	*/
	class FMDataAPIOAuthConnector {
		
		/**
		* @var string Hostname of the FileMaker Server as evaluated from the Resource Owner's User Agent.
		*/
		public $fileMakerServerHostname;
		const STORAGEFOLDERPATH = '../Storage/';
		
		function __construct($fileMakerServerHostname = '') {
			if ( $fileMakerServerHostname == '') $fileMakerServerHostname = $_SERVER['SERVER_NAME'];
			$this->fileMakerServerHostname = $fileMakerServerHostname;
		}
		
		/**
		* Stores Application callback URL. This URL will be called after completion of the OAuth workflow. You can adapt this code to use database or sessions storage. 
		* If this OAuthConnector folder is stored in a submap of the document root of the webserver, please adjust access rights to the Storage folder, or move the Storage folder 
		* outside the document root and adapt the path below.
		*
		* @param string callbackURL
		*/
		public function SetApplicationCallbackURL($callbackURL)  {
			$file = fopen('../Storage/Callback.txt','w');
			$result = fwrite($file, $callbackURL);
			$result = fclose($file);
			}
		
		/**
		* Gets Application callback URL. 
		*
		*/
		public function GetApplicationCallbackURL()  {
			$fileCallback = fopen('../Storage/Callback.txt','r');
			$callbackURL = fread ( $fileCallback, filesize('../Storage/Callback.txt') );
			return $callbackURL;
		}
		
		/**
		* Gets Request ID. This is later used together with the Identifier returned afther Authentication grant to log in into the FileMaker database
		*
		* @param string returnURL URL that FileMaker Server redirects to after the OAuth Provider send the Authorization grant code to FileMaker Server
		* @param string provider Name of the OAuth Provider. The OAuth Provder MUST have been configured in FileMaker Server, i.a. a client id and secret must be registered.
		* @param string trackingID Optional trackingID
		*
		* @return string OAuth Provider authentication URL (used for the authorisation request)
		*/
		public function GetRequestId($returnURL,$provider='',$trackingID='')  {
			
			$server = $this->fileMakerServerHostname;
			$url = "http://" . $server . "/oauth/getoauthurl?trackingID=" . $trackingID . "&provider=" . $provider  ."&address=" . $server . "&X-FMS-OAuth-AuthType=2";
			$arrHeaders = array(
					'X-FMS-Application-Type:9', 
					'X-FMS-Application-Version:15', 
					'X-FMS-Return-URL:' . $returnURL 
				);
				
			// print_r($arrayHeaders); exit();
	
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders ) ;
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			$output = curl_exec($ch) ;
			curl_close($ch) ;
			
			//print_r($output) ; exit();
			
			// Parse output and get Request ID
			$start = strpos($output, 'X-FMS-Request-ID:',0) + 18;
			$end = strpos($output, "\r", $start ) ;
			$length = $end - $start;
			$requestId = substr($output, $start, $length) ;
			// Store Request Id for later usage
			$this->StoreRequestId($requestId) ;
			
			$startAuthURL = strpos($output, 'https://',0);
			$endAuthURL = strlen($output);
			$length = $end - $start;
			$authURL = substr($output, $startAuthURL, $endAuthURL) ;
			$authURLnew = (  str_replace(' ', '+', $authURL) ) ;
			return $authURLnew;
		}
		
	
		/**
		* Returns a list of configured OAuth Providers as an associative array with Provider name as key and an array of details as value. The link key can be used for login buttons
		*
		* @return string OAuth Provider authentication URL (used for the authorisation request)
		*/
		public function ListProviders()  {
			$url = "https://" . $this->fileMakerServerHostname . "/fmws/oauthproviderinfo";
	
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch) ;
			curl_close($ch) ;
			//print_r($output);
			
			$arrJSONProviders = json_decode ( $output , true);
			if(!count($arrJSONProviders)) return false ;
			
			foreach($arrJSONProviders['data']['Provider'] as $arrProviderDetails) {
				$providerName = $arrProviderDetails['Name'] ; 
				$arrProviders[$providerName]['link'] = "../OAuthDispatcher.php?action=GetRequestId&provider=". $providerName ;
			}
			
			return $arrProviders;
			
		}
		
		/**
		 * Returns HTML with login buttons for the configured OAuth Providers
		 * 
		 * @return string
		 */
		public function ListProvidersHTML() {
			$arrProviders = $this->ListProviders() ;
			if(!$arrProviders) return "No OAuth Providers are configured." ;
			
			//print_r ( $arrProviders);
			
			$html = '';
			foreach($arrProviders as $providerName=>$arrProviderDetails) {
				$html .= "<a href='../OAuthConnector/OAuthDispatcher.php?action=GetRequestId&provider=". $providerName . "'>
						<img src='../OAuthConnector/Resources/" . $providerName . ".png' style='width: 156px; height: 38px' /></a><br />";
			}
			
			return $html ;
		}
		
		
		/**
		* Redirects to the OAuth Provider's authorization and authentication pages.
		*
		* @param string URL to redirect for the grant request from the Resource Owner that authorize/authenticate FileMaker Server and your web application.
		*/
		public function AuthenticateWithOAuthProvider($authURL)  {
			//echo $authURL; exit();
			$ch = curl_init($authURL);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
			$output = curl_exec($ch) ;
			curl_close($ch) ;
		}
		
		
		/**
		* Put Request Id in storage . Adapt this code if using other storage types.
		*
		* @return boolean result
		*/
		public function StoreRequestId($requestId)  {
			$file = fopen(self::STORAGEFOLDERPATH . 'RequestId.txt','w');
			$result = fwrite($file, $requestId);
			fclose($file);
			return $result;
		}
		
		/**
		* Put Identifier in storage. Adapt this code if using other storage types.
		*
		* @return boolean result
		*/
		public function StoreIdentifier($identifier)  {
			$file = fopen(self::STORAGEFOLDERPATH . 'Identifier.txt','w');
			$result = fwrite($file, $identifier);
			fclose($file);
			return $result;	
		}
		
		
		/**
		* Gets Request Id from storage, assumed that this is handled by the oAuthConnector scripts first
		*
		* @return string Request Id
		*/
		public function GetStoredRequestId()  {
			$fileRequestId = fopen('../Storage/RequestId.txt','r');
			$requestId = fread ( $fileRequestId, filesize('../Storage/RequestId.txt') );
			return $requestId;
			
		}
		
		/**
		* Gets Identifier from storage, assumed that this is handled by the oAuthConnector scripts first
		*
		* @return string Identifier
		*/
		public function GetStoredIdentifier()  {
				$fileIdentifier = fopen('../Storage/Identifier.txt','r');
				$identifier = fread ( $fileIdentifier, filesize('../Storage/Identifier.txt') );
				return $identifier;
			
		}
		
	}
