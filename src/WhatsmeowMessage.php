<?php

namespace Shadowbane\Whatsmeow;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Shadowbane\Whatsmeow\Exceptions\WhatsmeowException;

class WhatsmeowMessage implements JsonSerializable
{
    public string $type = 'message';
    public string $message;
    public string $phone;
    public string|null $attachment = null;
    public string|null $token = null;

    // legacy API support
    // should be removed in version 2.0
    public bool $secret = false;
    public bool $retry = false;
    public bool $isGroup = false;

    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
    }

    /**
     * @param string $content
     *
     * @return static
     */
    public static function create(string $content = ''): static
    {
        return new self($content);
    }

    /**
     * Notification message (Supports Markdown).
     *
     * @param string $content
     * @param string|null $attachment
     *
     * @return $this
     */
    public function content(string $content = '', string $attachment = null): self
    {
        $this->message = $content;
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Recipient's Phone number.
     *
     * @param $phoneNumber
     *
     * @throws WhatsmeowException
     *
     * @return $this
     */
    public function to($phoneNumber): self
    {
        // return debug phone number if local
        // this will prevent real user getting debug notification
        if (app()->isLocal() && config('app.debug')) {
            $this->phone = config('whatsmeow.debug_number');

            return $this;
        }

        // throw error if $phoneNumber is blank
        if (blank($phoneNumber)) {
            throw WhatsmeowException::destinationIsEmpty();
        }

        $this->phone = $phoneNumber;

        return $this;
    }

    /**
     * Set Wablas Token.
     * Useful when you want to send with another token.
     *
     * @param $token
     *
     * @throws WhatsmeowException
     *
     * @return $this
     */
    public function token($token): self
    {
        if (blank($token)) {
            throw WhatsmeowException::tokenIsEmpty();
        }

        $this->token = $token;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     *
     * @since 5.4
     */
    #[Pure]
 public function jsonSerialize(): mixed
 {
     return $this->toArray();
 }

    /**
     * Returns params payload.
     *
     * @return array
     */
    #[ArrayShape(['token' => 'null|string', 'phone' => 'null|string', 'secret' => 'bool', 'retry' => 'bool', 'isGroup' => 'bool', 'message' => 'string'])]
    public function toArray(): array
    {
        $arr = [
            'token' => $this->token,
            'phone' => $this->phone ?? null,
            'secret' => $this->secret,
            'retry' => $this->retry,
            'isGroup' => $this->isGroup,
        ];

        if ($this->type === 'message') {
            $arr['message'] = $this->message;
        }

        return $arr;
    }

    /**
     * Send message as Text.
     *
     * @throws WhatsmeowException
     *
     * @return $this
     */
    public function sendAsText(): static
    {
        return $this->setType('message');
    }

    /**
     * Set Message Type.
     *
     * @param string $type
     *
     * @throws WhatsmeowException
     *
     * @return $this
     */
    public function setType(string $type): static
    {
        // validate the type. allowed type is: message (default), image, audio, video, document
        // whatsmeow limitation: temporarily limited to message only
        if ($type != 'message') {
            throw WhatsmeowException::invalidMessageType();
        }

        $this->type = $type;

        return $this;
    }
}
