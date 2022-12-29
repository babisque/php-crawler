<?php

libxml_use_internal_errors(true);

$uri = "https://wiki.php.net";
$linkList = [];

$content = file_get_contents($uri . "/rfc");
$document = new DOMDocument();
$document->loadHTML($content);

$xPath = new DOMXPath($document);
$domNodeList = $xPath->query('.//a[@class="wikilink1"]');

/** @var DOMNode $element */
foreach ($domNodeList as $element) {
    if (str_contains($element->textContent, 'Deprecations for')) {
        array_push($linkList, $element->getAttribute("href"));
    }
}

foreach ($linkList as $link) {
    echo "Testando {$uri}{$link}\n";
    $contentRfc = file_get_contents("{$uri}{$link}");
    $document->loadHTML($contentRfc);    

    $xPathRfc = new DOMXPath($document);
    
    $contentListNode = $xPathRfc->query('.//div[@class="page group"]/div[@class="level2"]/ul/li[@class="level1"]/div[@class="li"]');
    $title = $xPathRfc->query('.//h1[@class="sectionedit1"]');
    $title = $title->item(0)->textContent;
    
    $file = fopen("./txt/" . substr($title, 9) . ".txt", 'a+');
    fwrite($file, "{$title}\n");
    
    /** @var DOMNode $element */
    foreach ($contentListNode as $element) {
        fwrite($file, " - {$element->textContent}\n");
    }

    fclose($file);
}