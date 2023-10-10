<?php

function buscarClientes($parametroBusqueda)
{
global $clientes;

$resultados = [];

foreach ($clientes as $cliente) {
    if ($cliente->contieneNombre($parametroBusqueda)) {
        $resultados[] = $cliente;
    }
}

return $resultados;
}

?>