<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public $azureProvider;
    public $azureBlobStorage;
    public $azureTableStorage;

    public function __construct()
    {
        // AZURE AD AUTHENTICATION
        //
        // Obtain server's address so it can be dynamically used for both local and remove environments
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
        $server = $protocol.$_SERVER['HTTP_HOST'];
        // Create provider for Azure AD authentication - https://msdn.microsoft.com/en-us/office/office365/howto/add-common-consent-manually
        $this->azureProvider = new \TheNetworg\OAuth2\Client\Provider\Azure([
            'clientId'          => getenv('clientId') ?: clientId,
            'clientSecret'      => getenv('clientSecret') ?: clientSecret,
            'redirectUri'       => $server.'/auth/azure/callback'
        ]);
        // Use our custom configured $httpClient hence local PHP doesn't have curl.cainfo set correctly
        $httpClient = new \GuzzleHttp\Client(['verify' => false]);
        $this->azureProvider->setHttpClient($httpClient);

        // AZURE STORAGE
        //
        // Connection string from Azure Portal
        $connectionString = getenv('storage') ?: storage;
        // Initialize Azure Blob Storage
        $this->azureBlobStorage = \WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService($connectionString);
        // Initialize Azure Table Storage connection
        $this->azureTableStorage = \WindowsAzure\Common\ServicesBuilder::getInstance()->createTableService($connectionString);
    }
}