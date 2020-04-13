<?php

namespace App\Auth\Grants;

use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class DynamicCodeGrant extends AbstractGrant
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
        $phone_number = $this->getRequestParameter('phone_number', $request);
        if (is_null($phone_number)) {
            throw OAuthServerException::invalidRequest('phone_number');
        }

        $code = $this->getRequestParameter('code', $request);
        if (is_null($code)) {
            throw OAuthServerException::invalidRequest('code');
        }

        $user = $this->getUserEntityByPhoneNumber(
            $phone_number,
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
     * @param string  $phone_number
     * @param string  $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface  $clientEntity
     *
     * @return \Laravel\Passport\Bridge\User|null
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    private function getUserEntityByPhoneNumber(
        $phone_number,
        $code,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $provider = config('auth.guards.api.provider');

        if (
            is_null($model = config('auth.providers.' . $provider . '.model'))
        ) {
            throw new RuntimeException(
                'Unable to determine authentication model from configuration.'
            );
        }

        if (!method_exists($model, 'checkCodeForPassport')) {
            throw OAuthServerException::invalidCredentials();
        }

        $user = (new $model())->where('phone_number', $phone_number)->first();

        if (is_null($user)) {
            if (is_null($user)) {
                return;
            }
        }

        if (!$user->checkCodeForPassport($code)) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'sms';
    }
}
