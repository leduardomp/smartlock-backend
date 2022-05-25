<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

require_once("claves.php");

$mqtt = new MqttClient($server, $port, $clientId);
$connectingSettings = (new ConnectionSettings())
    ->setUsername($username)
    ->setPassword($password)
    ->setKeepAliveInterval(60);

$mqtt->connect($connectingSettings, true);