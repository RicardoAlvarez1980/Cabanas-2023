<?php
// Función para el submenú de listados
function menuListados()
{
    $opcionesDisponibles = [
        '1' => 'listarClientes',
        '2' => 'listarCabanas',
        '3' => 'listarReservas',
        '0' => 'menuPrincipal',
    ];

    echo "==================================================\n";
    echo "Menú de Listados";
    echo "\n==================================================\n";
    echo "1. Listar Clientes\n";
    echo "2. Listar Cabañas\n";
    echo "3. Listar Reservas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    if (isset($opcionesDisponibles[$opcion])) {
        $funcion = $opcionesDisponibles[$opcion];
        call_user_func($funcion);
    } else {
        echo "Opción inválida. Intente nuevamente.\n";
        menuListados();
    }
}