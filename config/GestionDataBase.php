<?php
include_once 'ConfigDB.php';
class Database
{
    private static $PDOInstance;
    protected $transactionCounter = 0;
    private static $instance      = null;
    public $transactionState      = 0;

    public function __construct($ParametersConfig = '')
    {
        if (!self::$PDOInstance) {
            try {
                if (empty($ParametersConfig)) {
                    global $ParametersConfig;
                }
                self::$PDOInstance = new PDO("mysql:dbname=$ParametersConfig[dbname];host=$ParametersConfig[dbhost]", $ParametersConfig['dbuser'], $ParametersConfig['dbpass']);
                self::$PDOInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
            }
        }
        return self::$PDOInstance;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            if (empty($ParametersConfig)) {
                global $ParametersConfig;
            }
            self::$instance = new Database($ParametersConfig);
        }
        return self::$instance;
    }
    public function errorInfo()
    {
        return self::$PDOInstance->errorInfo();
    }
    public function exec($statement)
    {
        return self::$PDOInstance->exec($statement);
    }

    public function getAttribute($attribute)
    {
        return self::$PDOInstance->getAttribute($attribute);
    }

    public function getAvailableDrivers()
    {
        return Self::$PDOInstance->getAvailableDrivers();
    }

    public function query($statement)
    {
        return self::$PDOInstance->query($statement);
    }

    public function quote($input, $parameter_type = 0)
    {
        return self::$PDOInstance->quote($input, $parameter_type);
    }
    public function setAttribute($attribute, $value)
    {
        return self::$PDOInstance->setAttribute($attribute, $value);
    }
    public function prepare($statement, $driver_options = false)
    {
        if (!$driver_options) {
            $driver_options = array();
        }
        return self::$PDOInstance->prepare($statement, $driver_options);
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
            if ($this->transactionCounter > 0) {
                $this->transactionStatus = false;
            }
            $this->transactionMessage[] = $message;
            $this->printQuery($message, $query, $data);
        }
        $response = !empty($response) ? $response : null;
        return (object) array('status' => $status, 'response' => $response, 'errorSQL' => $message);
    }

}
