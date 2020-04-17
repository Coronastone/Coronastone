<?php

namespace App\Auth\Grants;

use App\Models\Login;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ExternalCodeGrant extends AbstractGrant
{
    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes(
            $this->getRequestParameter('scope', $request)
        );
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes(
            $scopes,
            $this->getIdentifier(),
            $client,
            $user->getIdentifier()
        );

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken(
            $accessTokenTTL,
            $client,
            $user->getIdentifier(),
            $scopes
        );
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return UserEntityInterface
     * @throws OAuthServerException
     */
    protected function validateUser(
        ServerRequestInterface $request,
        ClientEntityInterface $client
    ) {
        $socialite = $this->getRequestParameter('provider', $request);
        if (is_null($socialite)) {
            throw OAuthServerException::invalidRequest('provider');
        }

        $code = $this->getRequestParameter('code', $request);
        if (is_null($code)) {
            throw OAuthServerException::invalidRequest('code');
        }

        $user = $this->getUserEntityByCode(
            $socialite,
            $code,
            $this->getIdentifier(),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(
                new RequestEvent(
                    RequestEvent::USER_AUTHENTICATION_FAILED,
                    $request
                )
            );

            throw OAuthServerException::invalidGrant();
        }

        return $user;
    }

    /**
     *  Retrieve a user by the given phone number.
     *
     * @param string  $socialite
     * @param string  $code
     * @param string  $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface  $clientEntity
     *
     * @return \Laravel\Passport\Bridge\User|null
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    private function getUserEntityByCode(
        $socialite,
        $code,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $info = Socialite::driver($socialite)
            ->redirectUrl(env('EXTERNAL_STATELESS_REDIRECT'))
            ->stateless()
            ->user();

        $provider = config('auth.guards.api.provider');

        if (
            is_null($model = config('auth.providers.' . $provider . '.model'))
        ) {
            throw new RuntimeException(
                'Unable to determine authentication model from configuration.'
            );
        }

        if (!method_exists($model, 'findByLogin')) {
            throw OAuthServerException::invalidCredentials();
        }

        if (!method_exists($model, 'createForLogin')) {
            throw OAuthServerException::invalidCredentials();
        }

        $user = (new $model())->findByLogin($socialite, $info->getId());

        if (is_null($user)) {
            if (!env('OAUTH_CREATE_USER', false)) {
                return;
            }

            $user = (new $model())->createForLogin($socialite, $info->getId());

            if (is_null($user)) {
                return;
            }
        }

        return new User($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'socialite';
    }
}
