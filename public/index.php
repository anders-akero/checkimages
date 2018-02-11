<?php
if (!isset($_GET['auth'])) {
    die('Unauthorised');
}
if ($_GET['auth'] !== 'princessessee') {
    die('You are not allowed to see this!');
}

$startTime = '0955';//format HHMM
$endTime = '1555';//format HHMM

$date = $_GET['date'] ?? date('Ymd');
if (isset($_GET['image'])) {
    $image = $_GET['image'];
    $date = 20 . substr($image, 1, 6);
}

function isValidDate($dateToCheck)
{
    if (preg_match("/^(\d{4})(\d{2})(\d{2})$/", $dateToCheck, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    }
    return false;
}

if (!isValidDate($date)) {
    die('Invalid date');
}

if (in_array(date('N', strtotime($date)), [6, 7])) {
    die('It is weekend');
}

$folder = '../data/living_room/' . $date . '/images';

if (!is_dir($folder)) {
    die('No images taken on this date ');
}

$images = scandir($folder);

$timestampStart = substr($date, 2) . $startTime . 00;
$timestampEnd = substr($date, 2) . $endTime . 00;

if (isset($_GET['image'])) {
    $image = $_GET['image'];
    $imagePath = $folder . '/' . $image;
    if ($_GET['download'] ?? false) {
        header('Content-Disposition: attachment; filename="' . basename($image) . '"');
    }
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . filesize($imagePath));
    readfile($imagePath);
    die();
}

$showingImages = false;
foreach ($images as $image) {
    if (substr($image, 0, 1) !== 'A' || substr($image, -4) !== '.jpg') {
        continue;
    }
    $timestamp = substr($image, 1, -4);
    $timestamp = substr($timestamp, 0, -2);
    if ($timestamp < $timestampStart) {
        continue;
    }
    if ($timestamp > $timestampEnd) {
        break;
    }
    $imagePath = $folder . '/' . $image;

    $imgData = base64_encode(file_get_contents($imagePath));
    $src = 'data: ' . mime_content_type($img_file) . ';base64,' . $imgData;
    ?>
    <img src="<?= $src; ?>" alt="" style="max-width: 100%;">
    <a href="?image=<?= $image; ?>&auth=<?= $_GET['auth']; ?>&download=true">Download</a>
    |
    <a href="?image=<?= $image; ?>&auth=<?= $_GET['auth']; ?>">Open in full screen</a>
    <?php
    $showingImages = true;
}

if (!$showingImages) {
    die('No images taken between ' . substr($startTime, 0, 4) . ' and ' . substr($endTime, 0, 4));
}