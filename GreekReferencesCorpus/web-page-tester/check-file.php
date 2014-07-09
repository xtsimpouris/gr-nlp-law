<?php

define("DEBUG_SQL", 0);
define("DEBUG", 0);
error_reporting(E_ALL);

include("lib.database.php");

$DB = new db("references", "localhost", "root", "*****");
$DB->q('SET NAMES %s', 'UTF8');

global $a;

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

$already = $DB->q('KEYTABLE SELECT regexp_test_id AS ARRAYKEY, regexp_tests.* FROM regexp_tests ORDER BY outputs, regexp_test_id;');
$etos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'etos');
$arthro = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'arthro');
$ar_nomou = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'ar_nomou');
$nomos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'nomos');
$fek_tp = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'fek_tp');
$periptwsi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'periptwsi');
$paragrafos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'paragrafos');
$edafio = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'edafio');
$pd = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'pd');
$fek = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'fek');
$apofasi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'apofasi');
$diataksi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'diataksi');
$basiliko_diatagma = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'basiliko_diatagma');

function find_all_matches($results, $fall, $fname, $freplace = '') {
  if ($freplace == '')
    $freplace = '__' . $fname . '__';
    
  $ret = array();
  foreach($results as $value) {
    foreach($fall as $fall_pat) {
      $matches = array();
      $a = preg_match_all($fall_pat, $value, $matches, PREG_OFFSET_CAPTURE);
      foreach($matches[$fname] as $match) {
        $text = $match[0];
        $pos = $match[1];
        
        $new_phrase = mb_substr($value, 0, $pos);
        $new_phrase .= $freplace;
        $new_phrase .= mb_substr($value, $pos + mb_strlen($text));
        
        $ret[] = $new_phrase;
      }
    }
  }
  
  return $ret;
}


?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>References testing sciprt</title>
    <script src="jquery-1.8.1.min.js" type="text/javascript"></script>
    <style>
      i.f { background-color: #DABC90; }
      i.s { background-color: #8CD0D9; }
      i.t { background-color: #8CE0E9; }
      i.u { background-color: #8CE0D9; }
      i.v { background-color: #2CE3D9; }
    </style>
  </head>
  <body>
  <div id="results" style="padding:top: 40px;">
    <?php
      
  $value = file_get_contents("/media/Data3/FEK_txt/2010.08/ΦΕΚ B 1265 - 06.08.2010.txt");
  $already = array();
  
  foreach($fek as $fekpat) {
    $matches = array();
    $a = preg_match_all($fekpat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['fek'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['fek'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;
      
      $arithmos = $matches['arithmos'][$id][0];
      $tipos = $matches['tipos'][$id][0];
      $tipos = str_replace('΄', '', $tipos);
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/fek/{$imerominia}/{$tipos}/{$arithmos}" title="ΦΕΚ {$arithmos}/{$tipos}/{$imerominia}">[ΦΕΚ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);
    
    }
  }
  
  foreach($nomos as $nomospat) {
    $matches = array();
    $a = preg_match_all($nomospat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['nomos'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['nomos'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;
      
      $arithmos = $matches['arithmos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/nomos/{$imerominia}/{$arithmos}" title="ΝΟΜΟΣ {$arithmos}/{$imerominia}">[ΝΟΜΟΣ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);      
    }
  }
  
  foreach($pd as $pdpat) {
    $matches = array();
    $a = preg_match_all($pdpat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['pd'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['pd'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;

      $arithmos = ($matches['arithmos'][$id][0] == '') ? '??' : $matches['arithmos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/pd/{$imerominia}/{$arithmos}" title="ΠΔ {$arithmos}/{$imerominia}">[ΠΔ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);      
    
    }
  }

  foreach($apofasi as $apofasipat) {
    $matches = array();
    $a = preg_match_all($apofasipat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['apofasi'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['apofasi'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;

      $kwdikos = $matches['kwdikos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/apofasi/{$imerominia}/{$kwdikos}" title="ΑΠΟΦΑΣΗ {$kwdikos}/{$imerominia}">[ΑΠΟΦΑΣΗ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);      
    }
  }

  foreach($diataksi as $diataksipat) {
    $matches = array();
    $a = preg_match_all($diataksipat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['diataksi'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['diataksi'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;

      $tipos = $matches['tipos'][$id][0];
      $kwdikos = $matches['kwdikos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/diataksi/{$tipos}/{$imerominia}/{$kwdikos}" title="ΔΙΑΤΑΞΗ {$tipos}/{$kwdikos}/{$imerominia}">[ΔΙΑΤΑΞΗ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);      
    }
  }

  foreach($basiliko_diatagma as $basiliko_diatagmapat) {
    $matches = array();
    $a = preg_match_all($basiliko_diatagmapat, $value, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['basiliko_diatagma'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['basiliko_diatagma'] as $id => $rfek) {
      if (isset($already[md5($rfek[0])]))
        continue;
      $already[md5($rfek[0])] = 1;

      $arthro = isset($matches['arthro'][$id][0]) ? $matches['arthro'][$id][0] : '*';
      $kwdikos = ($matches['kwdikos'][$id][0] == '') ? '??' : $matches['kwdikos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/basiliko_diatagma/{$imerominia}/{$kwdikos}/{$arthro}" title="ΒΑΣΙΛΙΚΟ ΔΙΑΤΑΓΜΑ {$imerominia}/{$kwdikos}/{$arthro}">[ΒΔ]</a>
EOD;
      $value = str_replace($rfek[0], $rfek[0] . $l, $value);      
    }
  }

  $value = str_replace("\r", "", $value);
  $value = str_replace("-\n", "", $value);
  $value = str_replace("−\n", "", $value);
  // $value = str_replace("\n\n", "<BR>", $value);
  $value = str_replace("\n", "<BR>", $value);
  // $value = str_replace("\n", " ", $value);
  $value = str_replace("<BR>", "<BR>\n", $value);
    echo <<<EOD
{$value}
EOD;
          
    ?>
  </div>
  </body>
</html>
  
