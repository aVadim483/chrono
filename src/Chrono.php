<?php
/**
 * This file is part of the avadim\Chrono package
 * https://github.com/aVadim483/Chrono
 */

namespace avadim\Chrono;

/**
 * Class Chrono
 *
 * @package avadim\Chrono
 */
class Chrono
{
    public static $defaultFormat = 'Y-m-d H:i:s';

    /**
     * Usage:
     *      createInterval(100) -- Interval is 100 seconds
     *      createInterval('PT100S') -- The same as above
     *      createInterval('P1M20DT3H15M') -- One month 20 days 3 hours 15 minutes
     *      createInterval('2 weeks')
     *      createInterval('1 day + 12 hours')
     *
     * @param mixed $interval
     * @param string $baseDate
     *
     * @return DateTimeInterval
     *
     * @throws \Exception
     */
    public static function createInterval($interval, $baseDate = null)
    {
        if ($interval instanceof DateTimeInterval) {
            $interval = clone $interval;
            $interval->setBaseDateTime($baseDate);
        }
        return new DateTimeInterval($interval, $baseDate);
    }

    /**
     * @see https://www.php.net/manual/en/datetime.formats.php
     *
     * @param mixed $dateTime
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createDate($dateTime = 'now', $timeZone = null)
    {
        if (!($dateTime instanceof DateTime)) {
            if ($dateTime instanceof \DateTime) {
                $strDateTime = $dateTime->format('Y-m-d H:i:s.u O');
            } elseif (!is_numeric($dateTime)) {
                $strDateTime = (string)$dateTime;
            } else {
                $strDateTime = $dateTime;
            }
            $resultDateTime = new DateTime($strDateTime);
        } else {
            $resultDateTime = clone $dateTime;
        }
        $resultDateTime->setDefaultFormat(self::$defaultFormat);
        if (null !== $timeZone) {
            $resultDateTime->setTimezone(DateTimeZone::create($timeZone));
        }

        return $resultDateTime;
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function make($year, $month = 1, $day = 1, $hours = 0, $minutes = 0, $seconds = 0, $timeZone = null)
    {
        $strDate = sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hours, $minutes, $seconds);
        $now = static::now($timeZone);
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $strDate, $timeZone ?? date_default_timezone_get());
        $dateTime->setDefaultFormat($now->getDefaultFormat());

        return $dateTime;
    }

    /**
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function now($timeZone = null)
    {
        return self::createDate('now', $timeZone);
    }

    /**
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function today($timeZone = null)
    {
        return self::createFrom(null, null, null, 0, 0, 0, $timeZone);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFrom($year, $month = null, $day = null, $hours = null, $minutes = null, $seconds = null, $timeZone = null)
    {
        if (func_num_args() < 7) {
            $args = func_get_args();
            $last = end($args);
            if (!is_numeric($last)) {
                $timeZone = array_pop($args);
            } else {
                $timeZone = date_default_timezone_get();
            }
            if (count($args) < 6) {
                $args = array_merge($args, array_fill(0, 6 - count($args), null));
            }
            list($year, $month, $day, $hours, $minutes, $seconds) = $args;
        }
        $nate = static::now($timeZone);
        $strDate = sprintf(
            '%s-%s-%s %s:%02s:%02s',
            (null !== $year) ? $year : $nate->getYear(),
            (null !== $month) ? $month : $nate->getMonth(),
            (null !== $day) ? $day : $nate->getDay(),
            (null !== $hours) ? $hours : $nate->getHours(),
            (null !== $minutes) ? $minutes : $nate->getMinutes(),
            (null !== $seconds) ? $seconds : $nate->getSeconds()
        );

        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $strDate, $nate->getTimezone());
        $dateTime->setDefaultFormat($nate->getDefaultFormat());

        return $dateTime;
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromDate($year, $month = null, $day = null, $timeZone = null)
    {
        return static::createFrom($year, $month, $day, null, null, null, $timeZone);
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @param \DateTimeZone|string $timeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromTime($hours = null, $minutes = null, $seconds = null, $timeZone = null)
    {
        return static::createFrom(null, null, null, $hours, $minutes, $seconds, $timeZone);
    }

    /**
     * @param mixed $date1
     * @param mixed $date2
     *
     * @return DateTimePeriod
     *
     * @throws \Exception
     */
    public static function createPeriod($date1, $date2)
    {
        if (!is_object($date1) || !($date1 instanceof DateTime)) {
            $date1 = self::createDate($date1);
        }
        if (!is_object($date2) || !($date2 instanceof DateTime)) {
            $date2 = self::createDate($date2);
        }

        return new DateTimePeriod($date1, $date2);
    }

