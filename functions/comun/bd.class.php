<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once __DIR__ . '/config.php';

class bd {

    protected $conect;
    public $usuario;
    public $empresa;

    public function __construct() {        
        $this->conect = sqlsrv_connect(DB_SERVER, array('Database' => DB_NAME, 'UID' => DB_USER, 'PWD' => DB_PASSWORD, 'CharacterSet' => 'UTF-8'));
        if (!$this->conect) {
            echo "La conexión no se pudo establecer.<br />";
            die(print_r(sqlsrv_errors(), true));
        }
        date_default_timezone_set("Europe/Madrid");

        /* iniciar las variables de sesion */
        $this->id               = $_SESSION['userid'];
        $this->usuario          = $_SESSION['username'];
        $this->empresa          = $_SESSION['company_id'];
        $this->jefeObra         = $_SESSION['id_jefe_obra'];
    }

    public function consulta($valores, $tabla, $where, $order = '', $limit = '') {
        $sql = 'SELECT ' . $valores . ' FROM ' . $tabla . ' ' . $where . ' ' . $order . ' ' . $limit;
        //error_log($sql. PHP_EOL, 3, "php-errores.log");

        $result = sqlsrv_query($this->conect, $sql);
        if ($result) {
            if (sqlsrv_has_rows($result)) {
                $data = array();
                while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function insertar($campos, $tabla, $valores) {
        $flag = false;
        $sql = 'INSERT INTO ' . $tabla . ' (' . $campos . ') VALUES (' . $valores . ')';
        $insert = sqlsrv_query($this->conect, $sql);
        if ($insert) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array('flag' => $flag, 'sql' => $sql);
    }

    public function eliminar($from, $where) {
        $flag = false;
        $sql = 'DELETE FROM ' . $from . ' ' . $where;
        $delete = sqlsrv_query($this->conect, $sql);
        if ($delete) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array('flag' => $flag);
    }

    public function modificar($valores, $tabla, $where) {
        $flag = false;
        $sql = 'UPDATE ' . $tabla . ' SET ' . $valores . ' ' . $where;
        $update = sqlsrv_query($this->conect, $sql);
        if ($update) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array('flag' => $flag, 'sql' => $sql);
    }

    public function procedimiento($nombre) {
        ini_set('memory_limit', '-1'); // sin esto no funcionarán los procedimientos más lentos
        ini_set('max_execution_time', '0'); 

        $flag = false;
        $sql = 'exec.' . $nombre;
        $ejecutar = sqlsrv_query($this->conect, $sql);
        if ($ejecutar) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array('flag' => $flag, 'sql' => $sql);
    }

    public function crearSelect($valores, $tabla, $where, $order = '', $selected = '', $mostrarId = false) {
        $sql = 'SELECT ' . $valores . ' FROM ' . $tabla . ' ' . $where . ' ' . $order;
        $result = sqlsrv_query($this->conect, $sql);
        $html = '';
        if ($result) {
            if (sqlsrv_has_rows($result)) {
                while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $fila = array();
                    $sel = '';

                    foreach ($row as $k => $v) {
                        $fila[] = $row[$k];
                    }

                    if ($fila[0] == $selected) {
                        $sel = ' selected ';
                    }

                    if ($mostrarId) {
                        $fila[1] = $fila[0] . ' - ' . $fila[1];
                    }

                    $html .= '<option value="'.$fila[0].'" '.$sel.'>'.$fila[1].'</option>';
                }
                return $html;
            } else {
                return $html;
            }
        } else {
            return $html;
        }        
    }

    public function formatoNumero($n, $cero = false, $euro = true, $decimales = 2, $sinPunto = false, $porcentaje = false) {
        if ($n == '' || $n == 0) {
            if ($cero) {
                $n = 0;
            } else {
                return '';
            }       
        }

        if (strpos($n, ',') !== false) { // si contiene una coma, transformar en punto
            $n = str_replace(',' , '.' , $n);
        }

        $e = '';
        if ($euro) {
            $e = '&nbsp;&euro;';
        }

        $p = '';
        if ($porcentaje) {
            $p = ' %';
        }

        if ($sinPunto) {
            $n = number_format($n, 2, ',', ''); // sin el punto en los miles
        } else {
            $n = number_format((float)$n, $decimales, ',', '.').$e.$p; // este redondea
        }
        
        // $n = str_replace('.', ',', bcdiv($n, 1, 2) ).$e; // este no redondea
        return $n;
    }

    public function copiar($tabla1, $valores1, $tabla2, $valores2, $where) {
        $flag = false;
        $sql = 'INSERT INTO ' . $tabla1 . ' (' . $valores1 . ') SELECT ' . $valores2 . ' FROM ' . $tabla2 . ' ' . $where;
        $insert = sqlsrv_query($this->conect, $sql);
        if ($insert) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array("flag" => $flag, 'sql' => $sql);
    }

    public function escaparComillasSimples($string) {
        if (isset($string)) {
            $string = str_replace("'", "''", $string);    
        }
        return $string;
    }

    public function escaparComillasDobles($string) {
        if (isset($string)) {
            $string = str_replace('"', '\'', $string);    
        }
        return $string;
    }

    public function desencadenador($tabla, $habilitar) {
        $flag = false;
        $trigger = 'DISABLE TRIGGER';
        if ($habilitar) {
            $trigger = 'ENABLE TRIGGER';
        }
        $sql = 'ALTER TABLE '.DB_SAGE.$tabla.' '.$trigger.' '.$tabla.'_SyncIU';
        $ejecutar = sqlsrv_query($this->conect, $sql);
        if ($ejecutar) {
            $flag = true;
        } else {
            $flag = false;
        }
        return array('flag' => $flag, 'sql' => $sql);
    }

    /*
    public function formatoFecha($fecha, $format = '') {
    
        if (strpos($fecha, '/') === false and strpos($fecha, '-') === false) {
            // fecha unida sin separadores formato 14012015
            $e[] = substr($fecha, 0, 2);
            $e[] = substr($fecha, 2, 2);
            $e[] = substr($fecha, 4, 4);
        } elseif (strpos($fecha, '-') !== false) {
            // fecha separada por guiones con formato 2015-01-14
            $tmp_date = explode('-', $fecha);
            $e[] = $tmp_date['2']; // año
            $e[] = $tmp_date['1'];
            $e[] = $tmp_date['0'];
        } else {
            // fecha tradicional con formato 14/01/2015
            $e = explode('/', $fecha);
        }
        
        if ($format == '') {
            // 03/20/2015
            $year   = $e['2'];
            $month  = $e['0'];
            $day    = $e['1'];
            $fecha = $year.'-'.$day.'-'.$month.' 00:00:00.000';
        } elseif ($format == 'es') {
            // 20/03/2015
            $day    = $e['0'];
            $month  = $e['1'];
            $year   = $e['2'];
            $fecha = $day.'-'.$month.'-'.$year.' 00:00:00.000';
        }
        
        return $fecha;
    }
    */
}
