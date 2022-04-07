# Laravel WhatsMeow

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)
[![Total Downloads][ico-downloads]][link-packagist]

## About
This package provides easy integration with [Whatsmeow](https://github.com/shadowbane/whatsmeow), my own API for sending WhatsApp message.

## Installation
install the package via composer:

```bash
composer require shadowbane/laravel-whatsmeow
```

### Publishing Config
```
php artisan vendor:publish --provider="Shadowbane\Whatsmeow\WhatsmeowServiceProvider"
```

## Usage

### Configuration
add the following value to your `.env` file

```dotenv
WHATSMEOW_ENDPOINT=
WHATSMEOW_TOKEN=
WHATSAPP_NUMBER_FIELD=
WHATSAPP_NUMBER_JSON_FIELD=
DEBUG_WHATSAPP_NUMBER=
```

`WHATSMEOW_ENDPOINT`
Fill it with the url for Wablas API Endpoint.

`WHATSMEOW_TOKEN`
This is the token generated from your Wablas account.

`WHATSAPP_NUMBER_FIELD`
This is where you store the user's WhatsApp number in `users` table.

`WHATSAPP_NUMBER_JSON_FIELD`
only fill this if you store user's WhatsApp number on JSON column in database, for example, the data might look like this:
```
{"whatsapp": 0123456}
```

`DEBUG_WHATSAPP_NUMBER`
This is used when your `APP_ENV` is set to 'local' to prevent sending it to real user. It will send to debug number instead.

### Sending Text Message
You can send text message using 'via' method inside notification class.

`app/notifications/WhatsAppNotification`:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Shadowbane\Whatsmeow\Exceptions\FailedToSendNotification;
use Shadowbane\Whatsmeow\WhatsmeowChannel;
use Shadowbane\Whatsmeow\WhatsmeowMessage;

class WhatsappNotification extends Notification
{
    protected string $phoneNumber;
    protected string $message;

    /**
     * Create a new notification instance.
     *
     * @param string $phoneNumber
     * @param string $message;
     *
     * @return void
     */
    public function __construct(string $phoneNumber, string $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsmeowChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @throws FailedToSendNotification
     *
     * @return WhatsmeowMessage
     */
    public function toWhatsapp($notifiable)
    {
        return WhatsmeowMessage::create()
            ->to($this->phoneNumber)
            ->content($this->message);
    }
}
```

### Change token
If your application has multiple token for multiple purpose, you can chain `token($token)` method to your `WhatsmeowMessage` instance
```php

    use Shadowbane\Whatsmeow\WhatsmeowMessage;
    
    ...

    public function toWhatsapp($notifiable)
    {
        return WhatsmeowMessage::create()
            ->token('this-is-another-token-in-my-application')
            ->to($this->phoneNumber)
            ->content($this->message);
    }
```

<a name="send-to-notifiable"></a>
### Send Using The Notifiable Trait
If you want to send it via notifiable, you can refer to this example:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Shadowbane\Whatsmeow\WhatsmeowChannel;
use Shadowbane\Whatsmeow\WhatsmeowMessage;

class WhatsappNotification extends Notification
{
    protected string $phoneNumber;
    protected string $message;

    /**
     * Create a new notification instance.
     *
     * @param string $message;
     *
     * @return void
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsmeowChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return WhatsmeowMessage
     */
    public function toWhatsapp($notifiable)
    {
        return WhatsmeowMessage::create()
            ->content($this->message);
    }
}
```

Then, you can trigger it with:
```php
use App\Notifications\WhatsappNotification;

$user->notify(new WhatsappNotification($message));
```

### Sending to Multiple Users
This packages allows array to be passed as parameter in `to()` methods.
As Wablas allows comma-separated values as phone number, we automatically implode the array, and send it as comma-separated value to Wablas API.

#### Example:
```php


    use Shadowbane\Whatsmeow\WhatsmeowMessage;

    ...

    public function toWhatsapp($notifiable)
    {
        return WhatsmeowMessage::create()
            ->token('this-is-another-token-in-my-application')
            ->to([$destination1, $destination2])
            ->content($this->message);
    }
```

If you prefer to send it to notifiables, you can send it via notification facade.
```php
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\WhatsappNotification;

Notification::send(User::whereSomeCondition(1)->get(), new WhatsappNotification(123) );
```

### Notes
If you send it to notifiable, please make sure your `WHATSAPP_NUMBER_FIELD` reflecting the field where you store your user's WhatsApp number.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please send email to [adly.shadowbane@gmail.com](mailto:adly.shadowbane@gmail.com) instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


[ico-downloads]: https://img.shields.io/packagist/dt/shadowbane/laravel-whatsmeow.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/shadowbane/laravel-whatsmeow
