<?php

// Arreglos para almacenar cabañas, clientes y reservas
$cabanas = [];
$clientes = [];
$reservas = [];

require_once './Submenues/submenuCliente.php';
require_once './Submenues/submenuCabana.php';
require_once './Submenues/submenuReserva.php';
require_once './Conexion.php';

// Crear una instancia de la clase Conexion para establecer la conexión a la base de datos
$conexion = Conexion::obtenerInstancia();

// Mostrar el mensaje de conexión exitosa
echo "Conexión a la base de datos establecida con éxito.\n";

// Menú principal
function menuPrincipal()
{
    // Cargar datos desde la base de datos
    cargarClientesDesdeBD();
    cargarCabanasDesdeBD();
    cargarReservasDesdeBD();

    echo "==================================================\n";
    echo "Bienvenido a CabinManager, su gestor de reservas!";
    echo "\n==================================================\n";
    echo "-- Menú Principal --";
    echo "\n--------------------\n";
    echo "1. Gestionar Clientes\n";
    echo "2. Gestionar Cabañas\n";
    echo "3. Gestionar Reservas\n";
    echo "4. Buscar Clientes por Nombre\n";
    echo "5. Listados\n";
    echo "0. Salir\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            require_once './Submenues/submenuCliente.php';
            menuClientes();
            break;
        case 2:
            require_once './Submenues/submenuCabana.php';
            menuCabanas();
            break;
        case 3:
            require_once './Submenues/submenuReserva.php';
            menuReservas();
            break;
        case 4:
            buscarClientesPorNombre();
            menuPrincipal();
            break;
        case 5:
            require_once './Submenues/submenuListados.php';
            menuListados();
            break;
        case 0:
            echo "Hasta luego.\n";
            exit;
        default:
            echo "Opción inválida. Intente nuevamente.\n";
            menuPrincipal();
            break;
    }
}

// Función para leer una opción del usuario
function leerOpcion($mensaje)
{
    echo $mensaje;
    return trim(fgets(STDIN));
}
// Iniciar el programa
menuPrincipal();
