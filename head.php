<?php

for ($i=0; $i < 3000; $i++) { 

  $int = $i;

  $test = file_get_contents('http://www.hearthhead.com/card='.$int.'&power');

  $name = preg_match_all('/name_enus\:(.*)/', $test, $match_name);

  // assume the card does not exist
  if(count($match_name[1]) == 0){
    print "\n\t (skipping turn $int) \n";
    continue;
  }

  $name = trim($match_name[1][0]);
  $name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
  $name = str_replace(' ', '_', $name);

  $pic = preg_match_all('/tooltip_enus\:(.*)/', $test, $match_pic);
  $pic = $match_pic[1][0];

  $doc = new DOMDocument();
  $doc->loadHTML($pic);
  $xpath = new DOMXPath($doc);
  $pic = $xpath->evaluate("string(//img/@src)");

  $pic = str_replace('//', 'http://', $pic);

  $originalpic = str_replace('/medium/', '/original/', $pic);
  $goldenpic = str_replace(array('/medium/', '.png'), array('/animated/', '_premium.gif'), $pic);
  

  if(file_put_contents('new_cards_3/'.$name.('_'.$int).'.png', file_get_contents($originalpic))){
    print "\n";
    print 'Stored: ' . $name;
    print "\n";
    if(file_put_contents('new_cards_3/'.$name.'_Golden'.('_'.$int).'.gif', file_get_contents($goldenpic))){
      print 'Waaa golden: ' . $name;
      print "\n";
    }
    #print '<br><img src="'.$pic.'">';
  }

  // print $test;

}