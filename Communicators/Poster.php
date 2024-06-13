<?php
include_once 'Communicators/Curler.php';

class Poster extends Curler
{

    public function __construct($csrf, $sessionid)
    {
        parent::__construct($csrf, $sessionid);
    }

    function buy($quality, $quantity, $id)
    {
        $url = 'https://www.simcompanies.com/api/v2/market-order/take/';
        $content = [
            'quality' => $quality,
            'quantity' => $quantity,
            'resource' => $id,
        ];
        $json = json_encode($content);
        $response = $this->post($url, $json);
        echo "\033[35mP\033[39m buying {$quantity} of kind: {$id}\n";
        return $response;
    }

    function sell($kind, $price, $quality, $quantity, $resid)
    {
        $url = 'https://www.simcompanies.com/api/v2/market-order/';
        $quantity = strval($quantity);
        $content = [
            'kind' => $kind,
            'price' => $price,
            'quality' => $quality,
            'quantity' => $quantity,
            'resourceId' => $resid,
        ];
        $json = json_encode($content);
        $response = $this->post($url, $json);
        echo "\033[35mP\033[39m selling {$quantity} of kind: {$kind}\n";
        return $response;
    }
    function build($kind, $position)
    {
        $url = 'https://www.simcompanies.com/api/v2/companies/me/buildings/';
        $content = [
            'kind' => $kind,
            'position' => $position,
        ];
        $json = json_encode($content);
        $response = $this->post($url, $json);
        echo "\033[35mP\033[39m new building of kind: {$kind}\n";
        return $response;
    }
    function rush($id)
    {
        $url = "https://www.simcompanies.com/api/v1/rush/{$id}/";
        $content = null;
        $response = $this->post($url, $content);
        echo "\033[35mP\033[39m rushed building with id: {$id}\n";
        return $response;
    }
    function work($id, $amount, $kind, $price)
    {
        $url = "https://www.simcompanies.com/api/v1/buildings/{$id}/busy/";
        $content = [
            'amount' => $amount,
            'kind' => $kind,
            'price' => $price,
        ];
        $json = json_encode($content);
        $response = $this->post($url, $json);
        echo "\033[35mP\033[39m selling {$amount} of kind: {$kind} at store for {$price}\n";
        return $response;
    }
    function contract($recipient, $kind, $price, $quality, $quantity, $resid)
    {
        $url = 'https://www.simcompanies.com/api/v2/market-order/';
        $quantity = strval($quantity);
        $content = [
            'contractTo' => $recipient,
            'kind' => $kind,
            'price' => $price,
            'quality' => $quality,
            'quantity' => $quantity,
            'resourceId' => $resid,
        ];
        $json = json_encode($content);
        $response = $this->post($url, $json);
        echo "\033[35mP\033[39m sent contract over {$quantity} of kind: {$kind} to {$recipient}\n";
        return $response;
    }
}
