<?php

namespace Wideas\SMS\Drivers;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;
use GuzzleHttp\Client;
use Wideas\SMS\IncomingMessage;
use Wideas\SMS\OutgoingMessage;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Config as Config;

class MelipayamakSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    protected $cache;

    protected $lineNumbers;

    public function setLineNumbers(array $numbers){
        $this->lineNumbers = $numbers;
    }
    public function getLineNumbers(){

        return $this->lineNumbers;
    }
    public function getOneLine(){
        return $this->lineNumbers[array_rand($this->lineNumbers,1)];

    }
    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'http://api.payamak-panel.com/post/Send.asmx?wsdl';

    CONST SMS_SERVER_ROTATOR_CACHE_KEY = 'SMS_SERVER_ROTATOR';

    public static $api_servers = array(
        'http://api.payamak-panel.com/post/Send.asmx?wsdl',
        'http://87.107.121.51/post/Send.asmx?wsdl',
        'http://87.107.121.52/post/Send.asmx?wsdl',
        //'http://87.107.121.53/post/Send.asmx?wsdl',
        'http://87.107.121.54/post/Send.asmx?wsdl',
    );
    /**
     * Constructs the MozeoSMS Instance.
     *
     * @param Client $client The guzzle client
     */
    public function __construct(Client $client, $username, $password, $lineNumbers)
    {
        $this->client = $client;
        ini_set('default_socket_timeout', 60);

        $this->cache = new Cache;
        $this->cache = $this->cache->getFacadeRoot();

        $this->settings = [
            'username' => $username,
            'password' => $password,
            'targetUsername' => $username,
        ];
        $this->lineNumbers = $lineNumbers;


        $result = false;
        if(Cache::has(self::SMS_SERVER_ROTATOR_CACHE_KEY)){
            $result = Cache::get(self::SMS_SERVER_ROTATOR_CACHE_KEY);
        }else{
            Cache::put(self::SMS_SERVER_ROTATOR_CACHE_KEY, 1, '5');
        };

        if ($result === false) {
            $this->rotate();
        }

        $this->apiBase = self::$api_servers[Cache::get(MelipayamakSMS::SMS_SERVER_ROTATOR_CACHE_KEY)];
    }

    /**
     * rotate api server
     */
    private function rotate(){

        $result = Cache::get(MelipayamakSMS::SMS_SERVER_ROTATOR_CACHE_KEY);
        $result++;
        $api_servers = self::$api_servers;
        end($api_servers);
        $key = key($api_servers);
        if($result > $key){
            $result = 0;
        }elseif($result ==false) {
            $result = 0;
        }

        Cache::put(MelipayamakSMS::SMS_SERVER_ROTATOR_CACHE_KEY, $result);
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The SMS message instance.
     */
    public function send(OutgoingMessage $message)
    {
        SoapWrapper::add(function ($service) {

            $service->name('sendSms')
                ->wsdl($this->apiBase)
                ->trace(true);
        });


        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $to) {
            $data = [
                'from' => $this->getOneLine(),
                'to' => $to,
                'text' => $composeMessage,
                'isflash' => isset($params['isflash'])?$params['isflash']:true,
            ];
            $data = array_merge($this->settings,$data);

            // Using the added service
            SoapWrapper::service('sendSms', function ($service) use ($data) {
                return $service->call('SendSimpleSMS2', [$data])->SendSimpleSMS2Result;
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
