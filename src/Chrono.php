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
    protected static $sDefaultFormat = 'Y-m-d H:i:s';

    /**
     * @param mixed $xInterval
     * @param string $sBaseDate
     *
     * @return DateTimeInterval
     *
     * @throws \Exception
     */
    public static function createInterval($xInterval, $sBaseDate = null)
    {
        if ($xInterval instanceof DateTimeInterval) {
            return $xInterval;
        }
        return new DateTimeInterval($xInterval, $sBaseDate);
    }

    /**
     * @param mixed $xDateTime
     * @param \DateTimeZone|string $xDateTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createDate($xDateTime = 'now', $xDateTimeZone = null)
    {
        if ($xDateTime instanceof \DateTime) {
            $sDateTime = $xDateTime->format('Y-m-d H:i:s.u O');
        } elseif (!is_numeric($xDateTime)) {
            $sDateTime = (string)$xDateTime;
        } else {
            $sDateTime = $xDateTime;
        }
        $oDate = new DateTime($sDateTime);
        $oDate->setDefaultFormat(self::$sDefaultFormat);
        if (null !== $xDateTimeZone) {
            $oDate->setTimezone(DateTimeZone::create($xDateTimeZone));
        }
        return $oDate;
    }

    /**
     * @param \DateTimeZone|string $xDateTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function now($xDateTimeZone = null)
    {
        return self::createDate('now', $xDateTimeZone);
    }

    /**
     * @param \DateTimeZone|string $xDateTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function today($xDateTimeZone = null)
    {
        return self::createFrom(null, null, null, 0, 0, 0, $xDateTimeZone);
    }

    /**
     * @param int|null $iYear
     * @param int|null $iMonth
     * @param int|null $iDay
     * @param int|null $iHour
     * @param int|null $iMinute
     * @param int|null $iSecond
     * @param \DateTimeZone|string $sTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFrom($iYear, $iMonth = null, $iDay = null, $iHour = null, $iMinute = null, $iSecond = null, $sTimeZone = null)
    {
        if (func_num_args() < 7) {
            $aArgs = func_get_args();
            $xLast = end($aArgs);
            if (!is_numeric($xLast)) {
                $sTimeZone = array_pop($aArgs);
            }
            if (count($aArgs) < 6) {
                $aArgs = array_merge($aArgs, array_fill(0, 6 - count($aArgs), null));
            }
            list($iYear, $iMonth, $iDay, $iHour, $iMinute, $iSecond) = $aArgs;
        }
        $oDate = static::now($sTimeZone);
        $sDateString = sprintf(
            '%s-%s-%s %s:%02s:%02s',
            (null !== $iYear) ? $iYear : $oDate->getYear(),
            (null !== $iMonth) ? $iMonth : $oDate->getMonth(),
            (null !== $iDay) ? $iDay : $oDate->getDay(),
            (null !== $iHour) ? $iHour : $oDate->getHours(),
            (null !== $iMinute) ? $iMinute : $oDate->getMinutes(),
            (null !== $iSecond) ? $iSecond : $oDate->getSeconds()
        );
        $oDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $sDateString, $oDate->getTimezone());
        $oDateTime->setDefaultFormat($oDate->getDefaultFormat());

        return $oDateTime;
    }

    /**
     * @param int|null $iYear
     * @param int|null $iMonth
     * @param int|null $iDay
     * @param \DateTimeZone|string $sTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromDate($iYear, $iMonth = null, $iDay = null, $sTimeZone = null)
    {
        return static::createFrom($iYear, $iMonth, $iDay, $iHour = null, $iMinute = null, $iSecond = null, $sTimeZone);
    }

    /**
     * @param int|null $iHour
     * @param int|null $iMinute
     * @param int|null $iSecond
     * @param \DateTimeZone|string $sTimeZone
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function createFromTime($iHour = null, $iMinute = null, $iSecond = null, $sTimeZone = null)
    {
        return static::createFrom(null, null, null, $iHour, $iMinute, $iSecond, $sTimeZone);
    }

    /**
     * @param $xDate1
     * @param $xDate2
     *
     * @return DateTimePeriod
     *
     * @throws \Exception
     */
    public static function createPeriod($xDate1, $xDate2)
    {
        $oDate1 = self::createDate($xDate1);
        $oDate2 = self::createDate($xDate2);

        return new DateTimePeriod($oDate1, $oDate2);
    }

    /**
     * @param string $sMethod
     * @param string $sInterval
     * @param string $sBaseDate
     *
     * @return float
     *
     * @throws \Exception
     */
    private static function calcTotal($sMethod, $sInterval, $sBaseDate = null)
    {
        if (is_numeric($sInterval)) {
            return (float)$sInterval;
        }
        if (!is_string($sInterval)) {
            return null;
        }

        $oInterval = static::createInterval($sInterval, $sBaseDate);

        return $oInterval->$sMethod();
    }

    /**
     * Преобразует интервал в число секунд
     *
     * @param string $sInterval - значение интервала по спецификации ISO 8601 или в человекочитаемом виде
     * @param string $sBaseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalSeconds($sInterval, $sBaseDate = null)
    {
        return static::calcTotal('totalSeconds', $sInterval, $sBaseDate);
    }

    /**
     * Преобразует интервал в число секунд
     *
     * @param string $sInterval  - значение интервала по спецификации ISO 8601 или в человекочитаемом виде
     * @param string $sBaseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalMinutes($sInterval, $sBaseDate = null)
    {
        return static::calcTotal('totalMinutes', $sInterval, $sBaseDate);
    }

    /**
     * Преобразует интервал в число секунд
     *
     * @param string $sInterval  - значение интервала по спецификации ISO 8601 или в человекочитаемом виде
     * @param string $sBaseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalHours($sInterval, $sBaseDate = null)
    {
        return static::calcTotal('totalHours', $sInterval, $sBaseDate);
    }

    /**
     * Преобразует интервал в число секунд
     *
     * @param string $sInterval  - значение интервала по спецификации ISO 8601 или в человекочитаемом виде
     * @param string $sBaseDate
     *
     * @return  int
     *
     * @throws \Exception
     */
    public static function totalDays($sInterval, $sBaseDate = null)
    {
        return static::calcTotal('totalDays', $sInterval, $sBaseDate);
    }

    /**
     * @param string $sDate
     * @param string $sInterval
     * @param string $sFormat
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function dateAddFormat($sDate, $sInterval, $sFormat = null)
    {
        $oDate = static::createDate($sDate);
        $oInterval = static::createInterval($sInterval);
        $oDate->add($oInterval->interval());

        if (null !== $sFormat) {
            return $oDate->format($sFormat);
        }
        return (string)$oDate;
    }

    /**
     * @param string $sDate
     * @param string $sInterval
     * @param string $sFormat
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function dateSubFormat($sDate, $sInterval, $sFormat = null)
    {
        $oDate = static::createDate($sDate);
        $oInterval = static::createInterval($sInterval);
        $oDate->sub($oInterval->interval());

        if (null !== $sFormat) {
            return $oDate->format($sFormat);
        }
        return (string)$oDate;
    }

    /**
     * @param string $sDate1
     * @param string $sDate2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffSeconds($sDate1, $sDate2)
    {
        $oDate1 = static::createDate($sDate1);
        $oDate2 = static::createDate($sDate2);

        return $oDate2->getTimestamp() - $oDate1->getTimestamp();
    }

    /**
     * @param string $sDate1
     * @param string $sDate2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffMinutes($sDate1, $sDate2)
    {
        return (int)floor(self::dateDiffSeconds($sDate1, $sDate2) / DateTimeInterval::PT1M);
    }

    /**
     * @param string $sDate1
     * @param string $sDate2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffHours($sDate1, $sDate2)
    {
        return (int)floor(self::dateDiffSeconds($sDate1, $sDate2) / DateTimeInterval::PT1H);
    }

    /**
     * @param string $sDate1
     * @param string $sDate2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function dateDiffDays($sDate1, $sDate2)
    {
        return (int)floor(self::dateDiffSeconds($sDate1, $sDate2) / DateTimeInterval::P1D);
    }

    /**
     * @param $xDate1
     * @param $sOperator
     * @param $xDate2
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function compare($xDate1, $sOperator, $xDate2)
    {
        $oDate1 = static::createDate($xDate1);
        $oDate2 = static::createDate($xDate2);

        return $oDate1->compare($sOperator, $oDate2);
    }

    /**
     * @param $xDate1
     * @param $xDate2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function compareWidth($xDate1, $xDate2)
    {
        $oDate1 = static::createDate($xDate1);
        $oDate2 = static::createDate($xDate2);

        return $oDate1->compareWidth($oDate2);
    }

    /**
     * @param $xComparedDate
     * @param $xDate1
     * @param $xDate2
     * @param bool $bInclude
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function between($xComparedDate, $xDate1, $xDate2, $bInclude = true)
    {
        $oComparedDate = static::createDate($xComparedDate);
        $oDate1 = static::createDate($xDate1);
        $oDate2 = static::createDate($xDate2);

        return $oComparedDate->between($oDate1, $oDate2, $bInclude);
    }

}

// EOF