<?php

require_once '../src/Authenticate.php';
require_once '../src/Date.php';

$deleteFilesAfterZipped = false;

$folderLocation = '../data';
$nameOfZipFile = 'seccam.zip';

try {
    new Authenticate($_GET['auth'] ?? '');
} catch (UnauthorizedAccessException $e) {
    return new Response('You are not allowed to see this!', Response::HTTP_UNAUTHORIZED);
}

$folder = $folderLocation;
// Get real path for our folder
$rootPath = realpath($folder);

// Initialize archive object
$zip = new ZipArchive();

$zip->open($nameOfZipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Initialize empty "delete list" and one list with all added files
$filesToDelete = $addedFiles = [];

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    // Skip directories (they would be added automatically)
    if (!$file->isDir()) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
        $addedFiles[] = $file->getFilename();

        // Add current file to "delete list"
        // delete it later cause ZipArchive create archive only after calling close function and ZipArchive lock files until archive created)
        if ($deleteFilesAfterZipped) {
            $filesToDelete[] = $filePath;
        }
    }
}

// Zip archive will be created only after closing object
$zip->close();

// Delete all files from "delete list"
foreach ($filesToDelete as $file) {
    unlink($file);
}

// Send the headers to force download the zip file
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$nameOfZipFile");
header("Pragma: no-cache");
header("Expires: 0");
readfile("$nameOfZipFile");
// Deleting the newly created zipfile
//unlink($nameOfZipFile);
die();
