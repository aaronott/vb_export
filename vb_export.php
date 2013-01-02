#!/usr/bin/env php
<?php
/**
 * This script works by grabbing the RSS feed from VB and pulling down the
 * video file associated with each video.
 */

$feedid = '';
$path = "https://my.videobloom.com/feed/";
$output_dir = "videos";

$path .= $feedid . "/rss";

echo "Fetching the list... This could take a few seconds.\n";
$rss = file_get_contents($path);

echo "Okay I have the list, now let's start the magic.\n";

if (! function_exists("simplexml_load_string")) {
  die("You don't have simplexml_load_string().\n");
}

$xml = @simplexml_load_string($rss);
if (!$xml instanceof SimpleXMLElement) {
  die("Yeah... something broke and I was unable to load the RSS.");
}

//print_r($xml);

$videos = $xml->channel->item;

echo "You have " . sizeof($videos) . " videos in the list.\n";
echo "I will start downloading them now.\n";

if (!file_exists($output_dir)) {
  mkdir($output_dir, 0755);
}

foreach ($videos as $video) {
  // create the filename from the title_first5.ext
  $filename = preg_replace('/ /', '_', $video->title);
  $first_five = substr(pathinfo($video->link, PATHINFO_FILENAME), 0, 5);
  $ext = pathinfo($video->link, PATHINFO_EXTENSION);

  $output_name = $filename . "_" . $first_five . "." . $ext;


  echo "\nDownloading $output_name...\n";
  system("curl --progress-bar -o $output_dir/$output_name $video->link");

  die();
}
