<?php

$src = 'http://www.hearthpwn.com/cards?page=7';
$src = file_get_contents($src);
$src = explode("\n", $src);

$cardlist = [];
foreach ($src as $out) {
  $stream = trim($out);
  if(preg_match_all('/\<a href=\"\/cards\/(\d{3})-(.*)\"/', $stream, $afk)){

    $card_id = $afk[1][0];
    $card_raw_text = $afk[0][0];

    # determine full card id now that we have the card id
    $card_id_path = str_replace('<a href="/cards/'.$card_id, '', $card_raw_text);
    $card_id_path = explode('"', $card_id_path); // remove anything after the double quote
    $card_id_path = $card_id_path[0];

    # create the id
    $card_id_path = $card_id . $card_id_path;

    # because all card pictures are stored in a unique location, fetch this too
    // $card_avatar = str_replace('<a href="/cards/'.$card_id_path.'" data-show-tooltip="false"><img src="', '', $stream);
    // $card_avatar = str_replace('" width="230" /></a>', '', $card_avatar);

    array_push($cardlist, $card_id_path);
    #print $card_raw_text . "\n";
  } 
}

# because array_unique uses string conversion to check, specify SORT_REGULAR flag
# so it will work for multidimensional arrays

$cardlist = array_unique($cardlist, SORT_REGULAR);

print "Got card list, traversing and stealing images...\n";

$x=0;
foreach ($cardlist as $card) {
  $get_card = file_get_contents('http://www.hearthpwn.com/cards/'.$card);
  $card_data = explode("\n", $get_card);
  $line = 0;
  foreach ($card_data as $cc) {
    if(preg_match_all('/\<div class=\"hearth-tooltip\" >(.*)/', $cc)){
    // if(preg_match_all('/\<div class=\"(.*)\"\>\<img src=\"(.*)\" width=\"230\" \/\>/', $cc)){
      $real_image_url = trim($card_data[($line+2)]);
      $real_image_url = str_replace('<img src="', '', $real_image_url);
      $real_image_url = str_replace('" width="230" />', '', $real_image_url); 
      // remove card id from the text
      $human_card = preg_replace('/(\d{3})-/', '', $card);
      if(file_put_contents('cards/'.$human_card.'.png', file_get_contents($real_image_url))){
        print 'Got ' . $human_card . ' ('.$real_image_url.')';
        print "\n";
      }
      else {
        die('Err 32');
      }
    }
    $line++;
  }
  // if($x == 5) exit;
  $x++;
}
// print_r($cardlist);
#print_r($src);

