<?php

require_once '../src/Auth.php';
require_once '../src/Authenticate.php';
require_once '../src/Date.php';
require_once '../src/Image.php';
require_once '../src/Response.php';
require_once '../src/Time.php';

$folderLocation = '../data/living_room/%s/images';

try {
    new Authenticate($_GET['auth'] ?? '');
} catch (UnauthorizedAccessException $e) {
    return new Response('You are not allowed to see this!', Response::HTTP_UNAUTHORIZED);
}

$date = new Date($_GET['date'] ?? date('Ymd'));

if ($date->isWeekend()) {
    return new Response('It is weekend', Response::HTTP_FORBIDDEN);
}

$folder = sprintf($folderLocation, $date);

$timestampStart = substr($date, 2) . Time::START_CLOCK . '00';
$timestampEnd = substr($date, 2) . Time::END_CLOCK . '00';

$imagesToShow = [];
$allImages = is_dir($folder) ? scandir($folder) : [];
foreach ($allImages as $img) {
    $image = new Image($img);
    if (!$image->isFromSecurityCamera()) {
        continue;
    }
    if ($image->takenBefore($timestampStart)) {
        continue;
    }
    if ($image->takenAfter($timestampEnd)) {
        break;
    }
    $imagesToShow[] = $image;
}

if (isset($_GET['asJson'])) {
    return new Response(json_encode(array_map(function (Image $image) {
        return $image->getImage();
    }, $imagesToShow)), Response::HTTP_OK);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Security camera living room">
    <link rel="manifest" href="manifest.json">
    <title>Security camera</title>
</head>

<body>
<?php if (!$imagesToShow): ?>
    No images taken between <?= Time::START_CLOCK; ?> and <?= Time::END_CLOCK; ?>
<?php endif; ?>
<?php foreach ($imagesToShow as $image): ?>
    <img src="/images/?name=<?= $image; ?>&auth=<?= Auth::getToken(); ?>" alt="" style="max-width: 100%;">
    <a href="/images/?name=<?= $image; ?>&auth=<?= Auth::getToken(); ?>&download=true">Download</a>
    |
    <a href="/images/?name=<?= $image; ?>&auth=<?= Auth::getToken(); ?>">Open in full screen</a>
<?php endforeach; ?>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').then(function (registration) {
            }, function (err) {
                console.error('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>
</body>

</html>