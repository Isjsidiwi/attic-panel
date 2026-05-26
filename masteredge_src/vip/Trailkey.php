<?php
session_start();
// Database connection
include 'auth.php';
include 'info.php';

// Function to generate a random alphanumeric string
function random_string($type = 'alnum', $len = 5) {
    switch ($type) {
        case 'alnum':
        default:
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
    }

    return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
}

// Function to generate a static username
function generate_username() {
    return "DESHI";
}

// Insert into database
if (isset($_SESSION['signup'])) {
    session_destroy();
    $username = generate_username();
    $duration = 2; // Key time
    $random_str = random_string('alnum', 5);
    $user_key = $username . '-' . $duration . '-' . $random_str;
    $max_devices = 1;

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO keys_code (user_key, duration, game, max_devices) VALUES (?, ?, 'PUBG', ?)");
    $stmt->bind_param("sii", $user_key, $duration, $max_devices);
    $stmt->execute();
    $stmt->close();
    
    // HTML output with improved styling
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Key</title>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f2f2f2;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                padding: 30px;
                background-color: #ffffff;
                border: 4px solid #007bff;
                border-radius: 9px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            .container h2 {
                color: #007bff;
                font-size: 24px;
                margin-bottom: 20px;
                text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
            }
            .button {
                display: inline-block;
                background-color: #007bff;
                color: #fff;
                padding: 18px 24px;
                margin: 10px;
                border: none;
                cursor: pointer;
                text-decoration: none;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease;
            }
            .button:hover {
                background-color: #005b8b;
            }
            input[type=text] {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border: 2px dashed #007bff;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 18px;
                font-weight: bold;
                color: #007bff;
                text-align: center;
                background-color: #f2f2f2;
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            }
            hr {
                border: 2px solid #f1f1f1;
                margin: 20px 0;
            }
            .message {
                color: #007bff;
                font-weight: bold;
                margin-top: 10px;
            }
            
            .footer {
                background-color: #007bff;
                color: white;
                padding: 10px 0;
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
            }
            .footer a {
                color: #ffffff;
                text-decoration: none;
                margin: 0 10px;
                font-weight: bold;
            }
        </style>
        <script>
            function copyUsername() {
                var UsernameText = document.getElementById("user_key");
                UsernameText.select();
                document.execCommand("copy");
                document.getElementById("copyMessage").innerText = "Key copied to clipboard";
            }

   
    function downloadUsername() {
        var UsernameText = document.getElementById("user_key").value;
        var blob = new Blob([UsernameText], { type: "text/plain;charset=utf-8" });
        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "vipkey.txt"; // Set the desired filename here
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

            window.addEventListener("beforeunload", function (e) {
                var confirmationMessage = "Please copy or download your key before leaving the page.";
                e.preventDefault();
                e.returnValue = confirmationMessage;
                return confirmationMessage;
            });
        </script>
    </head>
        <body>
        <div class="container">
                        <h2>' . htmlspecialchars($pageTitle) . '</h2>
            <input type="text" id="user_key" value="' . htmlspecialchars($user_key) . '" readonly>
            <div id="copyMessage"></div>
            <button type="button" class="button" onclick="copyUsername()">COPY YOUR KEY 🔑</button>
            <button type="button" class="button" onclick="downloadUsername()">DOWNLOAD YOUR KEY 🔑</button>
            <hr>
            <p class="message">Please ensure you copy or download your key before leaving this page.</p>
            <a href="' . htmlspecialchars($contactAdminUrl) . '" class="button">Contact Admin</a>
            <a href="' . htmlspecialchars($joinTelegramChannelUrl) . '" class="button">Join Telegram Channel</a>
        </div>

        <div class="footer">
            <p style="margin-bottom: 0;">JOIN TELEGRAM FOR LATEST UPDATE HACK & FEEDBACK</p>
            <a href="' . htmlspecialchars($contactAdminUrl) . '">Contact Admin</a>
            <a href="' . htmlspecialchars($joinTelegramChannelUrl) . '">Join Telegram Channel</a>
        </div>
        
    </body>
    </html>';
} else {
    echo "<script>location='error.php';</script>";
}
?>