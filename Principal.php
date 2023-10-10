<?php
require_once 'Cabanas.php';
require_once 'Clientes.php';
require_once 'Reservas.php';


/*
// Crear instancias de las clases
$cabana = new Cabanas(1, 4, 'Cabaña A', 100.0); // Ejemplo de cabaña
$cliente = new Clientes(123456789, 'Juan Pérez', '123 Calle Principal', '555-1234', 'juan@example.com'); // Ejemplo de cliente
$reserva = new Reservas(1, '2023-10-15', '2023-10-20', $cliente, $cabana); // Ejemplo de reserva

// Imprimir información por consola
echo "Información del Cliente:\n";
echo "DNI: " . $cliente->getDni() . "\n";
echo "Nombre: " . $cliente->getNombre() . "\n";
echo "Dirección: " . $cliente->getDireccion() . "\n";
echo "Teléfono: " . $cliente->getTelefono() . "\n";
echo "Email: " . $cliente->getEmail() . "\n";

echo "\nInformación de la Cabaña:\n";
echo "Número: " . $cabana->getNumero() . "\n";
echo "Capacidad: " . $cabana->getCapacidad() . "\n";
echo "Descripción: " . $cabana->getDescripcion() . "\n";
echo "Costo Diario: $" . $cabana->getCostoDiario() . "\n";

echo "\nInformación de la Reserva:\n";
echo "Número de Reserva: " . $reserva->getNumero() . "\n";
echo "Fecha de Inicio: " . $reserva->getFechaInicio() . "\n";
echo "Fecha de Fin: " . $reserva->getFechaFin() . "\n";
echo "Cliente: " . $reserva->getCliente()->getNombre() . "\n";
echo "Cabaña: " . $reserva->getCabana()->getNumero() . "\n";
echo "Diferencia de Días en la Reserva: " . $reserva->calcularDiferenciaDias() . " días\n";
echo "Costo Total de la Reserva: $" . $reserva->calcularCostoTotal() . "\n";
*/