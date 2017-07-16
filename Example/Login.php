<?php 
	/*
	 * RETRIEVE REQUEST ID AND IDENTIFIER
	 */
	use  ClickWorks\FMDataAPIOAuthConnector;
	
	require_once ( '../OAuthConnector/Class.FMDataAPIOAuthConnector.php') ;
	
	// Get Request Id from storage, assumed that this is handled by the oAuthConnector scripts first
	$objFMDataAPIOAuthConnector = new FMDataAPIOAuthConnector();
	$objFMDataAPIOAuthConnector->SetApplicationCallbackURL('http://yourserver.yourdomain.org/FMDataAPIOAuthConnector/Example/GetCompaniesList.php') ;
	
	$providerButtonsHTML = $objFMDataAPIOAuthConnector->ListProvidersHTML();
	// Alternatively, call ListProviders() to obtain an associative array of configured OAuth Providers with the Provider name as a key and the URL link as a value
	
	/*
	 * APPLICATION LOGIC BELOW, ADJUST TO YOU OWN NEEDS. DISPLAY LOGIN BUTTONS FOR THE LIST OF PROVIDERS.
	 * Note: refer to each of the OAuth provider's websites to download images or code for buttons, or provide your own.
	 */
	
	?><!DOCTYPE html>
	<html>
		<head>
			<style type="text/css">
				h1 {
					font-family: Helvetica, Arial;text-align: center;
				}
				body {
					font-family: Helvetica, Arial;
				}
				div.button-links {
					width: 100%; display: block; text-align:center; 
				}
				.buttonlinks a{
					text-decoration: none;
				}
			</style>
			<title>List Of OAuth Providers</title>
		</head>
		<body>
			<h1>Please log in</h1>
			<div class="button-links">
				<?php echo $providerButtonsHTML; ?>
			<div>
		</body>
	</html>