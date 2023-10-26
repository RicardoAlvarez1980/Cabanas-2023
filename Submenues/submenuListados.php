<?php
// Función para el submenú de listados
function menuListados()
{
    echo "==================================================\n";
    echo "Menú de Listados";
    echo "\n==================================================\n";

    echo "1. Listar Clientes\n";
    echo "2. Listar Cabañas\n";
    echo "3. Listar Reservas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            listarClientes();
            menuListados();
            break;
        case 2:
            listarCabanas();
            menuListados();
            break;
        case 3:
            listarReservas();
            menuListados();
            break;
        case 0:
            menuPrincipal();
            break;
        default:
            echo "Opción inválida. Intente nuevamente.\n";
            menuListados();
            break;
    }
}
