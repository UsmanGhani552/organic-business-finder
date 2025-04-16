<?php

declare(strict_types=1);

return [
    /*
     |--------------------------------------------------------------------------
     | Routing configuration
     |--------------------------------------------------------------------------
     |
     | This configuration is used to determine the routing behavior of the
     | Server notifications handler endpoint.
     |
     | You can find more information on documentation.
     | @see https://imdhemy.com/laravel-iap-docs/docs/get-started/routing
     */
    'register_routes' => false,

    'routing' => [
        'signed' => true,
        'middleware' => ['api'],
        'prefix' => 'api/iap',
    ],

    /*
     |--------------------------------------------------------------------------
     | Google Play Default Package name
     |--------------------------------------------------------------------------
     |
     | This value is the default package name used when the package name is not
     | provided while verifying the receipts.
     |
     */
    'google_play_package_name' => env('GOOGLE_PLAY_PACKAGE_NAME', 'com.some.thing'),

    /*
     |--------------------------------------------------------------------------
     | App Store Password
     |--------------------------------------------------------------------------
     |
     | This value is the app-specific share password generated by the app store.
     | @see https://imdhemy.com/laravel-iap-docs/docs/credentials/app-store
     |
     */
    'appstore_password' => env('APPSTORE_PASSWORD', ''),

    /*
     |--------------------------------------------------------------------------
     | Event Listeners
     |--------------------------------------------------------------------------
     |
     | This configuration is used to determine the event listeners that will be
     | registered with the application.
     | You can find a list of all available events of the documentation
     |
     | @see https://imdhemy.com/laravel-iap-docs/docs/server-notifications/event-list
     | @see https://imdhemy.com/laravel-iap-docs/docs/get-started/event-listeners
     |
    */
    'eventListeners' => [
        /*
         |--------------------------------------------------------------------------
         | App Store Events
         |--------------------------------------------------------------------------
         |
         | These event listeners are triggered when a new notification is received from App Store.
         | @see https://imdhemy.com/laravel-iap-docs/docs/server-notifications/event-list#app-store-events
         |
         */

        /*  \Imdhemy\Purchases\Events\AppStore\Cancel::class => [
              \App\Listeners\AppStore\Cancel::class,
          ],*/
          

        /*
         |--------------------------------------------------------------------------
         | Google Play Events
         |--------------------------------------------------------------------------
         |
         | These event listeners are triggered when a new notification is received from Google Play
         | @see @see https://imdhemy.com/laravel-iap-docs/docs/server-notifications/event-list#google-play-events
         */

        /* \Imdhemy\Purchases\Events\GooglePlay\SubscriptionRecovered::class => [
             \App\Listeners\GooglePlay\SubscriptionRecovered::class,
         ],*/
    ],

    /*
     | --------------------------------------------------------------------------
     | App store JWT configuration
     | --------------------------------------------------------------------------
     |
     | The following configuration is used to generate the JWT token used to
     | authenticate with the App Store server.
     */
    // Your private key ID from App Store Connect (Ex: 2X9R4HXF34)
    'appstore_private_key_id' => env('APPSTORE_KEY_ID'),
    // The path to your private key file (Ex: /path/to/SuperSecretKey_ABC123.p8)
    'appstore_private_key' => env('APPSTORE_PRIVATE_KEY_PATH'),
    // Your issuer ID from the Keys page in App Store Connect (Ex: "57246542-96fe-1a63-e053-0824d011072a")
    'appstore_issuer_id' => env('APPSTORE_ISSUER_ID'),
    // Your app’s bundle ID (Ex: “com.example.testbundleid2021”)
    'appstore_bundle_id' => env('APPSTORE_BUNDLE_ID'),

    // 'webhook_url' => env('APP_URL') . '/liap/notifications?provider=app-store',
];
