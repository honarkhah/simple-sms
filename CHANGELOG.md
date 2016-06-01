Laravel SMS iran providers
==========================

##Change Log

#### 1.0.0 
* An `outgoing message` is now returned when a message is sent.
* Full Laravel 5.X support.
* Updated to Guzzle 6.
* Dropped support for PHP 5.4
* Added the ability to send SMS messages without a view.
* Added MMS support for Twilio.
* General comment and code clean up.
* Expanded documentation.
* Added error detection on API calls.
* Push SMS messages now work with Twilio.
* `SMS::queue` now works.
* Added [Melipayamak Driver](http://melipayamak.ir/)
* Fixed a bug where the `pretend` configuration variable was not working.
* `SMS::pretend()` now works as documented.
