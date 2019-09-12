<?php
/**
 * This file is part of the avadim\Chrono package
 * https://github.com/aVadim483/Chrono
 */

namespace avadim\Chrono;

/**
 * Class DateTime
 *
 * @package avadim\Chrono
 */
class DateTime extends \DateTime
{
    protected $sDefaultFormat = 'Y-m-d H:i:s.u O';

    /**
     * DateTime constructor
     *
     * @param string $sDateTime
     * @param null $xDateTimeZone
     *
     * @throws \Exception
     */
    public function __construct($sDateTime = 'now', $xDateTimeZone = null)
    {
        parent::__construct($sDateTime, DateTimeZone::create($xDateTimeZone));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->format($this->sDefaultFormat);
    }

    /**
     * Format date using strftime() function
     *
     * @param string $sFormat
     *
     * @return string
     */
    public function strFormat($sFormat)
    {
        $sDate = strftime($sFormat, $this->getTimestamp());
        if (false !== strpos($sFormat, '%q')) {
            $sDate = str_replace('%q', $this->getQuarter(), $sDate);
        }
        return $sDate;
    }

    /**
     * @return string
     */
    public function strSecond()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function strMinute()
    {
        return $this->format('Y-m-d H:i');
    }

    /**
     * @return string
     */
    public function strHour()
    {
        return $this->format('Y-m-d H');
    }

    /**
     * @return string
     */
    public function strDay()
    {
        return $this->format('Y-m-d');
    }

    /**
     * @param string $sDelimiter
     *
     * @return string
     */
    public function strWeek($sDelimiter = 'W')
    {
        return $this->strFormat('%Y' . $sDelimiter . '%V');
    }

    /**
     * @param string $sDelimiter
     *
     * @return string
     */
    public function strMonth($sDelimiter = '-')
    {
        return $this->strFormat('%Y' . $sDelimiter . '%m');
    }

    /**
     * @param string $sDelimiter
     *
     * @return string
     */
    public function strQuarter($sDelimiter = 'Q')
    {
        return (string)$this->getYear() . $sDelimiter . $this->getQuarter();
    }

    /**
     * @return string
     */
    public function strYear()
    {
        return (string)$this->getYear();
    }

