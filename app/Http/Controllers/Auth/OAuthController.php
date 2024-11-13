<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\GenericProvider;

class OAuthController extends Controller
{
    /**
     * @var GenericProvider
     */
    private $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => config('services.oauth.client_id'),
            'clientSecret'            => config('services.oauth.client_secret'),
            'redirectUri'             => config('services.oauth.redirect_uri'),
            'urlAuthorize'            => config('services.oauth.url_authorize'),
            'urlAccessToken'          => config('services.oauth.url_access_token'),
            'urlResourceOwnerDetails' => config('services.oauth.url_resource_owner')
        ]);
    }

    public function redirectToProvider()
    {
        // Fetch the authorization URL from the provider
        $authorizationUrl = $this->provider->getAuthorizationUrl();
        \Log::info("Stored state: " . $this->provider->getState());
        // Store the state generated for you and store it to the session.
        Session::put('oauth2state', $this->provider->getState());

        // Redirect the user to the authorization URL
        return redirect()->away($authorizationUrl);
    }

    public function handleProviderCallback(Request $request)
    {
        \Log::info("Returned state: " . $request->input('state'));
        if ($request->input('state') !== Session::get('oauth2state')) {
            Session::forget('oauth2state');
            return $this->errorResponse('Invalid OAuth state');
        }

        try {
            // Get the access token
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->input('code')
            ]);

            // Use the access token to retrieve user details
            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            $userDetails = $resourceOwner->toArray();

            return $this->successResponse('',[
                'accessToken' => $accessToken,
                'userDetails' => $userDetails,
            ]);

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
