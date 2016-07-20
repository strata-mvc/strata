<?php

/**
 * This script takes raw output of phpDox from ~/_api/, snips out core content and
 * inserts it in our custom theme.
 *
 * Bridges phpDox and a cooler design.
 */

$rootDir = dirname(dirname(__FILE__));
$apiSource = $rootDir . DIRECTORY_SEPARATOR . '_api' . DIRECTORY_SEPARATOR;
$apiDestination = $rootDir . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . "1.0" . DIRECTORY_SEPARATOR;
$layoutFile = $rootDir . DIRECTORY_SEPARATOR . '_layouts' . DIRECTORY_SEPARATOR . 'api.html';

echo "Deleting previous files...";

foreach (rsearch($apiDestination . 'classes', '/.*\.html$/') as $originalfile) {
    unlink($originalfile);
}
foreach (rsearch($apiDestination . 'traits', '/.*\.html$/') as $originalfile) {
    unlink($originalfile);
}


$dom = getDom($apiSource . 'classes.html');
createOurFile("Strata API - Classes", extractContent($dom), "classes.html");

$dom = getDom($apiSource . 'traits.html');
createOurFile("Strata API - Traits", extractContent($dom), "traits.html");

$classesFolder = $apiSource . 'classes';
foreach (rsearch($classesFolder, '/.*\.html$/') as $originalfile) {
    $dom = getDom($originalfile);
    $destination = str_replace($classesFolder, "", $originalfile);
    createOurFile(extractTitle($dom), extractContent($dom), 'classes' . $destination);
}

$traitsFolder = $apiSource . 'traits';
foreach (rsearch($traitsFolder, '/.*\.html$/') as $originalfile) {
    $dom = getDom($originalfile);
    $destination = str_replace($traitsFolder, "", $originalfile);
    createOurFile(extractTitle($dom), extractContent($dom), 'traits' . $destination);
}



echo "Done!\n\n";







//
// Utilities.
//

function createOurFile($pageTitle = '', $content = '', $filename) {

    global $layoutFile, $apiDestination;

    $newFileContents = file_get_contents($layoutFile);

    $newFileContents = str_replace("{{ page_title }}", $pageTitle, $newFileContents);
    $newFileContents = str_replace("{{ page_content }}", $content, $newFileContents);

    echo "Converting $apiDestination$filename\n";

    $parentDir = dirname($apiDestination . $filename);
    if (!file_exists($parentDir)) {
        mkdir($parentDir);
    }

    file_put_contents($apiDestination . $filename, $newFileContents);
}

function extractContent($dom)
{
    $contentNode = getElementById($dom, 'mainstage');

    $history = getElementById($dom, 'history');
    if ($history) {
        $history->parentNode->removeChild($history);

        $historyWrapper = findByClass($dom, 'history');
        if ($historyWrapper->length) {
            $historyWrapper->item(0)->parentNode->removeChild($historyWrapper->item(0));
        }
    }

    $additionalNav = $contentNode->getElementsByTagName("nav")->item(0);
    if ($additionalNav) {
        $additionalNav->parentNode->removeChild($additionalNav);
    }

    return $dom->saveHTML($contentNode);
}

function extractTitle($dom)
{
    $titleNode = $dom->getElementsByTagName('h1')->item(0);
    return strip_tags($dom->saveHTML($titleNode));
}

function findByClass($dom, $class)
{
    $selector =  sprintf("//*[contains(concat(' ', normalize-space(@class), ' '), ' %s ')]", $class);
    $xpath = new DOMXPath($dom);
    return $xpath->query($selector);
}

function getElementById($dom, $id)
{
    $xpath = new DOMXPath($dom);
    return $xpath->query("//*[@id='$id']")->item(0);
}

function getDom($filepath)
{
    $dom = new DOMDocument();

    echo "Opening $filepath\n";

    libxml_use_internal_errors(true);
    @$dom->loadHTMLFile($filepath);
    return $dom;
}

function rsearch($folder, $pattern)
{
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}
