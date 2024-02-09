<?php
class Conexion
{
    private static $instancia;
    private $conexion;

    private $host = 'peanut.db.elephantsql.com';
    private $usuario = 'qpldaohq';
    private $contrasena = 'xTQnmoKZN8eb8a5eUvm-eN0ceeCp7zk0';
    private $base_de_datos = 'qpldaohq';

    private function __construct()
    {
        try {
            // Establecer la conexión PDO
            $this->conexion = new PDO(
                "pgsql:host={$this->host};dbname={$this->base_de_datos}",
                $this->usuario,
                $this->contrasena
            );

            // Establecer la codificación de caracteres UTF-8
            $this->conexion->exec('SET client_encoding TO \'UTF8\'');

        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public static function obtenerInstancia()
    {
        if (self::$instancia == null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    public function obtenerConexion()
    {
        return $this->conexion;
    }

    // Evita que la instancia sea clonada
    private function __clone()
    {
        throw new RuntimeException('La clonación de esta instancia no está permitida.');
    }
}



