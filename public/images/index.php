<?php

require_once '../../src/Auth.php';
require_once '../../src/Authenticate.php';
require_once '../../src/Date.php';
require_once '../../src/Image.php';
require_once '../../src/Response.php';
require_once '../../src/Time.php';

$folderLocation = '../../data/living_room/%s/images';

try {
    new Authenticate($_GET['auth'] ?? '');
} catch (UnauthorizedAccessException $e) {
    return new Response('You are not allowed to see this!', Response::HTTP_UNAUTHORIZED);
}

$image = new Image($_GET['name'] ?? '');
if (!$image->isFromSecurityCamera()) {
    return new Response('Invalid image', Response::HTTP_FORBIDDEN);
}
$date = $image->getDate();

if ($date->isWeekend()) {
    return new Response('The image was taken on a weekend', Response::HTTP_FORBIDDEN);
}

$timestampStart = substr($date, 2) . Time::START_CLOCK . '00';
$timestampEnd = substr($date, 2) . Time::END_CLOCK . '00';
if ($image->takenBefore($timestampStart)) {
    $message = 'Images taken before ' . Time::START_CLOCK;
    return new Response($message, Response::HTTP_FORBIDDEN);
}
if ($image->takenAfter($timestampEnd)) {
    return new Response($message, Response::HTTP_FORBIDDEN);
    $message = 'Images taken after ' . Time::END_CLOCK;
}

$folder = sprintf($folderLocation, $date);

if (!is_dir($folder)) {
    return new Response('Invalid image', Response::HTTP_FORBIDDEN);
}

$imagePath = $folder . '/' . $image;

if ($_GET['download'] ?? false) {
    header('Content-Disposition: attachment; filename="' . basename($image) . '"');
}
header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($imagePath));
readfile($imagePath);
