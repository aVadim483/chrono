<?php
/**
 * This file is part of the avadim\Chrono package
 * https://github.com/aVadim483/Chrono
 */

namespace avadim\Chrono;

/**
 * Class DateTimeZone
 *
 * @package avadim\Chrono
 */
class DateTimeZone extends \DateTimeZone
{
    /**
     * @param mixed $dateTimeZone
     *
     * @return DateTimeZone
     */
    public static function create($dateTimeZone = null)
    {
        if (is_numeric($dateTimeZone)) {
            return new static($dateTimeZone);
        }
        if (null === $dateTimeZone) {
            $dateTimeZone = date_default_timezone_get();
        } elseif ($dateTimeZone instanceof \DateTimeZone) {
            $dateTimeZone = $dateTimeZone->getName();
        }
        return new static($dateTimeZone);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}

// EOF