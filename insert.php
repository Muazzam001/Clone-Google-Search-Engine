<?php
include("config.php");
include('classes/DomDocumentParser.php');
$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

if(isset($_POST['url'])){
    $start_url = $_POST['url'];
    insertLink($start_url);
    followLinks($start_url);
}

function linkExists($url){
    global $con;
    $query = $con->prepare("SELECT * FROM submit_url WHERE url = :url"); 
    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0;
}

function insertLink($url){
    global $con;
    $query = $con->prepare("INSERT INTO submit_url(url) VALUES (:url)");
    $query->bindParam(":url", $url);
    return $query->execute();
}

function createLink($src, $url) {
    $scheme = parse_url($url)["scheme"]; // http or https
    $host = parse_url($url)["host"]; // website.domain

    if(substr($src, 0, 2) == "//") {
        $src =  $scheme . ":" . $src;
    }
    else if(substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    else if(substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    else if(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    else if(substr($src, 0, 4) != "http" & substr($src, 0, 5) != "https") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    return $src;
}

function followLinks($url) {
    global $alreadyCrawled;
    global $crawling;

    $parser = new DomDocumentParser($url); 
    $link_lists = $parser->getLinks();
    foreach ($link_lists as $link){
        $href = $link->getAttribute("href"); 
        if(strpos($href, "#") !== false){ //check url have str #
            continue;
        }else if(substr($href, 0 ,11) == "javascript:"){//check and remove javascipt
            continue;
        }
        $href = createLink($href, $url);
        if(!in_array($href, $alreadyCrawled)){
            $alreadyCrawled[] = $href;
            $crawling = $href;
            getDetails($href);
        }
    }
    array_shift($crawling);
    foreach ($crawling as $site) {
        followLinks($site);
    }
}

function getDetails($url){
    global $alreadyFoundImages;
    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitleTags();
    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL){
        return;
    }
    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);
    if($title == ""){
        return;
    }

    $metaArray = $parser->getMetaTags();

    if(linkExists($url)){
        echo "$url already exists<br>";
    }else if(insertLink($url)){
        echo "Insert success $url to database<br>";
    }else{
        echo "failed insert $url";
    }
}
?>