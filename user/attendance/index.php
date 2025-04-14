<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: inline-block;
            width: 80px;
        }
        .response {
            margin: 10px 0;
            padding: 5px;
            border-radius: 4px;
        }
        input[type="number"] {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 120px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        h1, h2 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <h1>Gym Management System</h1>
    
    <!-- Gym Selection -->
    <div class="section">
        <div class="form-group">
            <label for="gym-select">Gym ID:</label>
            <input type="number" id="gym-select" onchange="updateGymIds(this.value)" required>
        </div>
    </div>

    <!-- Check-In Form -->
    <div class="section">
        <h2>Check-In</h2>
        <form id="checkin-form" method="POST">
            <div class="form-group">
                <label>User ID:</label>
                <input type="number" name="user_id" required>
            </div>
            <input type="hidden" name="gym_id" required>
            <button type="submit">Check In</button>
        </form>
        <div id="checkin-response" class="response"></div>
    </div>

    <!-- Check-Out Form -->
    <div class="section">
        <h2>Check-Out</h2>
        <form id="checkout-form" method="POST">
            <div class="form-group">
                <label>User ID:</label>
                <input type="number" name="user_id" required>
            </div>
            <input type="hidden" name="gym_id" required>
            <button type="submit">Check Out</button>
        </form>
        <div id="checkout-response" class="response"></div>
    </div>

    <!-- Real-Time Occupancy -->
    <div class="section">
        <h2>Real-Time Occupancy</h2>
        <button id="fetch-occupancy">Get Occupancy</button>
        <div id="occupancy-data" class="response"></div>
    </div>

    <script>
        function updateGymIds(value) {
            $('input[name="gym_id"]').val(value);
        }

        // Initialize gym ID if present
        $(document).ready(function() {
            const gymId = $('#gym-select').val();
            if (gymId) {
                updateGymIds(gymId);
            }
        });

        // Check-in form handling
        $('#checkin-form').submit(function (e) {
            e.preventDefault();
            
            $('#checkin-response').text('Processing...');
            
            const formData = $(this).serialize();

            $.ajax({
                url: 'checkin.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log("Response received:", response);
                    if (response.success) {
                        $('#checkin-response').text(response.message)
                                            .css('color', 'green');
                        updateOccupancy();
                    } else {
                        $('#checkin-response').text(response.message)
                                            .css('color', 'red');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Request Failed:", status, error);
                    console.error("Response Text:", xhr.responseText);
                    $('#checkin-response').text("An error occurred while connecting to the server.")
                                        .css('color', 'red');
                }
            });
        });

        // Check-out form handling
        $('#checkout-form').submit(function (e) {
            e.preventDefault();
            
            $('#checkout-response').text('Processing...');
            
            const formData = $(this).serialize();

            $.ajax({
                url: 'checkout.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log("Response received:", response);
                    if (response.success) {
                        $('#checkout-response').text(response.message)
                                             .css('color', 'green');
                        updateOccupancy();
                    } else {
                        $('#checkout-response').text(response.message)
                                             .css('color', 'red');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Request Failed:", status, error);
                    console.error("Response Text:", xhr.responseText);
                    $('#checkout-response').text("An error occurred while connecting to the server.")
                                         .css('color', 'red');
                }
            });
        });

        // Occupancy handling
        function updateOccupancy() {
            const gym_id = $('#gym-select').val();
            if (gym_id) {
                $('#fetch-occupancy').click();
            }
        }

        $('#fetch-occupancy').click(function() {
            const gym_id = $('#gym-select').val();
            
            if (!gym_id) {
                $('#occupancy-data').text('Please enter a Gym ID first')
                                   .css('color', 'red');
                return;
            }

            $('#occupancy-data').text('Loading...')
                               .css('color', 'black');

            $.ajax({
                url: 'occupancy.php',
                method: 'GET',
                data: { gym_id: gym_id },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        $('#occupancy-data').text(response.error)
                                           .css('color', 'red');
                    } else {
                        const occupancyText = `Current Occupancy: ${response.current_occupancy} / ${response.max_capacity}`;
                        $('#occupancy-data').text(occupancyText)
                                           .css('color', 'green');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Request Failed:", status, error);
                    console.error("Response Text:", xhr.responseText);
                    $('#occupancy-data').text('Error fetching occupancy data')
                                       .css('color', 'red');
                }
            });
        });
    </script>
</body>
</html>