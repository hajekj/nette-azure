<?php
namespace App\Presenters;

// Based on: https://github.com/michalsvec/nette-opauth

class AuthenticationPresenter extends \App\Presenters\BasePresenter
{
	public function actionAuth($strategy = NULL)
	{
        if($strategy == "azure") {
            $authUrl = $this->azureProvider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $this->azureProvider->getState();

            $this->redirectUrl($authUrl, 302);
            $this->terminate();
        }
	}

	public function actionCallback($strategy = NULL)
	{
		if ($strategy === NULL) {
			$this->flashMessage("Authentication failed.", "danger");
			$this->redirect(302, 'Homepage:default');
		}

        $identity = null;
        $accessToken = null;

        if($strategy == "azure") {
            $identityData = [];

            if($_GET['state'] == $_SESSION['oauth2state']) {
				unset($_SESSION['oauth2state']);
				$accessToken = $this->azureProvider->getAccessToken('authorization_code', ['code' => $_GET['code'], 'resource' => "https://graph.windows.net/"]);

                $resourceOwner = $this->azureProvider->getResourceOwner($accessToken)->toArray();
                $identityData = array_merge($identityData, $resourceOwner);
			}

            $identityData['accessToken'] = $accessToken;

            $identity = new \App\Identity\Azure($identityData);
        }

		$this->user->login($identity);
		$this->redirect(302, "Homepage:default");
	}

	public function actionLogout()
	{
		$this->getUser()->logout(1);
        // redirect to logout and use post_logout_uri
        $this->flashMessage("You have been logged out.", "info");
		$this->redirect(302, "Homepage:default");
	}
}