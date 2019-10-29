<?php

namespace avadim\Chrono;

include_once __DIR__ . '/../src/autoload.php';

use PHPUnit\Framework\TestCase;

class TestChrono extends TestCase
{
    public function textsDataProvider()
    {
        return [
            [
                '<b>текст текст текст</b>',
                '<b>текст текст текст</b>'
            ],
            [
                '<b>текст <b>текст</b> текст</b>',
                '<b>текст <b>текст</b> текст</b>'
            ],
        ];
    }

    /**
     */
    public function testChrono()
    {
        $oInterval1 = Chrono::createInterval(100);
        $oInterval2 = Chrono::createInterval('P1Y2s');
        $oNow = Chrono::now('Europe/Amsterdam');
        $oToday = Chrono::today('Europe/Amsterdam');
        $oDate1 = Chrono::createFrom(2000, 1, 1, 1, 2, 3, 'Europe/Amsterdam');
        $oDate2 = Chrono::createFrom(2000, 1, 1, 1, 2, 3);
        $oDate3 = Chrono::createFrom($oNow->getYear(), $oNow->getMonth(), $oNow->getDay(), $oNow->getHours(), $oNow->getMinutes(), $oNow->getSeconds(), $oNow->getTimezone());

        $this->assertTrue(Chrono::totalSeconds($oNow) >= Chrono::totalSeconds($oToday));
        $this->assertTrue(Chrono::between($oToday, $oToday, $oNow, true));
        $this->assertTrue(Chrono::compare($oToday, '<=', $oNow));
        $this->assertEquals(strcmp($oDate1->getTimeZoneNum(), $oDate2->getTimeZoneNum()), Chrono::compareWidth($oDate1, $oDate2));
        $this->assertSame($oNow->getTime(), $oDate3->getTime());

        $this->assertSame(Chrono::dateDiffSeconds($oNow, Chrono::dateAdd($oNow, $oInterval1)), 100);
        $this->assertSame(Chrono::dateDiffMinutes($oNow, Chrono::dateSub($oNow, 'PT120s')), -2);
        $this->assertEquals($oInterval2->totalDays($oDate1), $oDate1->isLeapYear() ? 366 : 365);
        $this->assertCount($oNow->getYear() - 1999 + 1, Chrono::createPeriod(Chrono::createDate(1999), 'now')->sequenceOfYears());
        $this->assertInstanceOf(\avadim\Chrono\DateTime::class, $oNow);
    }

}