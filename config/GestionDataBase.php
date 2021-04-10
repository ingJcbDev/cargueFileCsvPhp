<?php
include_once 'ConfigDb.php';
class GestionDataBase
{
    private static $instance;

    public function __construct()
    {
        if (!self::$instance) {
            try {
                global $ParametersConfig;
                self::$instance = new PDO("mysql:dbname=$ParametersConfig[db];host=$ParametersConfig[host]", $ParametersConfig['user'], $ParametersConfig['pass']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
            }
        }
        return self::$instance;
    }

    public function errorInfo()
    {
        return self::$instance->errorInfo();
    }
    public function exec($statement)
    {
        return self::$instance->exec($statement);
    }

    public function getAttribute($attribute)
    {
        return self::$instance->getAttribute($attribute);
    }

    public function getAvailableDrivers()
    {
        return Self::$instance->getAvailableDrivers();
    }

    public function query($statement)
    {
        return self::$instance->query($statement);
    }

    public function quote($input, $parameter_type = 0)
    {
        return self::$instance->quote($input, $parameter_type);
    }
    public function setAttribute($attribute, $value)
    {
        return self::$instance->setAttribute($attribute, $value);
    }
    public function prepare($statement, $driver_options = false)
    {
        if (!$driver_options) {
            $driver_options = array();
        }
        return self::$instance->prepare($statement, $driver_options);
    }

    public function querySelectFetchAllAssoc($query, $data = null)
    {
        $status   = true;
        $response = false;
        $message  = '';
        try {
            $resultado = $this->prepare($query);
            $resultado->execute($data);
            $response = $resultado->fetchAll(PDO::FETCH_ASSOC);
            if ($response === false) {
                $response = array();
            }
        } catch (PDOException $exc) {
            $status  = false;
            $message = utf8_encode($exc->getMessage());            
        }
        $response = !empty($response) ? $response : null;
        return (object) array('status' => $status, 'response' => $response, 'errorSQL' => $message);
    }

    public function insertMultiArray($nameTable,$lectArray,$heads)
    {
        $countParameters = implode(',', array_fill(0, count($lectArray[0]), '?'));
        $values          = '(' . implode('),(', array_fill(0, count($lectArray), $countParameters)) . ')';
        $dataInsert      = call_user_func_array('array_merge', $lectArray);
        $sql             = 'INSERT INTO ' . $nameTable . ' (' . implode(',', $heads) . ') VALUES ' . $values . ';';
        $resultado       = $this->prepare($sql);
        return $resultado->execute($dataInsert);
    }

}
