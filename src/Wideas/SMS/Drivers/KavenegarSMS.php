<?php

namespace Wideas\SMS\Drivers;

use GuzzleHttp\Client;
use Wideas\SMS\Kavenegar\KavenegarApi;
use Services_Twilio;
use Wideas\SMS\OutgoingMessage;

class KavenegarSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The Twilio SDK.
     *
     * @var Services_Twilio
     */
    protected $kavenegar;

    /**
     * Determines if requests should be checked to be authentic.
     *
     * @var bool
     */
    protected $verify;

    protected $defaultLineNumber;

    protected $lineNumbers;

    protected $client;

    /**
     * Constructs the TwilioSMS object.
     *
     * @param Client $client
     * @param bool $verify
     * @param bool $debug
     * @param Services_Twilio $twilio
     * @param $authToken
     * @param $url
     */
    public function __construct(Client $client, $verify = false, $debug=false)
    {
        $this->client = $client;
        $this->kavenegar = new KavenegarApi($debug);
        $this->verify = $verify;
        $this->defaultLineNumber = config('sms.kavenegar.line_numbers.default');
        $this->lineNumbers = config('sms.kavenegar.line_numbers.list');
    }

    /**
     * Sends a SMS message.
     *
     * @param \Wideas\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $data = $message->getData();
        $from = $message->getFrom();
        $view = $message->getView();
        $composeMessage = $message->composeMessage();
        $receptor = (array) $message->getTo();
        if (isset($data['method']) && $data['method'] == 'verifyLookup') {
            $this->verifyLookup($receptor[0], $data['token_data'], $view);
            return true;
        }
        if (count($receptor) == 1) {
            $to = $receptor[0];
            $this->kavenegar->send($to, $from, $composeMessage);
        } else {
            $this->kavenegar->sendArray($receptor, $from, $composeMessage);
        }
    }

    /**
     * Processing the raw information from a request and inputs it into the IncomingMessage object.
     *
     * @param $raw
     */
    protected function processReceive($raw)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw);
        $incomingMessage->setMessage($raw->body);
        $incomingMessage->setFrom($raw->from);
        $incomingMessage->setId($raw->sid);
        $incomingMessage->setTo($raw->to);
    }

    /**
     * Checks the server for messages status.
     *
     * @param array $options
     *
     * @return array
     */
    public function checkMessages(array $options = ['messageId'=>null])
    {
        $messageId = $options['messageId'];
        if (!empty($messageId)) {
            $this->kavenegar->status($messageId);
        }
    }

    /**
     * Gets message(s) by their ID.
     *
     * @param string|int $messageId
     *
     * @return \Wideas\SMS\IncomingMessage
     */
    public function getMessage($messageId)
    {
        $this->kavenegar->select($messageId);
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
     *
     * @return \Wideas\SMS\IncomingMessage
     */
    public function receive($raw)
    {
        $this->kavenegar->receive("receive", $this->defaultLineNumber, 0);
    }

    public function verifyLookup($to, $token, $template)
    {
        $this->kavenegar->verifyLookup($to, $token, $template);
    }

    /**
     * Checks if a message is authentic from Twilio.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateRequest()
    {
    }
}
