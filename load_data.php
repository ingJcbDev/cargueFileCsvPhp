<?php
require_once 'config/GestionDataBase.php';
$fileName = $argv['1'];
$lectura  = fopen($fileName, "r");
if (!$lectura) {
    exit("Error al intentar abrir archivo $fileName");
}
$connection = new GestionDataBase();
$sql        = "DESCRIBE $ParametersConfig[table]";
$response   = $connection->querySelectFetchAllAssoc($sql)->response;
$heads      = array();
foreach ($response as $key => $value) {
    $heads[] = $value['Field'];
}
$count0 = count($heads);
        
$lectArray = array();
$i         = 0;
$count1 = 0;
while (($lect = fgetcsv($lectura)) !== false) {
    if ($i !== 0) { 
        $count1 = count(explode(';', $lect['0']));
        $lectArray[] = explode(';', $lect['0']);
    }
    $i++;
}
fclose($lectura);

if($count0 !== $count1){
    die('Los campos suministrados no corresponden campos que son necesarios para realizar la transacion');
}

$response = $connection->insertMultiArray($ParametersConfig['table'],$lectArray,$heads);
if($response){
    die('Datos insertados correctamente');
}
