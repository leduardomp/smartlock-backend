<?php

// JSON Format Converter Function
function message($content, $status)
{
    return json_encode(['message' => $content, 'error' => $status]);
}

// JSON Format Converter Function
function messageAlexa($content, $status, $code)
{
    return json_encode(['message' => $content, 'error' => $status, 'code' => $code]);
}

//funcion validar
function vigenciaToken($fechaExpireToken)
{
    $fechaActual = strtotime(date('d-m-Y H:i:s'));
    $fechaToken = strtotime($fechaExpireToken);
    $fechaToken > $fechaActual ? $valor = true : $valor = false;

    return $valor;
}

function write_log($log_msg)
{
    $log_filename = "logs";
    if (!file_exists($log_filename)) {
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/debug.log';
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

function test_input($data)
{
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    $data = trim($data);
    return $data;
}
