<?php
// Función para el submenú de listados
function menuListados()
{
    echo "\nMenú de Listados\n";
    echo "1. Listar Clientes\n";
    echo "2. Listar Cabañas\n";
    echo "3. Listar Reservas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            require_once './Submenues/submenuCliente.php'; 
            listarClientes();
            menuListados();
            break;
        case 2:
            require_once './Submenues/submenuCabana.php';
            listarCabanas();
            menuListados();
            break;
        case 3:
            require_once './Submenues/submenuReserva.php';
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
