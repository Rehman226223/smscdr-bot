<?php

$BASE_URL = "http://51.89.99.105/NumberPanel";
$COOKIE = DIR . "/cookie.txt";

/* ================= LOGIN ================= */
function login($base, $cookie) {

    $url = $base . "/signin";

    $data = [
        "username" => "Sara558",
        "password" => "Sameasyou558"
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);

    curl_exec($ch);
    curl_close($ch);
}

/* ================= FETCH ================= */
function fetch($base, $cookie) {

    $url = $base . "/agent/res/data_smscdr.php?" . http_build_query([
        "fdate1" => date("Y-m-d 00:00:00"),
        "fdate2" => date("Y-m-d 23:59:59"),
        "fg" => "0",
        "_" => time()
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Requested-With: XMLHttpRequest"
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* ================= TELEGRAM ================= */
function sendTelegram($msg) {

    $token = "YOUR_BOT_TOKEN";   // ⚠️ keep your real token here manually
    $chat_id = "-1003236767166";

    $url = "https://api.telegram.org/bot$token/sendMessage";

    file_get_contents($url . "?" . http_build_query([
        "chat_id" => $chat_id,
        "text" => $msg
    ]));
}

/* ================= RUN ================= */

login($BASE_URL, $COOKIE);

$seen = [];

while (true) {

    $data = fetch($BASE_URL, $COOKIE);
    $json = json_decode($data, true);

    if (isset($json["aaData"])) {

        foreach ($json["aaData"] as $row) {

            $msg = $row[3] ?? "";

            if (preg_match('/\b\d{4,6}\b/', $msg, $m)) {

                $otp = $m[0];

                if (!in_array($otp, $seen)) {
                    $seen[] = $otp;

                    sendTelegram("OTP: $otp\nMSG: $msg");
                }
            }
        }
    }

    sleep(10);
}