<?php
session_start();

// Set the API endpoint URL
$api_url = 'https://aalyan.hertzai.in/api/create-order';

// Validate amount from form submission
$amount = isset($_POST['amount']) ? $_POST['amount'] : null;

if (!in_array($amount, ['50', '90', '140', '200', '500'])) {  
    die('Invalid amount'); // Handle invalid amount scenario
}

// Define other required data
$customer_mobile = '0987654321';
$user_token = 'd65a71049851bbfeea313261c619f745'; // Updated API token
$order_id = uniqid('order_', true); // Generate unique order ID

// Determine redirect URL and remark1 based on amount
switch ($amount) {
    case '50':
        $_SESSION['signup'] = "plexus";
        $redirectUrl = 'Trailkey.php';
        $remark1 = '2 Hours key';
        break;
    case '90':
        $_SESSION['signup'] = "plexus";
        $redirectUrl = '1daykey.php';
        $remark1 = '1 Day key';
        break;
    case '140':
        $_SESSION['signup'] = "plexus";
        $redirectUrl = '3daykey.php';
        $remark1 = '3 Day key';
        break;
    case '200':
        $_SESSION['signup'] = "plexus";
        $redirectUrl = '7daykey.php';
        $remark1 = '7 Day key';
        break;
    case '500':
        $_SESSION['signup'] = "plexus";
        $redirectUrl = '30daykey.php';
        $remark1 = '30 Day key';
        break;
    default:
        die('Invalid amount');
}

// Prepare payload data
$data = array(
    'customer_mobile' => $customer_mobile,
    'user_token' => $user_token,
    'amount' => $amount,
    'order_id' => $order_id,
    'redirect_url' => 'https://paid.aalyan.za.com/' . $redirectUrl,
    'remark1' => $remark1,
    'remark2' => 'testremark2',
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Encode the data as form-urlencoded

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
    // You may log the error for debugging purposes
} else {
    // Parse the JSON response
    $result = json_decode($response, true);

    // Check if the status is true or false
    if ($result && isset($result['status'])) {
        if ($result['status'] === true) {
            // Order was created successfully
            $orderId = htmlspecialchars($result['result']['orderId']);
            $paymentUrl = htmlspecialchars($result['result']['payment_url']);
            
            // JavaScript redirection
            echo "<script>window.location.href = '$paymentUrl';</script>";
        } else {
            // Handle API response error
            echo 'Status: ' . htmlspecialchars($result['status']) . '<br>';
            echo 'Message: ' . htmlspecialchars($result['message']);
        }
    } else {
        // Invalid response
        echo 'Invalid API response';
    }
}

// Close cURL session
curl_close($ch);
?>