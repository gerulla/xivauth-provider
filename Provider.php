<?php

namespace SocialiteProviders\XIVAuth;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'XIVAUTH';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'user',
        'refresh'
    ];

    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://xivauth.net/oauth/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://xivauth.net/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ];
        //Get User Data
        $response = $this->getHttpClient()->get('https://xivauth.net/api/v1/user', [ 'headers' => $headers]);
        $user = json_decode($response->getBody(), true);

        //Get Characters
        try {
            $response = $this->getHttpClient()->get('https://xivauth.net/api/v1/characters', [ 'headers' => $headers]);
            $characters = json_decode($response->getBody(), true);
        }catch (\Exception $e){
            if($e->getCode() === 403){
                $characters = null;
            }else throw $e;
        }
        return ['user' => $user, 'characters' => $characters];
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['user']['id'],
            'email' => $user['user']['email'] ?? null,
            'email_verified' => $user['user']['email_verified'] ?? null,
            'social_identities' => $user['user']['social_identities'] ?? null,
            'mfa_enabled' => $user['user']['mfa_enabled'],
            'verified_characters' => $user['user']['verified_characters'],
            'characters' => $user['characters'] ?? null
        ]);
    }

    public function withAllScopes(): Provider
    {
        $this->scopes = ['user', 'refresh', 'character:all', 'user:email', 'user:social', 'user:jwt', 'user:manage', 'character:jwt', 'character:manage'];
        return $this;
    }

    public function withExtraScopes(array $scopes): Provider
    {
        $this->scopes = array_merge($this->scopes, $scopes);
        return $this;
    }
    public function withCharactersScope(): Provider
    {
        $this->scopes[] = 'character:all';
        return $this;
    }

    public function withEmailScope(): Provider
    {
        $this->scopes[] = 'user:email';
        return $this;
    }

    public function withSocialScope(): Provider
    {
        $this->scopes[] = 'user:social';
        return $this;
    }
}