<?php

/**
 * Служба быстрой доставки
 *
 * Class FastDeliveryService
 */
class FastDeliveryService extends AbstractDeliveryService
{
    private $sourceKladr;
    private $targetKladr;
    private $weight;
    private $deadlineTime = 18;

    /**
     * FastDeliveryService constructor.
     *
     * @param array $inputData
     */
    public function __construct(array $inputData)
    {
        $this->sourceKladr = (!empty($inputData['sourceKladr']) && is_string($inputData['sourceKladr'])) ? $inputData['sourceKladr'] : '';
        $this->targetKladr = (!empty($inputData['targetKladr']) && is_string($inputData['targetKladr'])) ? $inputData['targetKladr'] : '';
        $this->weight = (!empty($inputData['weight']) && is_float($inputData['weight'])) ? $inputData['weight'] : 0;
    }

    /**
     * Основной публичный метод получения цены и даты доставки
     *
     * @return string (json)
     */
    public function getDeliveryPriceAndDate() : string
    {
        if ($this->verifyRequest()) {
            $transportCompanyResponse = $this->requestTransportCompany();
            if ($this->verifyResponse($transportCompanyResponse)) {
                return $this->formResponse($transportCompanyResponse);
            }
        }
        // В случае неправильного запроса или ответа отдаем заглушку по умолчанию с ошибкой
        return json_encode([
            'price' => $this->price,
            'date'  => $this->date,
            'error' => $this->error
        ]);
    }

    /**
     * Эмуляция запроса в транспортную компанию
     *
     * @return (string) json
     */
    private function requestTransportCompany() : string
    {
        return json_encode([
            'price' => 100.50,
            'period'  => 2,
            'error' => ''
        ]);
    }

    /**
     * Формирование ответа в унифицированном виде
     *
     * @param string $transportCompanyResponse
     * @return string
     */
    private function formResponse(string $transportCompanyResponse) : string
    {
        $responseArr = json_decode($transportCompanyResponse, true);
        $date = date('Y-m-d', strtotime("+ {$responseArr['period']} day"));
        return json_encode([
            'price' => $responseArr['price'],
            'date'  => $date,
            'error' => $this->error
        ]);
    }

    /**
     * Верификация входящих (пользовательских) данных
     *
     * @return bool
     */
    private function verifyRequest() : bool
    {
        if (empty($this->sourceKladr) || empty($this->targetKladr) || empty($this->weight)) {
            $this->error = 'Неверные входящие данные';
            return false;
        } elseif ((int)date("H") >= $this->deadlineTime) {
            $this->error = 'После 18.00 заявки не принимаются';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Верификация ответа транспортной компании
     *
     * @param $transportCompanyResponse (тип заранее неизвестен)
     * @return bool
     */
    private function verifyResponse($transportCompanyResponse) : bool
    {
        if (empty($transportCompanyResponse)) {
            $this->error = 'Нет ответа от транспортной компании';
            return false;
        } else {
            $responseArr = json_decode($transportCompanyResponse, true);
            if (!is_array($responseArr)
                || !isset($responseArr['price'])
                || !isset($responseArr['period'])
                || !(is_float($responseArr['price']) || is_int($responseArr['price']))
                || !is_int($responseArr['period'])) {
                $this->error = 'Неверный формат ответа транспортной компании';
                return false;
            }
        }
        return true;
    }
}