    /**
     * @param string $method
     * @param string $strInterval
     * @param string $baseDate
     *
     * @return float
     *
     * @throws \Exception
     */
    protected static function _calcTotal($method, $strInterval, $baseDate = null)
    {
        if (is_numeric($strInterval)) {
            return (float)$strInterval;
        }
        if (!is_string($strInterval)) {
            return null;
        }

        $interval = static::createInterval($strInterval, $baseDate);

        return $interval->$method();
    }

    /**
     * Converts an string interval to a number of seconds
     *
     * @param string $strInterval - Interval in ISO 8601 specification or human relative formats
     * @param string $baseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalSeconds($strInterval, $baseDate = null)
    {
        return static::_calcTotal('totalSeconds', $strInterval, $baseDate);
    }

    /**
     * Converts an string interval to a number of minutes
     *
     * @param string $strInterval  - Interval in ISO 8601 specification or human relative formats
     * @param string $baseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalMinutes($strInterval, $baseDate = null)
    {
        return static::_calcTotal('totalMinutes', $strInterval, $baseDate);
    }

    /**
     * Converts an string interval to a number of hours
     *
     * @param string $strInterval  - Interval in ISO 8601 specification or human relative formats
     * @param string $baseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalHours($strInterval, $baseDate = null)
    {
        return static::_calcTotal('totalHours', $strInterval, $baseDate);
    }

    /**
     * Converts an string interval to a number of days
     *
     * @param string $strInterval  - Interval in ISO 8601 specification or human relative formats
     * @param string $baseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalDays($strInterval, $baseDate = null)
    {
        return static::_calcTotal('totalDays', $strInterval, $baseDate);
    }

    /**
     * @param string $date
     * @param string $strInterval
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function dateAdd($date, $strInterval)
    {
        $dateTime = static::createDate($date);
        $oInterval = static::createInterval($strInterval);
        $dateTime->add($oInterval->interval());

        return $dateTime;
    }

    /**
     * @param string $date
     * @param string $strInterval
     * @param string $format
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function dateAddFormat($date, $strInterval, $format = null)
    {
        $dateTime = static::dateAdd($date, $strInterval);
        if (null !== $format) {
            return $dateTime->format($format);
        }
        return (string)$dateTime;
    }

    /**
     * @param string $date
     * @param string $strInterval
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function dateSub($date, $strInterval)
    {
        $dateTime = static::createDate($date);
        $interval = static::createInterval($strInterval);
        $dateTime->sub($interval->interval());

        return $dateTime;
    }

    /**
     * @param string $date
     * @param string $strInterval
     * @param string $format
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function dateSubFormat($date, $strInterval, $format = null)
    {
        $dateTime = static::dateSub($date, $strInterval);
        if (null !== $format) {
            return $dateTime->format($format);
        }
        return (string)$dateTime;
    }

    /**
     * Calculates the difference between two dates in seconds
     *
     * @param string $date1
     * @param string $date2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffSeconds($date1, $date2)
    {
        $dateTime1 = static::createDate($date1);
        $dateTime2 = static::createDate($date2);

        return $dateTime2->getTimestamp() - $dateTime1->getTimestamp();
    }

    /**
     * Calculates the difference between two dates in minutes
     *
     * @param string $date1
     * @param string $date2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffMinutes($date1, $date2)
    {
        $dateTime1 = static::createDate($date1);
        $dateTime2 = static::createDate($date2);

        return (int)(($dateTime2->getTimestamp() - $dateTime1->getTimestamp()) / DateTimeInterval::PT1M);
    }

    /**
     * Calculates the difference between two dates in hours
     *
     * @param string $date1
     * @param string $date2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffHours($date1, $date2)
    {
        $dateTime1 = static::createDate($date1);
        $dateTime2 = static::createDate($date2);

        return (int)(($dateTime2->getTimestamp() - $dateTime1->getTimestamp()) / DateTimeInterval::PT1H);
    }

    /**
     * Calculates the difference between two dates in days
     *
     * @param string $date1
     * @param string $date2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffDays($date1, $date2)
    {
        $dateTime1 = static::createDate($date1);
        $dateTime2 = static::createDate($date2);

        return (int)(($dateTime2->getTimestamp() - $dateTime1->getTimestamp()) / DateTimeInterval::P1D);
    }

    /**
     * @param $dateTime1
     * @param $dateTime2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffMonths($dateTime1, $dateTime2)
    {
        if (!($dateTime1 instanceof DateTime)) {
            $dateTime1 = static::createDate($dateTime1);
        }
        if (!($dateTime2 instanceof DateTime)) {
            $dateTime2 = static::createDate($dateTime2);
        }

        if ($dateTime1->getTimestamp() > $dateTime2->getTimestamp()) {
            $negative = -1;
            $checkDate1 = clone $dateTime2;
            $checkDate2 = clone $dateTime1;
        } else {
            $negative = 1;
            $checkDate1 = clone $dateTime1;
            $checkDate2 = clone $dateTime2;
        }
        $years = self::dateDiffYears($checkDate1, $checkDate2);
        if ($years === 0 && $checkDate1->getYear() !== $checkDate2->getYear()) {
            $months = 12;
        } else {
            $months = $years * 12;
        }
        $months += $checkDate2->getMonth() - $checkDate1->getMonth();

        return $months * $negative;

    }

    /**
     * @param $dateTime1
     * @param $dateTime2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffYears($dateTime1, $dateTime2)
    {
        if (!($dateTime1 instanceof DateTime)) {
            $dateTime1 = static::createDate($dateTime1);
        }
        if (!($dateTime2 instanceof DateTime)) {
            $dateTime2 = static::createDate($dateTime2);
        }
        if ($dateTime2->getTimestamp() === $dateTime1->getTimestamp()) {
            return 0;
        }
        if ($dateTime1->getTimestamp() > $dateTime2->getTimestamp()) {
            $negative = true;
            $checkDate1 = clone $dateTime2;
            $checkDate2 = clone $dateTime1;
        } else {
            $negative = false;
            $checkDate1 = clone $dateTime1;
            $checkDate2 = clone $dateTime2;
        }

        $result = $checkDate2->getYear() - $checkDate1->getYear();
        if ($checkDate1->setYear($checkDate2->getYear())->getTimestamp() > $checkDate2->getTimestamp()) {
            --$result;
        }
        return $negative ? -$result : $result;
    }

    /**
     * Compare two dates
     *
     * @param $dateTime1
     * @param $operator
     * @param $dateTime2
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function compare($dateTime1, $operator, $dateTime2)
    {
        if (!($dateTime1 instanceof DateTime)) {
            $dateTime1 = static::createDate($dateTime1);
        }
        if (!($dateTime2 instanceof DateTime)) {
            $dateTime2 = static::createDate($dateTime2);
        }

        return $dateTime1->compare($operator, $dateTime2);
    }

    /**
     * Compares two dates and returns -1, 0 or 1
     *
     * @param $dateTime1
     * @param $dateTime2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function compareWith($dateTime1, $dateTime2)
    {
        if (!($dateTime1 instanceof DateTime)) {
            $dateTime1 = static::createDate($dateTime1);
        }
        if (!($dateTime2 instanceof DateTime)) {
            $dateTime2 = static::createDate($dateTime2);
        }

        return $dateTime1->compareWith($dateTime2);
    }

    /**
     * Determines if the passed date is between two other dates
     *
     * @param mixed $comparedDate
     * @param mixed $minDate
     * @param mixed $maxDate
     * @param bool $include Including boundary dates
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function between($comparedDate, $minDate, $maxDate, $include = true)
    {
        if (!($comparedDate instanceof DateTime)) {
            $comparedDate = static::createDate($comparedDate);
        }
        if (!($minDate instanceof DateTime)) {
            $minDate = static::createDate($minDate);
        }
        if (!($maxDate instanceof DateTime)) {
            $maxDate = static::createDate($maxDate);
        }

        return $comparedDate->between($minDate, $maxDate, $include);
    }

}

// EOF