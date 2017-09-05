<?php

namespace Wideas\SMS\Drivers;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;
use Wideas\SMS\IncomingMessage;
use Wideas\SMS\OutgoingMessage;

class SMS5m5SMS extends AbstractSMS implements DriverInterface
{
    protected $lineNumbers;

    public function setLineNumbers(array $numbers)
    {
        $this->lineNumbers = $numbers;
    }

    public function getLineNumbers()
    {

        return $this->lineNumbers;
    }

    public function getOneLine()
    {
        return $this->lineNumbers[array_rand($this->lineNumbers, 1)];

    }

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'http://www.5m5.ir/webservice/smsService.php?wsdl';


    /**
     * SMS5m5SMS constructor.
     * @param $username
     * @param $password
     * @param $lineNumbers
     */
    public function __construct($username, $password, $lineNumbers)
    {
        ini_set('default_socket_timeout', 60);

        $this->settings = [
            'username' => $username,
            'password' => $password,
        ];
        $this->lineNumbers = $lineNumbers;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The SMS message instance.
     */
    public function send(OutgoingMessage $message)
    {
        SoapWrapper::add(function ($service) {

            $service->name('send_sms')
                ->wsdl($this->apiBase)
                ->trace(true);
        });


        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $to) {
            $data = [
                'sender_number' => $this->getOneLine(),
                'receiver_number' => $to,
                'note' => $composeMessage,
                'ersal_flash' => false
            ];

            $data = array_merge($this->settings, $data);

            // Using the added service
            SoapWrapper::service('send_sms', function ($service) use ($data) {
                return $service->call('send_sms', [$data])->send_smsResponse;
            });
        }
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @throws \RuntimeException
     */
    protected function processReceive($rawMessage)
    {
        throw new \RuntimeException('LabsMobile does not support Inbound API Calls.');
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @throws \RuntimeException
     */
    public function checkMessages(array $options = array())
    {
        throw new \RuntimeException('LabsMobile does not support Inbound API Calls.');
    }

    /**
     * Gets a single message by it's ID.
     *
     * @throws \RuntimeException
     */
    public function getMessage($messageId)
    {
        throw new \RuntimeException('LabsMobile does not support Inbound API Calls.');
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param $raw
     *
     * @return IncomingMessage|void
     *
     * @throws \RuntimeException
     */
    public function receive($raw)
    {
        throw new \RuntimeException('LabsMobile does not support Inbound API Calls.');
    }
}
