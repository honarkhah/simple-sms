<?php

namespace Wideas\SMS;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use Wideas\SMS\Drivers\EmailSMS;
use Wideas\SMS\Drivers\KavenegarSMS;
use Wideas\SMS\Drivers\MelipayamakSMS;
use Wideas\SMS\Drivers\NexmoSMS;
use Wideas\SMS\Drivers\MozeoSMS;
use Wideas\SMS\Drivers\Sms5m5SMS;
use Wideas\SMS\Drivers\TwilioSMS;
use Wideas\SMS\Drivers\ZenviaSMS;
use Wideas\SMS\Drivers\CallFireSMS;
use Wideas\SMS\Drivers\EZTextingSMS;
use Wideas\SMS\Drivers\LabsMobileSMS;

class DriverManager extends Manager
{
    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.driver'];
    }

    /**
     * Set the default sms driver name.
     *
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['sms.driver'] = $name;
    }

    /**
     * Create an instance of the Twillo driver.
     *
     * @return TwilioSMS
     */
    protected function createTwilioDriver()
    {
        $config = $this->app['config']->get('sms.twilio', []);

        return new TwilioSMS(
            new \Services_Twilio($config['account_sid'], $config['auth_token']),
            $config['auth_token'],
            $this->app['request']->url(),
            $config['verify']
        );
    }

    /**
     * Create an instance of the MelipayamakSMS driver.
     *
     * @return MelipayamakSMS
     */
    protected function createMelipayamakDriver()
    {
        $config = $this->app['config']->get('sms.melipayamak', []);

        $provider = new MelipayamakSMS(
            new Client(),
            $config['username'],
            $config['password'],
            $config['lineNumbers']
        );

        return $provider;
    }
    /**
     * Create an instance of the MelipayamakSMS driver.
     *
     * @return MelipayamakSMS
     */
    protected function createKavenegarDriver()
    {
        $provider = new KavenegarSMS(
            new Client(),
            true
        );
        return $provider;
    }

    protected function createSms5m5Driver()
    {
        $config = $this->app['config']->get('sms.sms5m5', []);

        $provider = new Sms5m5SMS(
            $config['username'],
            $config['password'],
            $config['lineNumbers']
        );

        return $provider;
    }
}
