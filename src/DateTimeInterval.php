<?php
/**
 * This file is part of the avadim\Chrono package
 * https://github.com/aVadim483/Chrono
 */

namespace avadim\Chrono;

/**
 * Class DateTimeInterval
 *
 * @package avadim\Chrono
 */
class DateTimeInterval
{
    const PT1S = 1; // 1; = 1 second
    const PT1M = 60; // 60 * 1; = 1 minute
    const PT1H = 3600; // 60 * 60 * 1; = 1 hour
    const P1D = 86400; // 60 * 60 * 24 * 1; = 1 day
    const P1W = 604800; // 60 * 60 * 24 * 7; = 1 week
    const P1M = 2592000; // 60 * 60 * 24 * 30; = 1 month
    const P1Y = 31536000; // 60 * 60 * 24 * 365; = 1 year

    protected $baseDateTime;
    protected $dateTimeInterval;

    /**
     * DateTimeInterval constructor
     *
     * @param string $interval
     * @param mixed $baseDateTime
     *
     * @throws \Exception
     */
    public function __construct($interval, $baseDateTime = null)
    {
        try {
            if (is_numeric($interval)) {
                $this->dateTimeInterval = new \DateInterval('PT' . $interval . 'S');
            } elseif (is_string($interval) && $interval[0] === 'P') {
                $this->dateTimeInterval = new \DateInterval($interval);
            } else {
                $this->dateTimeInterval = \DateInterval::createFromDateString($interval);
            }
        } catch (\Exception $e) {
            $this->dateTimeInterval = new \DateInterval(self::normalize($interval));
        }
        if ($baseDateTime) {
            $this->setBaseDateTime($baseDateTime);
        }
    }

    /**
     * Normalizes interval according by ISO 8601
     *
     * @param string $intervalString
     *
     * @return string
     */
    public static function normalize($intervalString)
    {
        $result = '';
        if (preg_match('/P(?P<y>\d+Y)?(?P<m>\d+M)?(?P<w>\d+W)?(?P<d>\d+D)?(T)?(?P<th>\d+H)?(?P<tm>\d+M)?(?P<ti>\d+I)?(?P<ts>\d+S)?/i', $intervalString, $matches)) {
            $p = '';
            $t = '';
            if (isset($matches['y'])) {
                $p .= $matches['y'];
            }
            if (isset($matches['m'])) {
                $p .= $matches['m'];
            }
            // can not be used with D
            if (isset($matches['w']) && !isset($matches['d'])) {
                $p .= $matches['d'];
            }
            if (isset($matches['d'])) {
                $p .= $matches['d'];
            }
            if (isset($matches['th'])) {
                $t .= $matches['th'];
            }
            if (isset($matches['tm'])) {
                $t .= $matches['tm'];
            }
            // non-standard I is replaced by M
            if (!isset($matches['tm']) && isset($matches['ti'])) {
                $t .= str_replace('I', 'M', $matches['ti']);
            }
            if (isset($matches['ts'])) {
                $t .= $matches['ts'];
            }
            if ($p || $t) {
                $result = 'P';
            }
            if ($p) {
                $result .= $p;
            }
            if ($t) {
                $result .= 'T' . $t;
            }
        }
        if ($result) {
            return strtoupper($result);
        }
        return 'PT0S';
    }

    /**
     * @param $baseDateTime
     */
    public function setBaseDateTime($baseDateTime)
    {
        $this->baseDateTime = $baseDateTime;
    }

    /**
     * Get total seconds of interval
     *
     * @param string $baseDateTime
     *
     * @return float
     *
     * @throws \Exception
     */
    public function totalTime($baseDateTime = null)
    {
        $f = isset($this->dateTimeInterval->f) ? $this->dateTimeInterval->f : 0.0;
        if (null !== $baseDateTime || $this->baseDateTime) {
            $date1 = new \DateTimeImmutable($baseDateTime ?: $this->baseDateTime);
            $date2 = $date1->add($this->dateTimeInterval);
            $interval = $date2->diff($date1);
            return (float)$interval->format('%a') * self::P1D + $interval->h * self::PT1H + $interval->i * self::PT1M + $interval->s + $f;
        }
        return ($this->dateTimeInterval->y * self::P1Y)
            + ($this->dateTimeInterval->m * self::P1M)
            + ($this->dateTimeInterval->d * self::P1D)
            + ($this->dateTimeInterval->h * self::PT1H)
            + ($this->dateTimeInterval->i * self::PT1M)
            + $this->dateTimeInterval->s + $f;
    }

    /**
     * Get total seconds of interval
     *
     * @param string $baseDateTime
     *
     * @return int
     *
     * @throws \Exception
     */
    public function totalSeconds($baseDateTime = null)
    {
        return (int)floor($this->totalTime($baseDateTime));
    }

    /**
     * Get total minutes of interval
     *
     * @param string $baseDateTime
     *
     * @return int
     *
     * @throws \Exception
     */
    public function totalMinutes($baseDateTime = null)
    {
        return (int)floor($this->totalSeconds($baseDateTime) / self::PT1M);
    }

    /**
     * Get total hours of interval
     *
     * @param string $baseDateTime
     *
     * @return int
     *
     * @throws \Exception
     */
    public function totalHours($baseDateTime = null)
    {
        return (int)floor($this->totalSeconds($baseDateTime) / self::PT1H);
    }

    /**
     * Get total days of interval
     *
     * @param string $baseDateTime
     *
     * @return int
     *
     * @throws \Exception
     */
    public function totalDays($baseDateTime = null)
    {
        return (int)floor($this->totalSeconds($baseDateTime) / self::P1D);
    }

    /**
     * @return \DateInterval
     */
    public function interval()
    {
        return $this->dateTimeInterval;
    }
}

// EOF