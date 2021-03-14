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
    protected $defaultFormat = 'Y-m-d H:i:s.u O';

    /**
     * DateTime constructor
     *
     * @param string $dateTime
     * @param mixed|null $dateTimeZone
     *
     * @throws \Exception
     */
    public function __construct($dateTime = 'now', $dateTimeZone = null)
    {
        parent::__construct($dateTime, DateTimeZone::create($dateTimeZone));
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
        return $this->format($this->defaultFormat);
    }

    /**
     * Format date using strftime() function
     *
     * @param string $format
     *
     * @return string
     */
    public function strFormat($format)
    {
        $date = strftime($format, $this->getTimestamp());
        if (false !== strpos($format, '%q')) {
            $date = str_replace('%q', $this->getQuarter(), $date);
        }

        return $date;
    }

    /**
     * Returns date as string with second precision
     *
     * @return string
     */
    public function strSecond()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Returns date as string with minute precision
     *
     * @return string
     */
    public function strMinute()
    {
        return $this->format('Y-m-d H:i');
    }

    /**
     * Returns date as string with hour precision
     *
     * @return string
     */
    public function strHour()
    {
        return $this->format('Y-m-d H');
    }

    /**
     * Returns date as string with day precision
     *
     * @return string
     */
    public function strDay()
    {
        return $this->format('Y-m-d');
    }

    /**
     * Returns date as string with week precision as <year><delimiter><week>
     *
     * @param string $delimiter
     *
     * @return string
     */
    public function strWeek($delimiter = 'W')
    {
        return $this->strFormat('%Y' . $delimiter . '%V');
    }

    /**
     * @param string $delimiter
     *
     * @return string
     */
    public function strMonth($delimiter = '-')
    {
        return $this->strFormat('%Y' . $delimiter . '%m');
    }

    /**
     * @param string $delimiter
     *
     * @return string
     */
    public function strQuarter($delimiter = 'Q')
    {
        return (string)$this->getYear() . $delimiter . $this->getQuarter();
    }

    /**
     * @return string
     */
    public function strYear()
    {
        return (string)$this->getYear();
    }

    /**
     * Parses a time string according to a specified format
     *
     * @param string $format
     * @param string $dateTime
     * @param mixed|null $dateTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromFormat($format, $dateTime, $dateTimeZone = null)
    {
        $objDateTime = parent::createFromFormat($format, $dateTime, DateTimeZone::create($dateTimeZone));

        return new static($objDateTime->format('Y-m-d H:i:s'), $objDateTime->getTimezone());
    }

    /**
     * @param $format
     *
     * @return $this
     */
    public function setDefaultFormat($format)
    {
        $this->defaultFormat = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function setYear($year)
    {
        $this->setDate($year, $this->getMonth(), $this->getDay());

        return $this;
    }

    /**
     * @param int $months
     *
     * @return $this
     */
    public function setMonth($month)
    {
        $this->setDate($this->getYear(), $month, $this->getDay());

        return $this;
    }

    /**
     * @param int $day
     *
     * @return $this
     */
    public function setDay($day)
    {
        $this->setDate($this->getYear(), $this->getMonth(), $day);

        return $this;
    }

    /**
     * @param int $hours
     *
     * @return $this
     */
    public function setHours($hours)
    {
        $this->setTime($hours, $this->getMinutes(), $this->getSeconds());

        return $this;
    }

    /**
     * @param int $minutes
     *
     * @return $this
     */
    public function setMinutes($minutes)
    {
        $this->setTime($this->getHours(), $minutes, $this->getSeconds());

        return $this;
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function setSeconds($seconds)
    {
        $this->setTime($this->getHours(), $this->getMinutes(), $seconds);

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
        $seconds = $this->getOffset();
        $hours = (int)floor($seconds / DateTimeInterval::PT1H);
        $minutes = $seconds - $hours * DateTimeInterval::PT1H;

        return $hours . (($minutes < 10) ? '0' . $minutes : $minutes);
    }

    /**
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        return DateTimeZone::create(parent::getTimeZone());
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
     * @param int|string $intervalValue
     * @param string $intervalName
     *
     * @return $this
     */
    private function addInterval($intervalValue, $intervalName = null)
    {
        $interval = $intervalValue;
        if ($intervalName) {
            $interval .= ' ' . $intervalName;
        }

        return $this->add(\DateInterval::createFromDateString($interval));
    }

    /**
     * @param int|string $intervalValue
     * @param string $intervalName
     *
     * @return $this
     */
    private function subInterval($intervalValue, $intervalName = null)
    {
        if (null === $intervalName) {
            if (0 === strpos($intervalValue, 'PT')) {
                $intervalValue = substr($intervalValue, 2, -1);
            } elseif (0 === strpos($intervalValue, 'P')) {
                $intervalValue = substr($intervalValue, 1, -1);
            }
            $intervalName = substr($intervalValue, -1);
        } else {
            $intervalValue = -(int)$intervalValue;
        }

        return $this->addInterval($intervalValue, $intervalName);
    }

    /**
     * @param int $years
     *
     * @return $this
     */
    public function addYears($years)
    {
        return $this->addInterval($years, 'years');
    }

    /**
     * @param int $years
     *
     * @return $this
     */
    public function subYears($years)
    {
        return $this->addYears(-$years);
    }

    /**
     * @param int $months
     *
     * @return $this
     */
    public function addMonths($months)
    {
        return $this->addInterval($months, 'months');
    }

    /**
     * @param int $months
     *
     * @return $this
     */
    public function subMonths($months)
    {
        return $this->addMonths(-$months);
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function addDays($days)
    {
        return $this->addInterval($days, 'days');
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function subDays($days)
    {
        return $this->addDays(-$days);
    }

    /**
     * @param $hours
     *
     * @return $this
     */
    public function addHours($hours)
    {
        return $this->addInterval($hours, 'hours');
    }

    /**
     * @param int $hours
     *
     * @return $this
     */
    public function subHours($hours)
    {
        return $this->addHours(-$hours);
    }

    /**
     * @param $minutes
     *
     * @return $this
     */
    public function addMinutes($minutes)
    {
        return $this->addInterval($minutes, 'minutes');
    }

    /**
     * @param int $minutes
     *
     * @return $this
     */
    public function subMinutes($minutes)
    {
        return $this->addMinutes(-$minutes);
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function addSeconds($seconds)
    {
        return $this->addInterval($seconds, 'seconds');
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function subSeconds($seconds)
    {
        return $this->addSeconds(-$seconds);
    }

    /**
     * @param string   $operator
     * @param DateTime $dateTime
     *
     * @return bool
     */
    public function compare($operator, $dateTime)
    {
        $timestamp1 = $this->getTimestamp();
        $timestamp2 = $dateTime->getTimestamp();
        switch ($operator) {
            case '<':
            case 'lt':
                return $timestamp1 < $timestamp2;
            case '<=':
            case 'le':
            case 'lte':
                return $timestamp1 <= $timestamp2;
            case '=':
            case '==':
            case 'eq':
                return $timestamp1 === $timestamp2;
            case '!=':
            case '<>':
            case 'ne':
                return $timestamp1 !== $timestamp2;
            case '>=':
            case 'gte':
            case 'ge':
                return $timestamp1 >= $timestamp2;
            case '>':
            case 'gt':
                return $timestamp1 > $timestamp2;
        }
        return false;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return int
     */
    public function compareWith($dateTime)
    {
        $timestamp1 = $this->getTimestamp();
        $timestamp2 = $dateTime->getTimestamp();
        if ($timestamp1 < $timestamp2) {
            return 1;
        }
        if ($timestamp1 > $timestamp2) {
            return -1;
        }
        return 0;
    }

    /**
     * between($date1, $date2) - including both dates
     * between($date1, $date2, false) - excluding both dates
     * between($date1, $date2, true, false) - including 1st date and excluding 2nd date
     * between($date1, $date2, false, true) - excluding 1st date and including 2nd date
     *
     * @param DateTime $minDateTime
     * @param DateTime $maxDateTime
     * @param bool $include1
     * @param bool $include2
     *
     * @return bool
     */
    public function between($minDateTime, $maxDateTime, $include1 = true, $include2 = null)
    {
        if (null === $include2) {
            $include2 = $include1;
        }
        if ($include1) {
            $result = $this->compare('>=', $minDateTime);
        } else {
            $result = $this->compare('>', $minDateTime);
        }
        if ($include2) {
            $result = $result && $this->compare('<=', $maxDateTime);
        } else {
            $result = $result && $this->compare('<', $maxDateTime);
        }
        return $result;
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