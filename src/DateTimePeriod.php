<?php
/**
 * This file is part of the avadim\Chrono package
 * https://github.com/aVadim483/Chrono
 */

namespace avadim\Chrono;

/**
 * Class DateTimePeriod
 *
 * @package avadim\Chrono
 */
class DateTimePeriod
{
    protected $minDateTime;
    protected $maxDateTime;

    /**
     * DateTimePeriod constructor
     *
     * @param mixed $minDateTime
     * @param mixed $maxDateTime
     *
     * @throws \Exception
     */
    public function __construct($minDateTime, $maxDateTime)
    {
        if (is_object($minDateTime) && $minDateTime instanceof DateTime) {
            $this->minDateTime = $minDateTime;
        } else {
            $this->minDateTime = new DateTime($minDateTime);
        }
        if (is_object($maxDateTime) && $maxDateTime instanceof DateTime) {
            $this->maxDateTime = $maxDateTime;
        } else {
            $this->maxDateTime = new DateTime($maxDateTime);
        }
    }

    /**
     * @param string $period
     * @param string $format
     *
     * @return array
     */
    public function sequenceOf($period, $format = null)
    {
        $sequence = [];
        $dateTime = clone $this->minDateTime;
        $key = -1;
        do {
            if ($format) {
                if ($format === 'YQ') {
                    $key = $dateTime->format('Y');
                    $key .= 'Q' . ((int)floor(($dateTime->getMonth() - 1)/ 3) + 1);
                } else {
                    $key = $dateTime->format($format);
                }
            } else {
                $key++;
            }
            $sequence[$key] = $dateTime;
            $dateTime = clone $dateTime;
            $dateTime->modify($period);
        } while($dateTime->compare('<=', $this->maxDateTime));

        return $sequence;
    }

    /**
     * @return array
     */
    public function sequenceOfSeconds()
    {
        return $this->sequenceOf('+1 second', 'Y-m-d H:m:s');
    }

    /**
     * @return array
     */
    public function sequenceOfMinutes()
    {
        return $this->sequenceOf('+1 minute', 'Y-m-d H:m');
    }

    /**
     * @return array
     */
    public function sequenceOfHours()
    {
        return $this->sequenceOf('+1 hour', 'Y-m-d H');
    }

    /**
     * @return array
     */
    public function sequenceOfDays()
    {
        return $this->sequenceOf('+1 day', 'Y-m-d');
    }

    /**
     * @return array
     */
    public function sequenceOfWeeks()
    {
        return $this->sequenceOf('+7 days', 'Y\WW');
    }

    /**
     * @return array
     */
    public function sequenceOfMonths()
    {
        return $this->sequenceOf('+1 month', 'Y-m');
    }

    /**
     * @return array
     */
    public function sequenceOfQuarters()
    {
        return $this->sequenceOf('+3 months', 'YQ');
    }

    /**
     * @return array
     */
    public function sequenceOfYears()
    {
        return $this->sequenceOf('+1 year', 'Y');
    }
}

// EOF