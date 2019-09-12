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
    protected $oDate1;
    protected $oDate2;

    /**
     * DateTimePeriod constructor
     *
     * @param mixed $xDate1
     * @param mixed $xDate2
     *
     * @throws \Exception
     */
    public function __construct($xDate1, $xDate2)
    {
        $this->oDate1 = new DateTime($xDate1);
        $this->oDate2 = new DateTime($xDate2);
    }

    /**
     * @param string $sPeriod
     * @param string $sFormat
     *
     * @return array
     */
    public function sequenceOf($sPeriod, $sFormat = null)
    {
        $aSequence = [];
        $oDate = clone $this->oDate1;
        $xKey = -1;
        do {
            if ($sFormat) {
                if ($sFormat === 'YQ') {
                    $xKey = $oDate->format('Y');
                    $xKey .= 'Q' . ((int)floor(($oDate->getMonth() - 1)/ 3) + 1);
                } else {
                    $xKey = $oDate->format($sFormat);
                }
            } else {
                $xKey++;
            }
            $aSequence[$xKey] = $oDate;
            $oDate = clone $oDate;
            $oDate->modify($sPeriod);
        } while($oDate->compare('<=', $this->oDate2));

        return $aSequence;
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