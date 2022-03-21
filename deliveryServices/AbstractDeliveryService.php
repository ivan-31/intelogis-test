<?php

/**
 * Class AbstractDeliveryService
 *
 * Абстрактный класс, обеспечивающий для наследников с помощью полиморфизма использование разных служб доставки (транспортных компаний).
 * У каждой службы могут быть свои входные данные. При необходимости, общие данные и методы можно прописать
 * непосредственно в этом абстрактном классе.
 */
abstract class AbstractDeliveryService
{
    protected $price = 0.00;
    protected $date = "";
    protected $error = "";

    abstract public function getDeliveryPriceAndDate();
}