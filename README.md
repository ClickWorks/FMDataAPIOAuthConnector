# FMDataAPIOAuthConnector
OAuth Connector for the FileMaker Data API.$

INTRODUCTION
The FMDataAPIOAuthConnector handles the OAuth authentication process when using the FileMaker Data API, which is a RESTful API. Specifically, it retrieves RequestId and Identifier necessary for an OAuth Login call. 

After retrieval of these 2 pieces of information, your PHP application has all that is necessary to do the actual login request and obtain an Access Token from FileMaker Server. Please refer to the FileMaker Data API reference documentation on how to to perform that call. 

It is important to note that the Connector is not a wrapper around the REST API from FileMaker Server, but only a handler to facilitate the OAuth login. Your application will need to make HTTP calls to the FileMaker Data API in order to log in and retrieve data from FileMaker Server.

The general outline of using the connector is this:

* Your application shows a login page where your application users (or, in OAuth language 'Resource Owners') can click a login button linked to an OAuth Provider (Google, Amazon or Microsoft Azure). You specify a callback URL for your application for the connector to call after users successfully log in.
* The Connector calls the callback script and your application can retrieve the RequestId and Identifier necessary to log in into a FileMaker database.

REQUIREMENTS

- An installation of FileMaker Server 16 or higher.
- At least 1 OAuth Provider must be configured in the FileMaker Server Admin Console.
- At least 1 user account authenticating through a configured OAuth provider must be specified in the database you will be connecting to.
- PHP version 5.6 or higher must be installed on the webserver (master machine or worker in a multiple machine setup)
- The PHP curl extension must be enabled.

INSTALLATION

Copy the files and folders in (a subfolder of) the Document root of the Webserver on the FileMaker Server master machine in a Single machine setup, or on the worker machine in a multiple machine setup. The PHP code must live on the same machine as FileMaker Server.

It is recommended to move the 'Storage' folder outside the document root of your webserver, so it cannot be compromised. You must then adjust the FMDataAPIOAuthConnector class constant STORAGEFOLDERPATH to reflect this change. The current setup and configuration of storage works but is recommended for testing purposes only as it may expose Request Id and Identifier.

GETTING STARTED

* Create a login page and instantiate the FMDataAPIOAuthConnector class and specify a callback URL for the Connector to call after your application user has been authenticated.
* The PHP script called by the callback can then implement your application specific logic, i.e. retrieve data from FileMaker. In order to do that, it instantiates the FMDataAPIOAuthConnector again, and retrieves RequestId and Identifier from it, to login to a FileMaker database and receive an Acces Token.

To test the FMDataAPIOAuthConnector refer to the Login.php and GetCompaniesList.php files in the Example folder database, which are well documented.