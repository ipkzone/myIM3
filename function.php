<?php

class MyIM3Api
{
    private $baseUrl = "https://iddant.id/myIM3";
    private $licenseKey;
    private $authToken;

    public function __construct($licenseKey, $authToken)
    {
        $this->licenseKey = $licenseKey;
        $this->authToken  = $authToken;
    }

    private function request($endpoint, $data)
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-License-Key: {$this->licenseKey}",
            "Authorization: Bearer {$this->authToken}",
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $error    = curl_error($ch);

        curl_close($ch);

        return $error ? $error : $response;
    }


    public function sendOTP($phone)
    {
        return $this->request("/send", [
            "phone" => $phone
        ]);
    }

    public function verifyOTP($transid, $otp)
    {
        return $this->request("/verify", [
            "transid" => $transid,
            "otp"     => $otp
        ]);
    }

    public function dashboard($token)
    {
        return $this->request("/dashboard", [
            "token" => $token
        ]);
    }

    public function superHematList($token)
    {
        return $this->request("/superhemat/list", [
            "token" => $token
        ]);
    }

    public function superHematBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/superhemat", [
            "token"          => $token,
            "package_id"     => $package_id,
            "payment_method" => $payment_method
        ]);
    }

    public function plusList($token)
    {
        return $this->request("/plus/list", [
            "token" => $token
        ]);
    }

    public function plusBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/plus", [
            "token"          => $token,
            "package_id"     => $package_id,
            "payment_method" => $payment_method
        ]);
    }

    public function freedomList($token)
    {
        return $this->request("/freedom/list", [
            "token" => $token
        ]);
    }

    public function freedomBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/freedom", [
            "token"          => $token,
            "package_id"     => $package_id,
            "payment_method" => $payment_method
        ]);
    }

    public function offerList($token)
    {
        return $this->request("/offer/list", [
            "token" => $token
        ]);
    }

    public function offerBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/offer", [
            "token"          => $token,
            "package_id"     => $package_id,
            "payment_method" => $payment_method
        ]);
    }

    public function promoList($token)
    {
        return $this->request("/promo/list", [
            "token" => $token
        ]);
    }

    public function promoBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/promo", [
            "token"          => $token,
            "package_id"     => $package_id,
            "payment_method" => $payment_method
        ]);
    }
}
