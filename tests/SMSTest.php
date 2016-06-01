<?php

use Mockery as m;
use Wideas\SMS\SMS;
use Wideas\SMS\OutgoingMessage;
class SMSTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->sms = new SMS(m::mock('Wideas\SMS\Drivers\DriverInterface'));
        $this->message = new OutgoingMessage(m::mock('\Illuminate\View\Factory'));
    }

    public function testIsPretendingByDefault()
    {
        $this->assertFalse($this->sms->isPretending());
    }

    public function testPretendIsSet()
    {
        $this->sms->setPretending(true);

        $this->assertTrue($this->sms->isPretending());
    }
}
