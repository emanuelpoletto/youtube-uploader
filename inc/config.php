<?php
require_once 'google-api-php-client-2.2.0/vendor/autoload.php';

// Call set_include_path() as needed to point to your client library.
// require_once 'Google/Client.php';
// require_once 'Google/Service/YouTube.php';
session_start();

/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * Google Developers Console <https://console.developers.google.com/>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$OAUTH2_CLIENT_ID = '';
$OAUTH2_CLIENT_SECRET = '';

require_once 'inc/authenticate.php';
