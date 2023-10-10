<?php
require_once 'Cabanas.php';
require_once 'Clientes.php';
require_once 'Reservas.php';

// Función para cargar reservas desde la base de datos
function cargarReservasDesdeBD()
{
    global $reservas;

    // Limpia el arreglo de reservas existente
    $reservas = [];

    // Realiza la consulta para cargar reservas desde la base de datos
    $conexion = Conexion::obtenerInstancia();
    $pdo = $conexion->obtenerConexion();
    $stmt = $pdo->query("SELECT * FROM reservas");

    // Recorre los resultados y crea instancias de Reserva
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reserva = new Reservas(
            $row['numero_reserva'],
            $row['dni_cliente'],
            $row['numero_cabana'],
            $row['fecha_inicio'],
            $row['fecha_fin']
        );
        $reservas[] = $reserva;
    }
}

// Menú de Gestionar Reservas
function menuReservas()
{
    echo "=================================";
    echo "\nMenú de Gestionar Reservas\n";
    echo "=================================\n";
    
    echo "1. Alta de Reserva\n";
    echo "2. Modificar Reserva\n";
    echo "3. Eliminar Reserva\n";
    echo "4. Listar Reservas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            altaReserva();
            menuReservas();
            break;
        case 2:
            modificarReserva();
            menuReservas();
            break;
        case 3:
            eliminarReserva();
            menuReservas();
            break;
        case 4:
            listarReservas();
            menuReservas();
            break;
        case 0:
            menuPrincipal();
            break;
        default:
            echo "Opción inválida. Intente nuevamente.\n";
            menuReservas();
            break;
    }
}
// Función para dar de alta una reserva
function altaReserva()
{
    global $reservas, $cabanas, $clientes;
    echo "\nAlta de Reserva\n";

    while (true) {
        // Solicitar datos de la reserva al usuario
        echo "Ingrese el DNI del cliente que realiza la reserva: ";
        $dniCliente = trim(fgets(STDIN));

        // Buscar el cliente por su DNI
        $clienteSeleccionado = buscarClientePorDNI($dniCliente);

        if (!$clienteSeleccionado) {
            echo "No se encontró un cliente con ese DNI. ¿Desea intentar nuevamente? (S/N): ";
            $opcion = strtoupper(trim(fgets(STDIN)));
            if ($opcion !== 'S') {
                // Devolver al menú principal
                return;
            }
        } else {
            break; // Continuar si se encontró el cliente
        }
    }

    while (true) {
        // Mostrar lista de cabañas disponibles
        echo "---------------------------------";
        echo "Cabañas Disponibles:\n";
        echo "---------------------------------\n";
        foreach ($cabanas as $cabana) {
            echo "Número: " . $cabana->getNumero() . "\n";
            echo "---------------------------\n";
        }
        echo "Ingrese el número de la cabaña a reservar: ";
        $numeroCabana = trim(fgets(STDIN));

        // Buscar la cabaña por su número
        $cabanaSeleccionada = buscarCabanaPorNumero($numeroCabana);

        if (!$cabanaSeleccionada) {
            echo "No se encontró una cabaña con ese número. ¿Desea intentar nuevamente? (S/N): ";
            $opcion = strtoupper(trim(fgets(STDIN)));
            if ($opcion !== 'S') {
                // Devolver al menú principal
                return;
            }
        } else {
            break; // Continuar si se encontró la cabaña
        }
    }

    echo "Ingrese la fecha de inicio de la reserva (formato YYYY-MM-DD): ";
    $fechaInicio = trim(fgets(STDIN));
    echo "Ingrese la fecha de fin de la reserva (formato YYYY-MM-DD): ";
    $fechaFin = trim(fgets(STDIN));

    // Crear una nueva instancia de Reservas
    $reserva = new Reservas(count($reservas) + 1, $fechaInicio, $fechaFin, $clienteSeleccionado, $cabanaSeleccionada);
    $reservas[] = $reserva;

    echo "Reserva agregada exitosamente.\n";
}

