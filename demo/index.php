<?php

include_once __DIR__ . '/../src/autoload.php';

use avadim\Chrono\Chrono;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>avadim/Chrono</title>
    <style>
        td { padding: 10px; border: 1px solid #111;}
    </style>
</head>
<body>
<?php
$date1 = Chrono::createFrom(1984);
$date2 = Chrono::today()->subYears(1);

echo '1984 year is ', $date1, '<br>';
echo 'Year ago is ', $date2, '<br>';
echo '<br>';
echo "Difference between $date1 and $date2 is ", Chrono::dateDiffYears($date1, $date2), ' years or ', Chrono::dateDiffMonths($date1, $date2), ' months <br>';
echo '<br>';

$now = Chrono::now();
echo 'Default timezone is ', $now->getTimeZone(), ' and now is ', $now->format('Y-m-d-H:i:sP'), '<br>';

$now = Chrono::now('Pacific/Chatham');
echo 'But in ', $now->getTimeZone(), ' now is ', $now->format('Y-m-d-H:i:sP'), '<br>';

$date1 = Chrono::today();
$date2 = Chrono::dateAdd('today', '1 week')->format('Y-m-d');
$dayName = $date1->format('l');
echo "Today is $dayName and next $dayName at $date2<br>";

echo '<h3>Sequence of dates</h3>';
foreach (Chrono::createPeriod('now', '+1 week')->sequenceOfDays() as $date) {
    echo $date, '<br>';
}
?>
<table>
    <tr>
        <th>Interval</th>
        <th>Base date</th>
        <th>Days</th>
        <th>Seconds</th>
    </tr>
<?php
$aIntervals = [
    '1 month',
    'P30D',
    'P1M',
];

$aBaseDate = [
    'now',
    '2017-01-01',
    '2017-02-01',
    '2016-02-01',
];

foreach($aIntervals as $sInterval) {
    echo '<tr><td>', $sInterval, '</td>', '<td>default</td><td>', Chrono::totalDays($sInterval), '</td><td>', Chrono::totalSeconds($sInterval), '</td></tr>';
}
foreach($aBaseDate as $sDate) {
    echo '<tr><td>P1M</td><td>', $sDate, '</td><td>', Chrono::totalDays('P1M', $sDate),'</td><td>', Chrono::totalSeconds('P1M', $sDate), '</td></tr>';
}
?>
</table>
</body>
</html>

