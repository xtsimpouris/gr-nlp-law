<?php

define("DEBUG_SQL", 0);
define("DEBUG", 0);
error_reporting(E_ALL);

include("lib.database.php");

$DB = new db("references", "localhost", "root", "******");
$DB->q('SET NAMES %s', 'UTF8');

/*
Mb_Internal_Encoding ( 'UTF-8' );
$loc = "UTF-8";
putenv("LANG=$loc");
$loc = setlocale(LC_ALL, $loc);
*/
global $a;

function unistr_to_ords($str, $encoding = 'UTF-8'){        
     // Turns a string of unicode characters into an array of ordinal values,
     // Even if some of those characters are multibyte.
     $str = mb_convert_encoding($str,"UCS-4BE",$encoding);
     $ords = array();
     
     // Visit each unicode character
     for($i = 0; $i < mb_strlen($str,"UCS-4BE"); $i++){        
         // Now we have 4 bytes. Find their total
         // numeric value.
         $s2 = mb_substr($str,$i,1,"UCS-4BE");                    
         $val = unpack("N",$s2);            
         $ords[] = $val[1];                
     }        
     return($ords);
 }
 
// print_r(unistr_to_ords("BΒ΄’'"));

function replaceme($matches) {
  global $a;
  
  if ($a) {
    $a = False;
    $k = '<i class="f">';
  }
  else {
    $a = True;
    $k = '<i class="s">';
  }
  $t = str_replace($matches[1], $k . $matches[1] . '</i>', $matches[0]);
  return $t;
}

if (isset($_GET['add'])) {
  $DB->q("INSERT INTO regexp_tests (reg_exp) VALUES (%s)", trim($_GET['add']));
  header("Location: tester.php");
  exit(); 
}

if (isset($_GET['testing'])) {
  $pattern = trim($_GET['testing']);
  $show_only = trim($_GET['show_only']);
  // $pattern = '/(' . $pattern . ')/';
  // $replacement = '<i>$1</i>';
  
  $exists = $DB->q("MAYBEVALUE SELECT reg_exp FROM regexp_tests WHERE reg_exp = %s", $pattern);
  if (empty($exists))
    echo "Time: " . date("H:i:s") . ' <a href="?add=' . urlencode($pattern) . '">Add to DB</a><BR>Pattern' . htmlspecialchars($pattern) . '<HR>';
  else
    echo "Time: " . date("H:i:s") . '<BR>Pattern' . htmlspecialchars($pattern) . '<HR>';

  $all_refs = $DB->q('KEYTABLE SELECT ref_id as ARRAYKEY, ref_text FROM all_ref ORDER BY ref_id');
  foreach($all_refs as $cid => $cref) {
    $value = ' ' . $cref['ref_text'] . ' ';
    // $value = mb_strtolower($value);
    
    if (trim($show_only) != '') {
      if (preg_match($show_only, $value) == FALSE)
        continue;
    }
    
    $a = True;
    // $result = preg_replace($pattern, $replacement, $value);
    if ($pattern != '')
      $result = preg_replace_callback($pattern, 'replaceme', $value);
    else
      $result = $value;
      
    echo <<<EOD
<div title="{$value}">$cid: $result</div>\n
EOD;
  }
  exit;
}

$already = $DB->q('KEYTABLE SELECT regexp_test_id AS ARRAYKEY, regexp_tests.* FROM regexp_tests ORDER BY outputs, regexp_test_id;');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>References testing sciprt</title>
    <script src="jquery-1.8.1.min.js" type="text/javascript"></script>
    <script>
      // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding#The_.22Unicode_Problem.22
      function b64_to_utf8( str ) {
        return decodeURIComponent(escape(window.atob( str )));
      }
      
      function refresh_results(text) {
        text = b64_to_utf8(text);
        
        $('#results').prepend("...waiting...");
        var only = $("#only").val();
        $.get("tester.php", {testing: text, show_only: only}, function(data) {
          $('#results').html(data);
        });
      }
      
      function copy_to_textbox(text) {
        text = b64_to_utf8(text);
        
        $("#testing").val(text);
      }
      
      $(document).ready(function(){
        $("#testing").keyup(function(e){
          var val = $("#testing").val();
          var only = $("#only").val();
          var code = e.which; // recommended to use e.which, it's normalized across browsers
          
          if(code==13)
            e.preventDefault();
          
          if (code == 13) {
            $('#results').prepend("...waiting...");
            $.get("tester.php", {testing: val, show_only: only}, function(data) {
              $('#results').html(data);
            });
          }
        });
        $("#only").keyup(function(e){
          var val = $("#testing").val();
          var only = $("#only").val();
          var code = e.which; // recommended to use e.which, it's normalized across browsers
          
          if(code==13)
            e.preventDefault();
          
          if (code == 13) {
            $('#results').prepend("...waiting...");
            $.get("tester.php", {testing: val, show_only: only}, function(data) {
              $('#results').html(data);
            });
          }
        });
      });
    </script>
    <style>
      i.f { background-color: #DABC90; }
      i.s { background-color: #8CD0D9; }
    </style>
  </head>
  <body>
  <div style="position:fixed; top:5px; right: 5px; bachground-color: white;  padding-right: 10px;">
    Show only /(?)/: <input id="only" style="width: 500px;" value="/(.*)/"><BR>
    Test regular expression /(?)/: <input id="testing" style="width: 500px;"><BR><BR>
    <?php
    foreach($already as $id => $d) {
      // $d['reg_exp'] = mb_convert_encoding($d['reg_exp'], 'utf-8');
      $big = $d['reg_exp'];
      $small = $big;
      if (mb_strlen($big) > 50)
        $small = mb_substr($small, 0, 50) . '...';
      
      $js = $big;
      $js = base64_encode($js);
      // $js = str_replace("'", '\\\'', $js);
      // $js = str_replace('\\', '\\\\', $js);
      
      $title = htmlspecialchars($big);
      $small_title = htmlspecialchars($small);
      
      echo <<<EOD
<div style="float: right; clear: both;">
  <a href="javascript: refresh_results('{$js}');" title="{$id} . {$title}">{$small_title}</a>
  [<a href="javascript: copy_to_textbox('{$js}');" title="copy to text box">+</a> ]
</div>
EOD;
    }
    ?>
    </div>
  <div id="results" style="padding:top: 40px;">
  </div>
  </body>
</html>
  