    /**
     * @param string $sFormat
     * @param string $sDateTime
     * @param mixed $xDateTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromFormat($sFormat, $sDateTime, $xDateTimeZone = null)
    {
        $oDateTime = parent::createFromFormat($sFormat, $sDateTime, DateTimeZone::create($xDateTimeZone));

        return new static($oDateTime->format('Y-m-d H:i:s'), $oDateTime->getTimezone());
    }

    /**
     * @param $sFormat
     *
     * @return $this
     */
    public function setDefaultFormat($sFormat)
    {
        $this->sDefaultFormat = $sFormat;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuarter()
    {
        return floor(($this->getMonth() - 1) / 3) + 1;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return (int)$this->format('Y');
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return (int)$this->format('m');
    }

    /**
     * @return int
     */
    public function getWeek()
    {
        return (int)$this->format('W');
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return (int)$this->format('d');
    }

    /**
     * @return int
     */
    public function getHours()
    {
        return (int)$this->format('H');
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        return (int)$this->format('i');
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return (int)$this->format('s');
    }

    /**
     * @return string
     */
    public function getTimeZoneNum()
    {
        $iSeconds = $this->getOffset();
        $iHours = (int)floor($iSeconds / DateTimeInterval::PT1H);
        $iMinutes = $iSeconds - $iHours * DateTimeInterval::PT1H;

        return $iHours . (($iMinutes < 10) ? '0' . $iMinutes : $iMinutes);
    }

    /**
     * Returns local time (timestamp)
     *
     * @return int
     */
    public function getTime()
    {
        return $this->getTimestamp() + $this->getOffset();
    }

    /**
     * @param mixed $xInterval
     * @param string $sIntervalName
     *
     * @return $this
     */
    private function addInterval($xInterval, $sIntervalName = null)
    {
        $sInterval = $xInterval;
        if ($sIntervalName) {
            $sInterval .= ' ' . $sIntervalName;
        }
        /*
        if ($sIntervalName === 'months') {
            $i = \DateInterval::createFromDateString($sInterval);
            $i = new \DateInterval('P3M');
            $this->sub($i);
            return $this;
        }
        */
        return $this->add(\DateInterval::createFromDateString($sInterval));
    }

    /**
     * @param mixed $xInterval
     * @param string $sIntervalSymbol
     *
     * @return $this
     */
    private function subInterval($xInterval, $sIntervalSymbol = null)
    {
        if (null === $sIntervalSymbol) {
            if (0 === strpos($xInterval, 'PT')) {
                $xInterval = substr($xInterval, 2, -1);
            } elseif (0 === strpos($xInterval, 'P')) {
                $xInterval = substr($xInterval, 1, -1);
            }
            $sIntervalSymbol = substr($xInterval, -1);
        } else {
            $xInterval = -(int)$xInterval;
        }
        return $this->addInterval($xInterval, $sIntervalSymbol);
    }

    /**
     * @param int $iYears
     *
     * @return $this
     */
    public function addYears($iYears)
    {
        return $this->addInterval($iYears, 'years');
    }

    /**
     * @param int $iYears
     *
     * @return $this
     */
    public function subYears($iYears)
    {
        return $this->addYears(-$iYears);
    }

    /**
     * @param int $iMonths
     *
     * @return $this
     */
    public function addMonths($iMonths)
    {
        return $this->addInterval($iMonths, 'months');
    }

    /**
     * @param int $iMonths
     *
     * @return $this
     */
    public function subMonths($iMonths)
    {
        return $this->addMonths(-$iMonths);
    }

    /**
     * @param int $iDays
     *
     * @return $this
     */
    public function addDays($iDays)
    {
        return $this->addInterval($iDays, 'days');
    }

    /**
     * @param int $iDays
     *
     * @return $this
     */
    public function subDays($iDays)
    {
        return $this->addDays(-$iDays);
    }

    /**
     * @param $iHours
     *
     * @return $this
     */
    public function addHours($iHours)
    {
        return $this->addInterval($iHours, 'hours');
    }

    /**
     * @param int $iHours
     *
     * @return $this
     */
    public function subHours($iHours)
    {
        return $this->addHours(-$iHours);
    }

    /**
     * @param int $iHour
     *
     * @return $this
     */
    public function setHours($iHour)
    {
        return $this->addHours($iHour - $this->getHours());
    }

    /**
     * @param $iMinutes
     *
     * @return $this
     */
    public function addMinutes($iMinutes)
    {
        return $this->addInterval($iMinutes, 'minutes');
    }

    /**
     * @param int $iMinutes
     *
     * @return $this
     */
    public function subMinutes($iMinutes)
    {
        return $this->addMinutes(-$iMinutes);
    }

    /**
     * @param int $iMinutes
     *
     * @return $this
     */
    public function setMinutes($iMinutes)
    {
        return $this->addMinutes($iMinutes - $this->getMinutes());
    }

    /**
     * @param int $iSeconds
     *
     * @return $this
     */
    public function addSeconds($iSeconds)
    {
        return $this->addInterval($iSeconds, 'seconds');
    }

    /**
     * @param int $iSeconds
     *
     * @return $this
     */
    public function subSeconds($iSeconds)
    {
        return $this->addSeconds(-$iSeconds);
    }

    /**
     * @param int $iSecond
     *
     * @return $this
     */
    public function setSeconds($iSecond)
    {
        return $this->addSeconds($iSecond - $this->getSeconds());
    }

    /**
     * @param string   $sOperator
     * @param DateTime $oDate
     *
     * @return bool
     */
    public function compare($sOperator, $oDate)
    {
        $iTime1 = $this->getTimestamp();
        $iTime2 = $oDate->getTimestamp();
        switch ($sOperator) {
            case '<':
            case 'lt':
                return $iTime1 < $iTime2;
            case '<=':
            case 'le':
            case 'lte':
                return $iTime1 <= $iTime2;
            case '=':
            case '==':
            case 'eq':
                return $iTime1 === $iTime2;
            case '!=':
            case '<>':
            case 'ne':
                return $iTime1 !== $iTime2;
            case '>=':
            case 'gte':
            case 'ge':
                return $iTime1 >= $iTime2;
            case '>':
            case 'gt':
                return $iTime1 > $iTime2;
        }
        return false;
    }

    /**
     * @param DateTime $oDate
     *
     * @return int
     */
    public function compareWidth($oDate)
    {
        $iTime1 = $this->getTimestamp();
        $iTime2 = $oDate->getTimestamp();
        if ($iTime1 < $iTime2) {
            return 1;
        }
        if ($iTime1 > $iTime2) {
            return -1;
        }
        return 0;
    }

    /**
     * @param $oDate1
     * @param $oDate2
     * @param bool $bInclude1
     * @param bool $bInclude2
     *
     * @return bool
     */
    public function between($oDate1, $oDate2, $bInclude1 = true, $bInclude2 = null)
    {
        if (null === $bInclude2) {
            $bInclude2 = $bInclude1;
        }
        if ($bInclude1) {
            $bResult = $this->compare('>=', $oDate1);
        } else {
            $bResult = $this->compare('>', $oDate1);
        }
        if ($bInclude2) {
            $bResult = $bResult && $this->compare('<=', $oDate2);
        } else {
            $bResult = $bResult && $this->compare('<', $oDate2);
        }
        return $bResult;
    }

    /**
     * @return bool
     */
    public function isLeapYear()
    {
        return (bool)$this->format('L');
    }

}

// EOF

// EOF