<?php
require_once './Submenues/submenuCliente.php';
require_once './Submenues/submenuCabana.php';
require_once './Submenues/submenuReserva.php';
require_once './Conexion.php';

//HECHOS:
//1 - Sería Proteger usando integridad referencial.
//    Al eliminar reservas se elimina cliente y cabaña asociados a la misma (Evitar esto)
//2 - Validar fechas. Mostrarlas e ingresarlas en el formato nuestro. 
//3 - Dadas las fechas de reserva, muestre las cabañas disponibles. (modificar altareserva())
//POR HACER:
//    Buscar cabañas libres dada una fecha en particular (nueva funcion).FUNCION YA HECHA.FALTA IMPLEMENTARLA CORRECTAMENTE EN EL MENU

// Arreglos para almacenar cabañas, clientes y reservas
$cabanas = [];
$clientes = [];
$reservas = [];

// Crear una instancia de la clase Conexion para establecer la conexión a la base de datos
$conexion = Conexion::obtenerInstancia();

// Mostrar el mensaje de conexión exitosa
echo "Conexión a la base de datos establecida con éxito.\n";

// Menú principal
function menuPrincipal()
{
    cargarDatosDesdeBD();

    echo "==================================================\n";
    echo "Bienvenido a CabinManager, su gestor de reservas!";
    echo "\n==================================================\n";
    echo "-- Menú Principal --";
    echo "\n--------------------\n";
    echo "1. Gestionar Clientes\n";
    echo "2. Gestionar Cabañas\n";
    echo "3. Gestionar Reservas\n";
    echo "4. Buscar Clientes por Nombre\n";
    echo "5. Busqueda de Cabañas\n";
    echo "0. Salir\n";

    $opcion = leerOpcion("Seleccione una opción: ");
    
    $opcionesDisponibles = [
        '1' => 'menuClientes',
        '2' => 'menuCabanas',
        '3' => 'menuReservas',
        '4' => 'buscarClientesPorNombre',
        '5' => 'buscarCabana',
        '0' => 'salir',
    ];

    if (isset($opcionesDisponibles[$opcion])) {
        $funcion = $opcionesDisponibles[$opcion];
        call_user_func($funcion);
        menuPrincipal();
    } else {
        echo "Opción inválida. Intente nuevamente.\n";
        menuPrincipal();
    }
}

// Cargar datos desde la base de datos
function cargarDatosDesdeBD()
{
    cargarClientesDesdeBD();
    cargarCabanasDesdeBD();
    cargarReservasDesdeBD();
}

// Función para salir del programa
function salir()
{
    echo "Hasta luego.\n";
    exit;
}
// Función para leer una opción del usuario
function leerOpcion($mensaje)
{
    echo $mensaje;
    return trim(fgets(STDIN));
}
// Iniciar el programa
menuPrincipal();