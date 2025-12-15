<?php

/*
- Script open source with REST API
* Silahkan di kembangkan kembali.
* Gunakan dengan bijak
*/

class MyIM3Api
{
    private $baseUrl = "https://iddant.id/myIM3";
    private $licenseKey;
    private $authToken;

    public function __construct($licenseKey, $authToken)
    {
        $this->licenseKey = $licenseKey;
        $this->authToken = $authToken;
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
        $error = curl_error($ch);

        curl_close($ch);

        return $error ? $error : $response;
    }

    public function sendOTP($phone)
    {
        return $this->request("/send", [
            "phone" => $phone,
        ]);
    }

    public function verifyOTP($transid, $otp)
    {
        return $this->request("/verify", [
            "transid" => $transid,
            "otp" => $otp,
        ]);
    }

    public function dashboard($token)
    {
        return $this->request("/dashboard", [
            "token" => $token,
        ]);
    }

    public function superHematList($token)
    {
        return $this->request("/superhemat/list", [
            "token" => $token,
        ]);
    }

    public function superHematBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/superhemat", [
            "token" => $token,
            "package_id" => $package_id,
            "payment_method" => $payment_method,
        ]);
    }

    public function plusList($token)
    {
        return $this->request("/plus/list", [
            "token" => $token,
        ]);
    }

    public function plusBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/plus", [
            "token" => $token,
            "package_id" => $package_id,
            "payment_method" => $payment_method,
        ]);
    }

    public function freedomList($token)
    {
        return $this->request("/freedom/list", [
            "token" => $token,
        ]);
    }

    public function freedomBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/freedom", [
            "token" => $token,
            "package_id" => $package_id,
            "payment_method" => $payment_method,
        ]);
    }

    public function offerList($token)
    {
        return $this->request("/offer/list", [
            "token" => $token,
        ]);
    }

    public function offerBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/offer", [
            "token" => $token,
            "package_id" => $package_id,
            "payment_method" => $payment_method,
        ]);
    }

    public function promoList($token)
    {
        return $this->request("/promo/list", [
            "token" => $token,
        ]);
    }

    public function promoBuy($token, $package_id, $payment_method = "QRIS")
    {
        return $this->request("/promo", [
            "token" => $token,
            "package_id" => $package_id,
            "payment_method" => $payment_method,
        ]);
    }
}

$LIC = "PTGV-8PQV-PRL1-T0U2";
$AUTH = "3203fccb29d361eac970918731d68255abc9c7907fba4b54f432743f754f62f3";

$api = new MyIM3Api($LIC, $AUTH);
$tokenDir = __DIR__ . "/token";
if (!is_dir($tokenDir)) {
    mkdir($tokenDir, 0777, true);
}

echo "Nomor: ";
$nomor = trim(fgets(STDIN));
$tokenFile = "$tokenDir/$nomor.txt";
function loginOTP($api, $nomor, $tokenFile)
{
    echo "Login OTP\n";
    $send = json_decode($api->sendOTP($nomor), true);
    if ($send['status'] != "0") {
        exit("Gagal kirim OTP\n");
    }

    echo "OTP: ";
    $otp = trim(fgets(STDIN));

    $ver = json_decode(
        $api->verifyOTP($send['data']['transid'], $otp),
        true
    );

    if ($ver['status'] != "0") {
        exit("OTP salah\n");
    }

    file_put_contents($tokenFile, $ver['data']['tokenid']);
    return $ver['data']['tokenid'];
}

function getToken($api, $nomor, $tokenFile)
{
    if (file_exists($tokenFile)) {
        $token = trim(file_get_contents($tokenFile));
        $test = json_decode($api->dashboard($token), true);
        if ($test['status'] === "00") {
            return $token;
        }

        unlink($tokenFile);
    }
    return loginOTP($api, $nomor, $tokenFile);
}

function choosePayment()
{
    echo "Payment:\n1) SHOPEEPAY\n2) DANA\n3) OVO\n4) GOPAY\n5) QRIS\nChoose: ";
    $map = [1 => "SHOPEEPAY", 2 => "DANA", 3 => "OVO", 4 => "GOPAY", 5 => "QRIS"];
    return $map[trim(fgets(STDIN))] ?? exit("Payment invalid\n");
}

function showPackages($list)
{
    foreach ($list['data']['packages'] as $i => $p) {
        echo "[$i] {$p['package_name']} | {$p['price_display']}\n";
    }
}

$token = getToken($api, $nomor, $tokenFile);
$d = json_decode($api->dashboard($token), true)['data'];

echo "\nNama   : {$d['fullname']}\n";
echo "Nomor  : {$d['msisdn']}\n";
echo "Saldo  : Rp {$d['balance']}\n";
echo "Expire : {$d['expires']}\n";

while (true) {

    echo "\nMENU
    1) SuperHemat
    2) Plus
    3) Freedom
    4) Offer
    5) Promo
    0) Exit
Choose: ";

    $menu = trim(fgets(STDIN));
    if ($menu == "0") {
        exit;
    }

    $map = [
        "1" => ["superHematList", "superHematBuy"],
        "2" => ["plusList", "superHematBuy"],
        "3" => ["freedomList", "superHematBuy"],
        "4" => ["offerList", "superHematBuy"],
        "5" => ["promoList", "superHematBuy"],
    ];

    if (!isset($map[$menu])) {
        continue;
    }

    [$listFn, $buyFn] = $map[$menu];
    $list = json_decode($api->$listFn($token), true);
    if ($list['status'] != "00") {
        continue;
    }

    showPackages($list);

    echo "Choose package: ";
    $idx = trim(fgets(STDIN));
    $id = $list['data']['packages'][$idx]['package_id'] ?? null;
    if (!$id) {
        continue;
    }

    $pay = choosePayment();
    echo $api->$buyFn($token, $id, $pay) . "\n";
}
