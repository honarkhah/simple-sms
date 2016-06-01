Simple SMS
==========

* [Introduction](#docs-introduction)
* [Requirements](#docs-requirements)
* [Configuration](#docs-configuration)
    * [Melipayamak Driver](#docs-melipayamak-driver)
    * [Twilio Driver](#docs-twilio-driver)
* [Driver Support](#docs-driver-support)
* [Usage](#docs-usage)
* [Outgoing Message Enclosure](#docs-outgoing-enclosure)
* [Incoming Message](#docs-incoming-message)

<a id="docs-introduction"></a>
## Introduction
Simple SMS is an easy to use package for [Laravel](http://laravel.com/) that adds the capability to send and receive SMS/MMS messages to mobile phones from your web app. It currently supports a free way to send SMS messages through E-Mail gateways provided by the wireless carriers. The package also supports 6 paid services, [Call Fire,](https://www.callfire.com/) [EZTexting,](https://www.eztexting.com) [LabsMobile,](http://www.labsmobile.com) [Mozeo,](https://www.mozeo.com/) [Nexmo,](https://www.nexmo.com/) [Twilio,](https://www.twilio.com) and [Zenvia.](http://www.zenvia.com.br)

<a id="docs-requirements"></a>
## Requirements

#### Laravel 5
* PHP: >= 5.5
* Guzzle >= 6.0

<a id="docs-configuration"></a>
## Configuration

#### Composer

First, add the Laravel SMS package to your `require` in your `composer/json` file:

    "require": {
        "honarkhah/laravel-sms": "~1"
    }

Next, run the `composer update` command.  This will install the package into your Laravel application.

#### Service Provider

Once you have added the package to your composer file, you will need to register the service provider with Laravel.

Add `Artisaninweb\SoapWrapper\ServiceProvider::class` in your `config/app.php` configuration file within the `providers` array.
Add `Wideas\SMS\SMSServiceProvider::class` in your `config/app.php` configuration file within the `providers` array.

#### Aliases

Finally, register the Facade.

Add `'SMS' => Wideas\SMS\Facades\SMS::class` in your `config/app.php` configuration file within the `aliases` array.

#### API Settings

You must run the following command to save your configuration files to your local app:

    php artisan vendor:publish

This will copy the configuration files to your `config` folder.

>Failure to run the `vendor:publish` command will result in your configuration files being overwritten after every `composer update` command.

#### Driver Configuration

<a id="docs-twilio-driver"></a>
######  Twilio Driver

This driver sends messages through the [Twilio](https://www.twilio.com/sms) messaging service.  It is very reliable and capable of sending messages to mobile phones worldwide.

    return [
        'driver' => 'twilio',
        'from' => '+15555555555', //Your Twilio Number in E.164 Format.
        'twilio' => [
            'account_sid' => 'Your SID',
            'auth_token' => 'Your Token',
            'verify' => true,  //Used to check if messages are really coming from Twilio.
        ]
    ];

It is strongly recommended to have the `verify` option enabled.  This setting performs an additional security check to ensure messages are coming from Twilio and not being spoofed.

To enable `receive()` messages you must set up the [request URL.](https://www.twilio.com/user/account/phone-numbers/incoming)  Select the number you wish to enable and then enter your request URL.  This request should be a `POST` request.

<a id="docs-melipayamak-driver"></a>
######  Melipayamak Driver

This driver sends messages through [Zenvia](http://www.zenvia.com.br) messaging service.  It is very reliable service for sending messages to mobile phones in Brazil.

    return [
        'driver' => 'melipayamak',
        'from' => 'CompanyABC', //Any String up to 20 chars.
        'melipayamak' => [
            'username' => 'Your Melipayamak Username',
            'password' => 'Your Melipayamak Password',
            'lineNumbers' => [
                'NONE'
            ]
        ]
    ];

The Zenvia API `recommends` that you should set an id parameter to each message. It will act as an unique identifier on Zenvia platform, can be used to check delivery status later and will prevent duplicated messages.

This package allows you to set this id passing it as another argument to $sms->to.

    $sms = SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('5511999991234', 'your-generated-message-id');
    });

It is not mandatory. For more information about this field, please, refer to the [API docs](http://docs.zenviasms.apiary.io/#introduction/parametro-id).

To enable `receive()` messages you must set up the [callback url](http://docs.zenviasms.apiary.io/#reference/callbacks-da-api) with Zenvia Support team. This request should be a `POST` request.

<a id="docs-driver-support"></a>
##Driver Support

Not all drivers support every method due to the differences in each individual API.  The following table outlines what is supported for each driver.

| Driver | Send | Queue | Pretend | CheckMessages | GetMessage | Receive |
| --- | --- | --- | --- | --- | --- | --- |
| Melipayamak | Yes | Yes | Yes | No | No | No |
| Twilio | Yes | Yes | Yes | Yes | Yes | Yes |

<a id="docs-usage"></a>
## Usage

#### Basic Usage

Simple SMS operates in much of the same way as the Laravel Mail service provider.  If you are familiar with this then SMS should feel like home.  The most basic way to send a SMS is to use the following:

    //Service Providers Example
    SMS::send('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });


The first parameter is the view file that you would like to use.  The second is the data that you wish to pass to the view.  The final parameter is a callback that will set all of the options on the `message` closure.

#### Send

The `send` method sends the SMS through the configured driver using a Laravel view file.

    SMS::send($view, Array $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    }
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });

It is possible to send a simple message without creating views by passing a string instead of a view.

    SMS::send($message, [], function($sms) {
        $sms->to('+98912XXXXXXX');
    }
    SMS::send('This is my message', [], function($sms) {
        $sms->to('+98912XXXXXXX');
    });

#### Driver

The `driver` method will switch the provider during runtime.

    //Will send through default provider set in the config file.
    SMS::queue('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });

    SMS::driver('twilio');

    //Will send through Twilio
    SMS::queue('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });

#### Queue

The `queue` method queues a message to be sent later instead of sending the message instantly.  This allows for faster respond times for the consumer by offloading uncustomary processing time. Like `Laravel's Mail` system, queue also has `queueOn,` `later,` and `laterOn` methods.

    SMS::queue('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });

>The `queue` method will fallback to the `send` method if a queue service is not configured within `Laravel.`

#### Pretend

The `pretend` method will simply create a log file that states that a SMS message has been "sent."  This is useful to test to see if your configuration settings are working correctly without sending actual messages.

    SMS::pretend('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
    });

You may also set the `pretend` configuration option to true to have all SMS messages pretend that they were sent.

    `/app/config/sms.php`
    return array(
        'pretend' => true,
    );

#### Receive

Simple SMS supports push SMS messages.  You must first configure this with your service provider by following the configuration settings above.

    Route::post('sms/receive', function()
    {
        SMS::receive();
    }

The receive method will return a `IncomingMessage` instance.  You may request any data off of this instance like:

    Route::post('sms/receive', function()
    {
        $incoming = SMS::receive();
        //Get the sender's number.
        $incoming->from();
        //Get the message sent.
        $incoming->message();
        //Get the to unique ID of the message
        $incoming->id();
        //Get the phone number the message was sent to
        $incoming->to();
        //Get the raw message
        $incoming->raw();
    }

The `raw` method returns all of the data that a driver supports.  This can be useful to get information that only certain service providers provide.

    Route::post('sms/receive', function()
    {
        $incoming = SMS::receive();
        //Twilio message status
        echo $incoming->raw()['status'];
    }

The above would return the status of the message on the Twilio driver.

>Data used from the `raw` method will not work on other service providers.  Each provider has different values that are sent out with each request.

#### Check Messages

This method will retrieve an array of messages from the service provider.  Each message within the array will be an `IncomingMessage` object.

    $messages = SMS::checkMessages();
    foreach ($messages as $message)
    {
        //Will display the message of each retrieve message.
        echo $message->message();
    }

The `checkMessages` method supports has an `options` variable to pass some settings onto each service provider. See each service providers API to see which `options` may be passed.

More information about each service provider can be found at their API docs.

* [Twilio](https://www.twilio.com/docs/api/rest/message#list-get)
* [Melipayamak](http://melipayamak.ir/)

#### Get Message

You are able to retrieve a message by it's ID with a simply call.  This will return an IncomingMessage object.

    $message = SMS::getMessage('aMessageId');
    //Prints who the message came from.
    echo $message->from();

<a id="docs-outgoing-enclosure"></a>
## Outgoing Message Enclosure

#### Why Enclosures?

We use enclosures to allow for functions such as the queue methods.  Being able to easily save the message enclosures allows for much greater flexibility.

#### To

The `to` method adds a phone number that will have a message sent to it.

    //Service Providers Example
    SMS::send('laravel-sms::welcome', $data, function($sms) {
        $sms->to('+98912XXXXXXX');
        $sms->to('+98912XXXXXXX');
    });

>The carrier is required for the email driver so that the correct email gateway can be used.  See the table above for a list of accepted carriers.

#### From

The `from` method will set the address from which the message is being sent.

    SMS::send('laravel-sms::welcome', $data, function($sms) {
        $sms->from('+98912XXXXXXX');
    });

#### attachImage

The `attachImage` method will add an image to the message.  This will also convert the message to a MMS because SMS does not support image attachments.

    //Twilio Driver
    SMS::send('laravel-sms::welcome', $data, function($sms) {
        $sms->attachImage('/url/to/image.jpg');
    });

>Currently only supported with the E-Mail and Twilio Driver.

<a id="docs-incoming-message"></a>
## Incoming Message

All incoming messages generate a `IncomingMessage` object.  This makes it easy to retrieve information from them in a uniformed way across multiple service providers.

#### Raw

The `raw` method returns the raw data provided by a service provider.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->raw()['status'];

>Each service provider has different information in which they supply in their requests.  See their documentations API for information on what you can get from a `raw` request.

#### From

This method returns the phone number in which a message came from.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->from();

#### To

The `to` method returns the phone number that a message was sent to.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->to();

#### Id

This method returns the unique id of a message.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->id();

#### Message

And the best for last; this method returns the actual message of a SMS.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->message();
