<?php
require_once 'config/GestionDataBase.php';
$fileName = $argv['1'];
$lectura  = fopen($fileName, "r");
if (!$lectura) {
    exit("Error al intentar abrir archivo $fileName");
}
$Connection = Database::getInstance();
$sql        = "DESCRIBE $ParametersConfig[table]";
$response   = $Connection->querySelectFetchAllAssoc($sql)->response;
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

$countParameters = implode(',', array_fill(0, count($lectArray[0]), '?'));
$values          = '(' . implode('),(', array_fill(0, count($lectArray), $countParameters)) . ')';
$dataInsert      = call_user_func_array('array_merge', $lectArray);
$sql             = 'INSERT INTO ' . $ParametersConfig['table'] . ' (' . implode(',', $heads) . ') VALUES ' . $values . ';';
$resultado       = $Connection->prepare($sql);
$response        = $resultado->execute($dataInsert);
if($response){
    die('Datos insertados correctamente');
}
