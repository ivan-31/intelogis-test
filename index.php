<?php
/**
 * Клиентский код модуля расчета стоимости доставки
 */

spl_autoload_register(function ($class) {
    require_once 'deliveryServices/' . $class . '.php';
});

// Набор отправлений, произвольно
$inputData = [
    "sourceKladr" => "125000, г. Москва, Ленинский проспект, д.50А",
    "targetKladr" => "127000, г. Москва, проспект Вернадского, д.37",
    "weight" => 5.00
];

// Служба доставки (сейчас доступны: FastDeliveryService || SlowDeliveryService)
$deliveryServiceName = "FastDeliveryService";

// Создаем объект службы доставки с ее входными данными
$deliveryService = new $deliveryServiceName($inputData);

// Получаем и выдаем данные о стоимости и дате доставки
echo $deliveryService->getDeliveryPriceAndDate();

exit();
