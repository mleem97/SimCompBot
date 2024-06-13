<?php

class TelegramBot
{

    public function __construct()
    {
    }
    private $chat = "";
    private $bot = "";

    public function sendMessage($text)
    {
        if ($this->chat == null || $this->bot == null) {
            return;
        }
        $text = urlencode($text);
        $url = "https://api.telegram.org/{$this->bot}/sendMessage?chat_id={$this->chat}&text={$text}";
        $this->do($url, 'POST');
    }
    public function quietMessage($text)
    {
        if ($this->chat == null || $this->bot == null) {
            return;
        }
        $text = urlencode($text);
        $url = "https://api.telegram.org/{$this->bot}/sendMessage?chat_id={$this->chat}&text={$text}&disable_notification=true";
        $this->do($url, 'POST');
    }
    private function do($url, $method)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);

        return $response;
    }
}
