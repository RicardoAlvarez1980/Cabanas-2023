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
    /**
     * Calcula el costo total de la reserva.
     *
     * @return float Costo total de la reserva.
     */
    public function calcularCostoTotal()
    {
        // Verificar si $this->cabana es un objeto válido de la clase Cabanas
        if ($this->cabana instanceof Cabanas) {
            // Suponiendo que $this->cabana->getCostoDiario() te da el costo diario de la cabaña
            $dias = $this->calcularDiferenciaDias();
            $costoTotal = $dias * $this->cabana->getCostoDiario();
            return $costoTotal;
        } else {
            // Manejar el caso en que $this->cabana no sea un objeto válido
            return 0; // O cualquier otro valor apropiado en este caso
        }
    }

    /**
     * Calcula la diferencia en días entre la fecha de inicio y la fecha de fin de la reserva.
     *
     * @return int Número de días de diferencia.
     */
    public function calcularDiferenciaDias()
    {
        $fechaInicio = new DateTime($this->fechaInicio);
        $fechaFin = new DateTime($this->fechaFin);
        $diferencia = $fechaInicio->diff($fechaFin);
        return $diferencia->days;
    }
}