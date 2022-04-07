<?php

namespace Shadowbane\Whatsmeow;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Shadowbane\Whatsmeow\Exceptions\WhatsmeowException;
use Throwable;

/**
 * Class WhatsmeowChannel.
 * Send a message to a whatsmeow channel.
 *
 * @package Shadowbane\Whatsmeow
 */
class WhatsmeowChannel
{
    /**
     * Channel Constructor.
     *
     * @param Whatsmeow $whatsmeow
     */
    public function __construct(
        public Whatsmeow $whatsmeow,
    ) {
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @throws WhatsmeowException
     * @throws Throwable
     *
     * @return array|null
     */
    public function send(mixed $notifiable, Notification $notification): ?array
    {
        $message = $notification->toWhatsapp($notifiable);

        if (is_string($message)) {
            $message = WhatsmeowMessage::create($message);
        }

        $params = $message->toArray();

        if (blank($params['phone'])) {
            if ($notifiable instanceof User) {
                $waNumber = $this->getNotifiableWhatsappNumber($notifiable);
                if (!blank($waNumber)) {
                    $message->to($waNumber);
                }
            }

            $params = $message->toArray();

            throw_if(blank($params['phone']), WhatsmeowException::destinationIsEmpty());
        }

        // if the token exists, then we need to refresh the macro.
        // after that, even if token does not exist, we need to remove it from the params.
        if (isset($params['token'])) {
            if (!blank($params['token'])) {
                $this->whatsmeow->setToken($params['token']);

                // set macro again
                $this->whatsmeow->setHttpMacro();
            }
        }
        unset($params['token']);

        // set endpoint
        $this->whatsmeow->setEndpoint('send-message');
        ray($params);

        return $this->whatsmeow->sendMessage($params);
    }

    /**
     * @param User $user
     *
     * @throws WhatsmeowException
     *
     * @return string
     */
    private function getNotifiableWhatsappNumber(User $user): string
    {
        // return debug phone number if local
        // this will prevent real user getting debug notification
        if (app()->isLocal() && config('app.debug')) {
            return config('whatsmeow.debug_number');
        }

        $waNumber = $user->{config('whatsmeow.whatsapp_number_field')};

        if (!blank(config('whatsmeow.whatsapp_number_json_field'))) {
            $waNumber = $waNumber[config('whatsmeow.whatsapp_number_json_field')];
        }

        // if the $waNumber starts with '+', remove it
        if (str_starts_with($waNumber, '+')) {
            $waNumber = substr($waNumber, 1);
        }

        // if the $waNumber is blank or is not valid number, throw exception
        if (blank($waNumber) || !is_int($waNumber)) {
            throw WhatsmeowException::destinationIsEmpty();
        }

        return $waNumber;
    }
}
