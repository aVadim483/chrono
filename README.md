# Chrono  ![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/aVadim483/Chrono)[![Build Status](https://travis-ci.org/aVadim483/Chrono.svg?branch=master)](https://travis-ci.org/aVadim483/Chrono)
Extends the features of PHP DateTime classes

## Create Chrono\DateTime object

```php
use avadim\Chrono\Chrono;

// Create date from any type:
//      string - any datetime format
//      integer - unix timestamp
//      DateTime - instance of standard php class
//      Chrono\Chrono - instance of class
// You also can set time zone
$date = Chrono::createDate('now', '+3');

// Current date and time
$date = Chrono::now();

// Current date and time in Africa/Dakar timezone
$date = Chrono::now('Africa/Dakar');

// Current date and time with timezone offset -5
$date = Chrono::now(-5);

// Current date with zero time
$date = Chrono::today();

// Create date corresponding to the arguments given
$date = Chrono::make(1984, 23, 2, 17);
// $date is '1094-23-02 17:00:00

$date = Chrono::make(1984);
// $date is '1994-01-01 00:00:00'

// Create date from arguments with current time
$date = Chrono::createFromDate(1984);
// if current datetime is '2020-07-17 11:34:48' then $date is '1984-07-17 11:34:48'

// Create current date with time from arguments
$date = Chrono::createFromTime(1, 59, 7, '-5');
```

## Create period
```php
use avadim\Chrono\Chrono;

echo '<h3>Sequence</h3>';
foreach (Chrono::createPeriod('now', '+1 week')->sequenceOfDays() as $oDate) {
    echo $oDate, '<br>';
}
```

## Date operations
```php
use avadim\Chrono\Chrono;

// Adds any interval (an amount of days, months, years, hours, minutes and seconds) to given date
$date = Chrono::dateAdd('now', '1 week');

// Subtracts any interval from given date
$date = Chrono::dateSub('1742-11-28', 'P2Y1M');

// The same as Chrono::dateAdd() but returns formatted date. String '@999' is a UNIX timestamp
$dateString = Chrono::dateAddFormat('@946684800', '1 year + 2 months + 13 days', 'D M j, Y G:i:s T');

// The same as Chrono::dateAdd() but returns formatted date
$dateString = Chrono::dateSubFormat('Mar 14, 1987', 'P1Y', 'm/d/Y');
```

## Compare dates
```php
use avadim\Chrono\Chrono;

// Calculates the difference between two dates
$date1 = new \DateTime('1985-01-20');
$date2 = new \DateTime('1997-03-27');
$int = Chrono::dateDiffSeconds($date1, 'now');
$int = Chrono::dateDiffMinutes($date1, $date2);
$int = Chrono::dateDiffHours($date1, $date2);
$int = Chrono::dateDiffDays($date1, 'today');

// Compare two dates
// You can use operators: '<', 'lt', '<=', 'le', 'lte', '=', '==', 'eq', '!=', '<>', 'ne', '>=', 'gte', 'ge', '>', 'gt'
$bool = Chrono::compare($date1, '>=', $date2);

// Determines if the passed date is between two other dates
$bool = Chrono::between('1990-12-31', $date1, $date2);
```