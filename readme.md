# Criteo PHP MAPI Client

### Features

- Authentication retry system
- Inline code documentation
- Save reporting results to file

### Installation

The Criteo MAPI package can be installed via Composer:

``` bash
composer require jpikowski/criteo-mapi-php-client:"^1.0"
```

Once installed, make sure your dependencies are included via the autoloader:

``` php
require_once __DIR__ . 'vendor/autoload.php';
```


### Basic Code Examples　

##### Initialization
``` php
require_once __DIR__ . 'vendor/autoload.php'; //Composer Dependencies

$criteo = new Criteo_MAPI( 'username', 'password' );
```

##### A Basic Request

Results from an API request are returned as an associative array of results.

``` php
$criteo->getCampaignsByAdvertiser( '12345' );
```

### Authentication Retry

Oauth2 Tokens retrieved from the `/oauth2/token` endpoint are valid for 5 minutes.

For the first request after initialization, the MAPI Client will request an authentication token based on the username and password provided and proceed with the request.

##### First Request (No Stored Auth)
![MAPI Authentication Retry](http://criteo.work/mapi/img/mapi-1.png)

For subsequent requests, the stored token may have become invalid for long-running processes. The MAPI Client will automatically detect the need for a refreshed token and retry a request that fails once because of a `401 Unauthorized` error.

##### Request with Expired or Invalid Token
![MAPI Authentication Retry](http://criteo.work/mapi/img/mapi-2.png)

### Other Features

##### Saving Reports to File

For reporting API calls, a filepath can be provided to optionally save results to a local path.

``` php
	$query = [
		'reportType' => 'CampaignPerformance',
		'advertiserIds' => '12345',
		'startDate' => '2019-01-15',
		'endDate' => '2019-01-16',
		'dimensions' => [
			'CampaignId',
			'AdvertiserId',
			'Day'
		],
		'metrics' => [
			'Clicks',
			'Displays'
		],
		'format' => 'csv',
		'currency' => 'USD',
		'timezone' => 'PST'
	];

$criteo->getStats($query, './reports/results.csv');
```

### Further Documentation

[MAPI Documentation (Criteo Help Center)](https://support.criteo.com/hc/en-us/sections/360000221105-Criteo-Marketing-API-)

[MAPI Spec and Test Tool (Swagger)](https://api.criteo.com/marketing/swagger/ui/index#/)

### License
[MIT](MIT-LICENSE)
