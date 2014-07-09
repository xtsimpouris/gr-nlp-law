<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php

error_reporting(E_ALL & ~E_NOTICE);
@apache_setenv('no-gzip', 1);

function grstrtoupper($string) {
  $latin_check = '/[\x{0030}-\x{007f}]/u';

  if (preg_match($latin_check, $string)) {
    $string = strtoupper($string);
  }

  $letters = array('α', 'β', 'β', 'B', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω');
  $letters_accent = array('ά', 'έ', 'ή', 'ί', 'ό', 'ύ', 'ώ');
  $letters_upper_accent = array('Ά', 'Έ', 'Ή', 'Ί', 'Ό', 'Ύ', 'Ώ');
  $letters_upper_solvents = array('ϊ', 'ϋ');
  $letters_other 							= array('ς');

  $letters_to_uppercase = array('Α', 'Β', 'Β', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
  $letters_accent_to_uppercase 			= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
  $letters_upper_accent_to_uppercase 		= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
  $letters_upper_solvents_to_uppercase 	= array('Ι', 'Υ');
  $letters_other_to_uppercase 			= array('Σ');

  $lowercase = array_merge($letters, $letters_accent, $letters_upper_accent, $letters_upper_solvents, $letters_other);
  $uppercase = array_merge($letters_to_uppercase, $letters_accent_to_uppercase, $letters_upper_accent_to_uppercase, $letters_upper_solvents_to_uppercase, $letters_other_to_uppercase);

  $uppecase_string = str_replace($lowercase, $uppercase, $string);

  return $uppecase_string;

}


$folder = "/media/Data3/FEK_txt/";
$data = grstrtoupper(file_get_contents($folder . "all_connections.txt"));
$lines = explode("\n", $data);
unset($data);

echo count($lines);

function j1(&$lines) {
  $to = array();
  foreach($lines as $line) {
    if (trim($line) == '')
      continue;
    
    $parts = explode("\t", $line);
    
    if (!isset($to[ $parts[2] ]))
      $to[ $parts[2] ] = 0;
    
    $to[ $parts[2] ] += 1;
    unset($parts);
  }
  arsort($to);
  
  return $to;
}

$to = j1($lines);
$get = array();
$neibs = array();
$neibs2 = array();

$exported = array();

/*
// Keep first ones
$keep = 1;
ini_set('max_execution_time', 30);
foreach($to as $fid => $num) {
  if (count($get) >= $keep)
    break;
  
  $get[$fid] = $num;
}
*/

$keys = array_keys($to);
$whichtokeep = $keys[count($keys) / 100];
$get[ $whichtokeep ] = $to[ $whichtokeep ];

print_r($get);

ini_set('max_execution_time', 30);
foreach($lines as $line) {
  if (trim($line) == '')
    continue;
  
  $parts = explode("\t", $line);
  if (isset($get[$parts[0]]) || isset($get[$parts[2]])) {
    $neibs[$parts[0]] = 1;
    $neibs[$parts[2]] = 1;
  }
  unset($parts);
}

ini_set('max_execution_time', 30);
foreach($lines as $line) {
  if (trim($line) == '')
    continue;
  
  $parts = explode("\t", $line);
  if (isset($get[$parts[0]]) || isset($get[$parts[2]]) || isset($neibs[$parts[0]]) || isset($neibs[$parts[2]]) )
    $exported[] = $line;
  
  unset($parts);
}

$fout = fopen($folder . "all_connections_filtered.txt", "w");
fwrite($fout, implode("\n", $exported) . "\n");
fclose($fout);

?>

  </body>
</html>
