<?php
require_once __DIR__ . '/../app/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$server   = "147.182.237.110";
$port     = 1883;
$clientId = "serverApi";
$username = 'ChapaLEMP013';
$password = 'lempCHAPA0232.3s';


$mqtt = new MqttClient($server, $port, $clientId);
$connectingSettings = (new ConnectionSettings())
    ->setUsername($username)
    ->setPassword($password)
    ->setKeepAliveInterval(60);

$mqtt = new MqttClient($server, $port, $clientId);
$mqtt->connect($connectingSettings, true);
$payload = array(
    'protocol' => 'tcp',
    'date' => date('Y-m-d H:i:s'),
    'url' => 'https://github.com/emqx/MQTT-Client-Examples'
);

$mqtt->publish(
    // topic
    'emqx/test',
    // payload
    json_encode($payload),
    // qos
    0,
    // retain
    true
);
$mqtt->disconnect();
