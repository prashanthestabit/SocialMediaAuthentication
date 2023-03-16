# Social Media Authentication Module

This is a pre-built module that provides social media authentication functionality in Laravel applications.

## Installation

To use this module, you need to clone the Modules folder to the root of your Laravel project. This module has been built using the [nwidart/laravel-modules](https://nwidart.com/laravel-modules/v6/installation-and-setup) package, so make sure you have this package installed in your project.for installation read nwidart/laravel-modules documents.

After cloning the Modules folder, navigate to the root of your project in a terminal window and run the following command to install the necessary dependencies:


``` bash
 composer require laravel/socialite
```

## Clone the code in laravel project Modules(Root) Folder

if Modules not exist you can create.

``` bash
git clone https://github.com/Hestabit/SocialMediaAuthentication
```

``` bash
php artisan module:enable SocialMediaAuthentication
```

``` bash
 php artisan migrate
```

## Usage

This module provides two endpoints for social media authentication:

<b>/api/social-auth/{driver}:</b> Redirects the user to the OAuth screen for the specified social media driver.
<b>/api/social-auth/{driver}/callback:</b> Handles the callback from the social media driver and returns a JWT token.

To use these endpoints, you need to replace {driver} with the name of the social media driver you want to authenticate against (e.g., facebook, google, twitter, etc.).


## Note

Before using this SocialMediaAuthentication module, please ensure that all the required services are registered in your Laravel project.

You can check and configure them in the config/services.php file. For example, if you are using Google as one of your authentication providers, you should have the following configuration in your services.php file:

``` bash
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
],
```
Please replace the client_id, client_secret, and redirect values with your actual Google credentials.

### For testing the api you can run the following command


```bash
php artisan test Modules/SocialMediaAuthentication/Tests/Unit/SocialMediaAuthenticationControllerTest.php
```
