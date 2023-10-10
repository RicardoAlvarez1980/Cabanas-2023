<?php
class Cabanas {
    private $numero;
    private $capacidad;
    private $descripcion;
    private $costoDiario;

    public function __construct($numero, $capacidad, $descripcion, $costoDiario) {
        $this->numero = $numero;
        $this->capacidad = $capacidad;
        $this->descripcion = $descripcion;
        $this->costoDiario = $costoDiario;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getCapacidad() {
        return $this->capacidad;
    }

    public function setCapacidad($capacidad) {
        $this->capacidad = $capacidad;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getCostoDiario() {
        return $this->costoDiario;
    }

    public function setCostoDiario($costoDiario) {
        $this->costoDiario = $costoDiario;
    }
}