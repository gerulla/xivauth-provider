# XIVAuthProvider

```bash
composer require gerulla/xivauth-provider
```

## Installation & Basic Usage

Please see the [Base Installation Guide](https://socialiteproviders.com/usage/),
then follow the provider-specific instructions below.

### Prepare OAuth provider & application in xivauth

Create a new OAuth Client within [XIVAuth](https://xivauth.net/), according to the [XIVAuth
Documentation](https://kazwolfe.notion.site/Documentation-128e77f0016c4901888ea1234678c37d)

### Add configuration to `config/services.php`

```php
'xivauth' => [
    'client_id' => env('XIVAUTH_CLIENT_ID'),
    'client_secret' => env('XIVAUTH_CLIENT_SECRET'),
    'redirect' => env('XIVAUTH_REDIRECT_URI'),
]
```

### Add provider event listener

#### Laravel 11+

In Laravel 11, the default `EventServiceProvider` provider was removed. Instead, add the listener using the `listen` method on the `Event` facade, in your `AppServiceProvider` `boot` method.


```php
Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
    $event->extendSocialite('xivauth', \SocialiteProviders\XIVAuth\Provider::class);
});
```

### Laravel 10 and below

Not Tested.

### Usage

Make sure you have the `user` and `refresh` scopes enabled in your XIVAuth OAuth
Client these are **required**, you may add additional scopes. Built-in scopes are:

```js
//Required 
user
refresh
//Optional
user:email
user:social
characters:all
```

You should now be able to use the provider like you would regularly use
Socialite (assuming you have the facade installed):

```php
return Socialite::driver('xivauth')->redirect();
```

To redirect to the authentication, and then:

```php
$user = Socialite::driver('xivauth')->user()
```

Optionally if you want to add additional scopes you can use the built in scope methods:
```php
return Socialite::driver('xivauth')->withCharactersScope()->redirect(); // characters:all
return Socialite::driver('xivauth')->withEmailScope()->redirect(); // user:email
return Socialite::driver('xivauth')->withSocialScope()->redirect(); // user:social
```
Or use the extras method to add multiple scopes:
```php
return Socialite::driver('xivauth')->withExtraScopes(['user:email', 'user:social', 'characters:all'])->redirect();
```

In the return function. The user will contain a `name` and `email` field
populated from the OAuth source along with a `attributes` object with the following fields:

```json
{
  "id": "string",
  // Only if you added user:email scope
  "email": "string",
  "email_verified": "bool",
  // Only if you added user:social scope
  "social_identities": [
    {
      "provider": "string",
      "external_id": "string",
      "name": "string",
      "nickname": "string",
      "email": "string", 
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  ],
  "mfa_enabled": "bool",
  "verified_characters": "bool",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  // Only if you added characters:all scope
  "characters": [
    {
      "persistent_key": "string",
      "lodestone_id": "string",
      "name": "string",
      "home_world": "string",
      "data_center": "string",
      "avatar_url": "string",
      "portrait_url": "string",
      "created_at": "timestamp",
      "verified_at": "timestamp",
      "updated_at": "timestamp"
    }
  ]
}

```
