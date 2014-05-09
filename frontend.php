<?php
$useSSL = false;
$fields = array();

//set the address of server
$BL1_host='54.85.111.90';
$BL2_host='54.85.171.21';
$operation=$_GET['operation'];
$ID=$_GET['ID'];
$path=$_FILES['file']['tmp_name'];
$tags=$_GET['tags'];
if ($operation == 'upload'){
    $consumerKey = $_GET['consumerKey'];
    $consumerSecret = $_GET['consumerSecret'];
    $token = $_GET['token'];
    $tokenSecret = $_GET['tokenSecret'];
}

if($operation=='hello') {
    $endpoint='/hello.json';
    $method = 'get';
    $passed_host = $BL1_host;
}
if($operation=='view') {
    $endpoint="/photo/$ID/view.json";
    $passed_host = $BL2_host;
    $method = 'get';

}   
if($operation=='upload') {
    $method = 'post';
    $endpoint='/photo/upload.json';
    $passed_host = $BL2_host;
    if(isset($path)) {
        $fields['photo']='@' . $path;
        $fields['tags']=$tags;
        $fields['auth']='true';
    } 
}

include 'OpenPhotoOAuth.php';
$client = new OpenPhotoOAuth($passed_host, $consumerKey, $consumerSecret, $token, $tokenSecret);
$client->useSSL($useSSL);
if($method == 'get')
  $resp = $client->get($endpoint, $fields);
elseif($method == 'post')
  $resp = $client->post($endpoint, $fields);

$verbose=true;
if($verbose)
  echo sprintf("==========\nMethod: %s\nHost: %s\nEndpoint: %s\nSSL: %s\n==========\n\n", $method, $passed_host, $endpoint, $useSSL ? 'Yes' : 'No');

$pretty = true;
if($pretty)
  echo indent($resp);
else
  echo $resp;

if($verbose || $pretty)
  echo "\n";

// from https://gist.github.com/906036
function indent($json) {

  $result      = '';
  $pos         = 0;
  $strLen      = strlen($json);
  $indentStr   = '  ';
  $newLine     = "\n";
  $prevChar    = '';
  $outOfQuotes = true;

  for ($i=0; $i<=$strLen; $i++) {

    // Grab the next character in the string.
    $char = substr($json, $i, 1);

    // Put spaces in front of :
    if ($outOfQuotes && $char == ':' && $prevChar != ' ') {
      $result .= ' ';
    }
    
    if ($outOfQuotes && $char != ' ' && $prevChar == ':') {
      $result .= ' ';
    }

    // Are we inside a quoted string?
    if ($char == '"' && $prevChar != '\\') {
      $outOfQuotes = !$outOfQuotes;

      // If this character is the end of an element, 
      // output a new line and indent the next line.
    } else if(($char == '}' || $char == ']') && $outOfQuotes) {
      $result .= $newLine;
      $pos --;
      for ($j=0; $j<$pos; $j++) {
        $result .= $indentStr;
      }
    }

    // Add the character to the result string.
    $result .= $char;
    
    // If the last character was the beginning of an element, 
    // output a new line and indent the next line.
    if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
      $result .= $newLine;
      if ($char == '{' || $char == '[') {
        $pos ++;
      }

      for ($j = 0; $j < $pos; $j++) {
        $result .= $indentStr;
      }
    }

    $prevChar = $char;
  }

  return $result;
}
