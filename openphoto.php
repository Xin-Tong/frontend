<?php
#$consumerKey = getenv('consumerKey');
#$consumerSecret = getenv('consumerSecret');
#$token = getenv('token');
#$tokenSecret = getenv('tokenSecret');

//$arguments = getopt('F:X:h:e:pvs', array('encode::'));
$host = 'localhost';
$useSSL = false;

/*
if(isset($arguments['h']))
  $host = $arguments['h'];
if(isset($arguments['X']))
  $method = strtolower($arguments['X']);
$endpoint = '/photos/pageSize-3/list.json';
if(isset($arguments['e']))
  $endpoint = $arguments['e'];
$pretty = false;
if(isset($arguments['p']))
  $pretty = true;
$verbose = false;
if(isset($arguments['v']))
  $verbose = true;
if(isset($arguments['s']))
  $useSSL = true;
$encode = false;
if(isset($arguments['encode']))
  $encode = true;
*/

//get data from url
//$host=$_SERVER['HTTP_HOST'];


//set the address of server
$host='ec2-54-85-195-87.compute-1.amazonaws.com';
#echo $host."\n";
$operation=$_GET['operation'];
#echo $operation."<br />";
$ID=$_GET['ID'];
#$path=$_GET['path'];
#echo is_uploaded_file($_FILES['file']['tmp_name']);
#move_uploaded_file($_FILES['file']['tmp_name'], "/home/ubuntu/test/test.jpg");
$path=$_FILES['file']['tmp_name'];
#$path="/home/ubuntu/test/test.jpg";
$tags=$_GET['tags'];
if ($operation == 'upload'){
        $consumerKey = $_GET['consumerKey'];
        $consumerSecret = $_GET['consumerSecret'];
        $token = $_GET['token'];
        $tokenSecret = $_GET['tokenSecret'];
}
#print_r ($_FILES);

if($operation=='hello')
    {
        $endpoint='/hello.json';
        $method = 'get';
    }
if($operation=='view')
    {
        $method = 'get';
        $endpoint="/photo/$ID/$operation.json";
    }   
if($operation=='upload')
    {
        $method = 'post';
        #$useSSL = true;
        $endpoint='/photo/upload.json';
        $fields = array();
        if(isset($path))
            {
                $fields['photo']='@' . $path;
                $fields['tags']=$tags;
                $fields['auth']='true';
            } 
    }
#print_r ($fields);
#echo $endpoint."\n";

/*
$encode = false;
$fields = array();
if(isset($arguments['F']))
{
  foreach((array)$arguments['F'] as $field)
  {
    $parts = explode('=', $field);
    if($encode && $parts[0] == 'photo' && strncmp($parts[1][0], '@', 1) == 0 && is_file(substr($parts[1], 1)))
      $fields[$parts[0]] = base64_encode(file_get_contents(substr($parts[1], 1)));
    else
      $fields[$parts[0]] = $parts[1];
  }
}
*/

#$endpoint = "/photo/upload.json";
#$fields = array('photo' => '@' . $path, 'tags' => $tags, 'auth' => 'true');


include 'OpenPhotoOAuth.php';
$client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $token, $tokenSecret);
$client->useSSL($useSSL);
if($method == 'get')
  $resp = $client->get($endpoint, $fields);
elseif($method == 'post')
  $resp = $client->post($endpoint, $fields);

$verbose=true;
if($verbose)
  #echo sprintf("==========\nMethod: %s\nHost: %s\nEndpoint: %s\nSSL: %s\n==========\n\n", $method, $host, $endpoint, $useSSL ? 'Yes' : 'No');

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
