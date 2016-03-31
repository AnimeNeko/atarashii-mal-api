<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Helper\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatTime()
    {
        $date = new Date();

        //Grab parts of the current date for use below
        $currentDate = new \DateTime('now');
        $currentYear = $currentDate->format('Y');

        // m-d-y, g:i A
        $inputDate = '3-14-97, 8:15 PM';
        $expected = '1997-03-14T20:15-0800';
        $this->assertEquals($expected, $date::formatTime($inputDate));

        // M j, g:i A
        $inputDate = 'Apr 9, 8:15 PM';
        $expected = $currentYear.'-04-09T20:15-0700';
        $this->assertEquals($expected, $date::formatTime($inputDate));

        // M j, Y g:i A
        $inputDate = 'Sep 30, 2014 6:54 AM';
        $expected = '2014-09-30T06:54-0700';

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Now
        $inputDate = 'Now';
        $expected = $currentDate->format(DATE_ISO8601);

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Seconds Ago
        $inputDate = '15 seconds ago';
        $expected = clone $currentDate;
        $expected = $expected->modify('-15 seconds')->format(DATE_ISO8601);

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Minutes Ago
        $inputDate = '26 minutes ago';
        $expected = clone $currentDate;
        $expected = $expected->modify('-26 minutes')->format('Y-m-d\TH:iO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Minute Ago
        $inputDate = '1 minute ago';
        $expected = clone $currentDate;
        $expected = $expected->modify('-1 minute')->format('Y-m-d\TH:iO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Hours ago
        $inputDate = '35 hours ago';
        $expected = clone $currentDate;
        $expected = $expected->modify('-35 hours')->format('Y-m-d\THO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Hour ago
        $inputDate = '1 hour ago';
        $expected = clone $currentDate;
        $expected = $expected->modify('-1 hour')->format('Y-m-d\THO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Today
        $inputDate = 'Today, 1:04 AM';
        $expected = clone $currentDate;
        $expected = $expected->setTime(1, 04)->format('Y-m-d\TH:iO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // Yesterday
        $inputDate = 'Yesterday, 11:34 PM';
        $expected = clone $currentDate;
        $expected = $expected->modify('-1 day')->setTime(23, 34)->format('Y-m-d\TH:iO');

        $this->assertEquals($expected, $date::formatTime($inputDate));

        // F d, Y
        $inputDate = 'March 5, 2012';
        $expected = '2012-03-05';
        $this->assertEquals($expected, $date::formatTime($inputDate));

        // M j, Y
        $inputDate = 'Mar 5, 2012';
        $expected = '2012-03-05';
        $this->assertEquals($expected, $date::formatTime($inputDate));

        // M Y
        $inputDate = 'Sept 2012';
        $expected = '2012-09';
        $this->assertEquals($expected, $date::formatTime($inputDate));

        // M Y
        $inputDate = 'blahblahblah';
        $expected = null;
        $this->assertEquals($expected, $date::formatTime($inputDate));
    }

    public function testTZ()
    {
        $editProfileContents = file_get_contents(__DIR__.'/../InputSamples/editprofile.html');
        Date::setTimeZone($editProfileContents);

        $expected = 'Asia/Tokyo';
        $this->assertEquals($expected, Date::$timeZone);
    }

    protected function setUp()
    {
        Date::$timeZone = 'America/Los_Angeles';
    }

    protected function tearDown()
    {
        Date::$timeZone = 'America/Los_Angeles';
    }

    public static function setUpBeforeClass()
    {
        //Our date tests assume we're in the default timezone for MAL
        //Set the PHP timezone to America/Los_Angeles to be safe.
        date_default_timezone_set('America/Los_Angeles');
    }
}
