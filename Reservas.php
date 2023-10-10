<?php
require_once 'Cabanas.php';
require_once 'Clientes.php';

class Reservas {
    private $numero;
    private $fechaInicio;
    private $fechaFin;
    private $cliente; // Un objeto de tipo Clientes
    private $cabana;  // Un objeto de tipo Cabanas

    public function __construct($numero, $fechaInicio, $fechaFin, $cliente, $cabana) {
        $this->numero = $numero;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->cliente = $cliente;
        $this->cabana = $cabana;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    public function getFechaFin() {
        return $this->fechaFin;
    }

    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function getCabana() {
        return $this->cabana;
    }
    public function calcularCostoTotal()
    {
        // Suponiendo que $this->cabana->getCostoDiario() te da el costo diario de la cabaña
        $dias = $this->calcularDiferenciaDias();
        $costoTotal = $dias * $this->cabana->getCostoDiario();
        return $costoTotal;
    }

    public function calcularDiferenciaDias()
    {
        $fechaInicio = new DateTime($this->fechaInicio);
        $fechaFin = new DateTime($this->fechaFin);
        $diferencia = $fechaInicio->diff($fechaFin);
        return $diferencia->days;
    }
}