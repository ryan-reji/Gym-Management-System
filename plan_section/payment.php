<?php
session_start();
include "../Login/db_config.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login/index.php?error=Please log in first");
    exit();
}

// Fetch user details from the session
$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$Firstname = $_SESSION['FirstName'];
$Secondname = $_SESSION['LastName'];
$email = $_SESSION['email'];
$mobno = $_SESSION['number'];

// Get plan and price from query string
$plan = isset($_GET['plan']) ? $_GET['plan'] : '';
$price_per_month = isset($_GET['price']) ? $_GET['price'] : 0;

$selected_plan_id = 0; // Default value

if ($plan == 'Day-Pass') {
    $selected_plan_id = 1;
} elseif ($plan == 'Standard') {
    $selected_plan_id = 2;
} elseif ($plan == 'Premium') {
    $selected_plan_id = 3;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add debug logging
    error_log("POST data: " . print_r($_POST, true));
    
    $plan_id = $_POST['plan_id'];
    $enum_duration = $_POST['enum_duration']; // ENUM value
    $duration_months = intval($_POST['duration_months']); // Numeric duration in months
    
    error_log("Duration months: $duration_months");

    // Safely handle missing values
    $price_per_month = isset($_POST['price_per_month']) ? floatval($_POST['price_per_month']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    // Validate values
    if ($price_per_month === 0 || $amount === 0) {
        die("Invalid pricing information. Please try again.");
    }

    // Calculate start and end dates
    $start_date = date('Y-m-d');
    
    // Make sure duration_months is a valid number
    if ($duration_months > 0) {
        $end_date = date('Y-m-d', strtotime("+$duration_months months"));
    } else {
        // Default to 1 month if something goes wrong
        $end_date = date('Y-m-d', strtotime("+1 month"));
        error_log("Invalid duration_months ($duration_months). Defaulting to 1 month.");
    }

    error_log("Start date: $start_date, End date: $end_date");

    // Insert into the database
    $insert_query = "INSERT INTO plan_bookings (user_id, plan_id, plan_duration, start_date, end_date, total_cost)
                     VALUES ('$user_id', '$plan_id', '$enum_duration', '$start_date', '$end_date', '$amount')";

    if (mysqli_query($conn, $insert_query)) {
        header("Location: success.html?success=Payment successful");
    } else {
        echo "Error: " . mysqli_error($conn);
        error_log("Database error: " . mysqli_error($conn));
    }
    exit();
}

// Store data in session for success.php to use
$_SESSION['temp_plan_id'] = $selected_plan_id;
$_SESSION['temp_amount'] = $price_per_month;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .payment-container {
            background-color: #1e1e1e;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            padding: 20px;
            border-radius: 20px;
            max-width: 600px;
            height: 700px;
            width: 100%;
            border: 2px solid #fff;
            position: relative;
        }
        .payment-container h1 {
            margin-bottom: 20px;
            color: #f05454;
        }
        .form-group {
            background-color: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            margin-bottom: 15px;
            align-items: center;
        }
        label {
            display: block;
            color: #aaa;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #f05454;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ff7979;
        }
    </style>
    <script>
        if (typeof jQuery == 'undefined') {
            alert("jQuery is not loaded!");
        }
    </script>
    <script>
        function updateAmount() {
            const pricePerMonth = parseFloat(document.getElementById('price_per_month').value);
            const durationSelect = document.getElementById('duration');
            const duration = durationSelect.value;
            
            const durationMapping = {
                '1': '1M',
                '3': '3M',
                '6': '6M',
                '12': '1Y'
            };

            // Get ENUM value for duration
            const selectedEnumDuration = durationMapping[duration];

            // Calculate total amount
            const totalAmount = pricePerMonth * parseInt(duration);
            document.getElementById('amount').value = totalAmount.toFixed(2);

            // Update hidden inputs
            document.getElementById('enum_duration').value = selectedEnumDuration;
            document.getElementById('duration_months').value = duration;
        }

        // Initialize values when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateAmount();
        });
    </script>
    
    <script>
        jQuery(document).ready(function($){
            console.log("jQuery is ready");
            
            $('#PayNow').click(function(e){
                console.log("Button clicked");
                e.preventDefault();
                
                // Make sure form values are updated
                updateAmount();
                
                // Log form values
                console.log("Form values:", {
                    enum_duration: $('#enum_duration').val(),
                    duration_months: $('#duration_months').val(),
                    amount: $('#amount').val(),
                    name: $('#name').val(),
                    mobile: $('#mobile').val(),
                    email: $('#email').val()
                });

                // Validate amount
                if(!$('#amount').val()) {
                    alert("Please select a duration first");
                    return;
                }

                // Option 1: Submit the form directly for the server-side processing
                if ($('#paymentMethod').val() === 'direct') {
                    $('form').submit();
                    return;
                }
                
                // Option 2: Continue with Razorpay integration
                // First AJAX call to store session
                $.ajax({
                    type: 'POST',
                    url: 'store_session.php',
                    data: {
                        enum_duration: $('#enum_duration').val(),
                        duration_months: $('#duration_months').val(),
                        amount: $('#amount').val()
                    },
                    success: function(response) {
                        console.log("Session stored successfully", response);
                        
                        // Second AJAX for payment
                        var formData = {
                            billing_name: $('#name').val(),
                            billing_mobile: $('#mobile').val(),
                            billing_email: $('#email').val(),
                            payAmount: $('#amount').val(),
                            action: 'payOrder',
                            duration_months: $('#duration_months').val(),  // Add this to include duration
                            enum_duration: $('#enum_duration').val()       // Add this to include ENUM
                        };

                        console.log("Sending payment data:", formData);
                        
                        $.ajax({
                            type: 'POST',
                            url: "submitpayment.php",
                            data: formData,
                            dataType: 'json',
                            encode: true
                        }).done(function(data){
                            console.log("Payment response:", data);
                            
                            if(data.res === 'success'){
                                var options = {
                                    "key": data.razorpay_key,
                                    "amount": data.userData.amount * 100,
                                    "currency": "INR",
                                    "name": "Your Business Name",
                                    "description": data.userData.description,
                                    "order_id": data.userData.rpay_order_id,
                                    "handler": function (response){
                                        // Include duration data in the redirect
                                        window.location.href = "success.php?oid=" + data.order_number + 
                                            "&rp_payment_id=" + response.razorpay_payment_id + 
                                            "&rp_signature=" + response.razorpay_signature +
                                            "&duration_months=" + $('#duration_months').val() +
                                            "&enum_duration=" + $('#enum_duration').val();
                                    },
                                    "prefill": {
                                        "name": data.userData.name,
                                        "email": data.userData.email,
                                        "contact": data.userData.mobile
                                    },
                                    "theme": {
                                        "color": "#3399cc"
                                    }
                                };
                                
                                var rzp1 = new Razorpay(options);
                                rzp1.on('payment.failed', function (response){
                                    window.location.href = "payment-failed.php?oid=" + data.order_number + 
                                        "&reason=" + response.error.description + 
                                        "&paymentid=" + response.error.metadata.payment_id;
                                });
                                rzp1.open();
                            } else {
                                alert("Error: " + data.info);
                            }
                        }).fail(function(xhr, status, error) {
                            console.error("Payment AJAX error:", status, error);
                            console.log("XHR:", xhr.responseText);
                            alert("Error processing payment. Please try again.");
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Session storage AJAX error:", status, error);
                        console.log("XHR:", xhr.responseText);
                        alert("Error storing session data. Please try again.");
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="payment-container">
        <h1>Complete Your Payment</h1>
        <form action="payment.php" method="post">
            <div class="form-group">
                <label for="name">First Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($Firstname); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="Secondname">Second Name</label>
                <input type="text" id="Secondname" name="Secondname" value="<?php echo htmlspecialchars($Secondname); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobno); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="plan">Plan</label>
                <input type="text" id="plan" name="plan" value="<?php echo htmlspecialchars($plan); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="duration">Duration (in months)</label>
                <select name="duration" id="duration" onchange="updateAmount()" required>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">1 Year</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" value="<?php echo $price_per_month; ?>" readonly>
            </div>
            
            <!-- Add payment method selector -->
            <div class="form-group">
                <label for="paymentMethod">Payment Method</label>
                <select name="paymentMethod" id="paymentMethod">
                    <option value="razorpay">Razorpay</option>
                    <option value="direct">Direct Database Entry</option>
                </select>
            </div>
            
            <!-- Hidden fields -->
            <input type="hidden" id="enum_duration" name="enum_duration">
            <input type="hidden" id="duration_months" name="duration_months">
            <input type="hidden" id="price_per_month" name="price_per_month" value="<?php echo htmlspecialchars($price_per_month); ?>">
            <input type="hidden" id="plan_id" name="plan_id" value="<?php echo $selected_plan_id; ?>">
            
            <button type="button" id="PayNow" class="btn btn-primary">Make Payment</button>
        </form>
    </div>
</body>
</html>