// Función para modificar una reserva
function modificarReserva()
{
    global $reservas, $cabanas, $clientes;

    echo "\nModificar Reserva\n";

    // Solicitar número de reserva a modificar
    echo "Ingrese el número de reserva que desea modificar (o deje en blanco para volver al Menú Principal): ";
    $numeroReserva = intval(trim(fgets(STDIN)));

    if (empty($numeroReserva)) {
        return; // Volver al Menú Principal si se ingresa un número de reserva en blanco
    }

    // Buscar la reserva por su número
    $reservaEncontrada = buscarReservaPorNumero($numeroReserva);

    if ($reservaEncontrada) {
        // Mostrar la información actual de la reserva
        echo "Información actual de la Reserva:\n";
        echo "Número de Reserva: " . $reservaEncontrada->getNumero() . "\n";
        echo "Fecha de Inicio: " . $reservaEncontrada->getFechaInicio() . "\n";
        echo "Fecha de Fin: " . $reservaEncontrada->getFechaFin() . "\n";
        echo "Cliente: " . $reservaEncontrada->getCliente()->getNombre() . "\n";
        echo "Cabaña: " . $reservaEncontrada->getCabana()->getNumero() . "\n";

        // Solicitar los nuevos datos al usuario
        echo "Ingrese la nueva fecha de inicio de la reserva (formato YYYY-MM-DD, deje en blanco para mantener el valor actual): ";
        $nuevaFechaInicio = trim(fgets(STDIN));
        echo "Ingrese la nueva fecha de fin de la reserva (formato YYYY-MM-DD, deje en blanco para mantener el valor actual): ";
        $nuevaFechaFin = trim(fgets(STDIN));

        // Actualizar los campos de la reserva si se ingresan nuevos valores
        if (!empty($nuevaFechaInicio)) {
            $reservaEncontrada->setFechaInicio($nuevaFechaInicio);
        }
        if (!empty($nuevaFechaFin)) {
            $reservaEncontrada->setFechaFin($nuevaFechaFin);
        }

        echo "Reserva modificada exitosamente.\n";
    } else {
        echo "No se encontró una reserva con ese número.\n";
    }
}

// Función para eliminar una reserva
function eliminarReserva()
{
    global $reservas;

    echo "\nEliminar Reserva\n";

    // Solicitar número de reserva a eliminar
    echo "Ingrese el número de reserva que desea eliminar: ";
    $numeroReserva = intval(trim(fgets(STDIN)));

    // Buscar la reserva por su número
    $reservaEncontrada = null;
    foreach ($reservas as $reserva) {
        if ($reserva->getNumero() === $numeroReserva) {
            $reservaEncontrada = $reserva;
            break;
        }
    }

    if ($reservaEncontrada) {
        // Mostrar la información completa de la reserva
        echo "Información de la Reserva:\n";
        echo "Número de Reserva: " . $reservaEncontrada->getNumero() . "\n";
        echo "Fecha de Inicio: " . $reservaEncontrada->getFechaInicio() . "\n";
        echo "Fecha de Fin: " . $reservaEncontrada->getFechaFin() . "\n";
        echo "Cliente: " . $reservaEncontrada->getCliente()->getNombre() . "\n";
        echo "Cabaña: " . $reservaEncontrada->getCabana()->getNumero() . "\n";

        // Confirmar eliminación
        echo "¿Está seguro de que desea eliminar esta reserva? (S/N): ";
        $opcion = strtoupper(trim(fgets(STDIN)));

        if ($opcion === 'S') {
            // Eliminar la reserva de la lista
            $key = array_search($reservaEncontrada, $reservas);
            if ($key !== false) {
                unset($reservas[$key]);
                echo "La reserva fue eliminada exitosamente.\n";
            } else {
                echo "No se pudo eliminar la reserva.\n";
            }
        } else {
            echo "La eliminación ha sido cancelada.\n";
        }
    } else {
        echo "No se encontró una reserva con ese número.\n";
    }
}

// Función para listar reservas con información completa de cliente y cabaña
function listarReservas()
{
    global $reservas, $clientes, $cabanas;

    echo "=================================";
    echo "\nListado de Reservas\n";
    echo "=================================\n";

    if (empty($reservas)) {
        echo "No hay reservas registradas en el sistema.\n";
    } else {
        foreach ($reservas as $reserva) {
            $cliente = $reserva->getCliente();
            $cabana = $reserva->getCabana();

            echo "Número de Reserva: " . $reserva->getNumero() . "\n";
            echo "Fecha de Inicio: " . $reserva->getFechaInicio() . "\n";
            echo "Fecha de Fin: " . $reserva->getFechaFin() . "\n";

            echo "Cliente:\n";
            echo "  DNI: " . $cliente->getDni() . "\n";
            echo "  Nombre: " . $cliente->getNombre() . "\n";
            echo "  Dirección: " . $cliente->getDireccion() . "\n";
            echo "  Teléfono: " . $cliente->getTelefono() . "\n";
            echo "  Email: " . $cliente->getEmail() . "\n";

            echo "Cabaña:\n";
            echo "  Número: " . $cabana->getNumero() . "\n";
            echo "  Capacidad: " . $cabana->getCapacidad() . "\n";
            echo "  Descripción: " . $cabana->getDescripcion() . "\n";
            echo "  Costo Diario: $" . $cabana->getCostoDiario() . "\n";

            echo "Diferencia de Días en la Reserva: " . $reserva->calcularDiferenciaDias() . " días\n";
            echo "Costo Total de la Reserva: $" . $reserva->calcularCostoTotal() . "\n";
            echo "---------------------------\n";
        }
    }
}

// Función para buscar una reserva por número
function buscarReservaPorNumero($numero)
{
    global $reservas;

    foreach ($reservas as $reserva) {
        if ($reserva->getNumero() == $numero) {
            return $reserva;
        }
    }
    return null;
}
