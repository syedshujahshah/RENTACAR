<?php
require_once 'db.php';

$car_id = $_GET['car_id'] ?? 0;
$pickup_date = $_GET['pickup_date'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$location = $_GET['location'] ?? '';

// Get car details
$car = getCarById($car_id);

if (!$car) {
    header('Location: cars.php');
    exit;
}

// Calculate rental days and total amount
$rental_days = 0;
$total_amount = 0;

if ($pickup_date && $return_date) {
    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $rental_days = $pickup->diff($return)->days;
    $total_amount = $rental_days * $car['price_per_day'];
}

// Handle form submission
if ($_POST) {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $pickup_location = $_POST['pickup_location'] ?? '';
    $pickup_date = $_POST['pickup_date'] ?? '';
    $return_date = $_POST['return_date'] ?? '';
    
    // Recalculate days and amount
    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $rental_days = $pickup->diff($return)->days;
    $total_amount = $rental_days * $car['price_per_day'];
    
    // Create booking
    if (createBooking($car_id, $customer_name, $customer_email, $customer_phone, $pickup_location, $pickup_date, $return_date, $rental_days, $total_amount)) {
        $booking_id = $pdo->lastInsertId();
        echo "<script>window.location.href = 'confirmation.php?booking_id=$booking_id';</script>";
        exit;
    } else {
        $error = "Failed to create booking. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?> - RentACar</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            align-items: start;
        }

        /* Booking Form */
        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .form-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Car Summary */
        .car-summary {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: sticky;
            top: 120px;
        }

        .summary-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #333;
            text-align: center;
        }

        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
        }

        .car-name {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
            color: #333;
        }

        .car-specs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .spec-item {
            text-align: center;
            color: #666;
        }

        .spec-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            color: #667eea;
        }

        .booking-details {
            border-top: 2px solid #f0f0f0;
            padding-top: 1.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding: 0.5rem 0;
        }

        .detail-row.total {
            border-top: 2px solid #667eea;
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: bold;
            font-size: 1.2rem;
            color: #667eea;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border: 1px solid #fcc;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .car-specs {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
        }

        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">
                <i class="fas fa-car"></i> RentACar
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Cars</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="main-content">
            <!-- Booking Form -->
            <div class="booking-form">
                <h2 class="form-title">
                    <i class="fas fa-calendar-check"></i> Complete Your Booking
                </h2>

                <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" id="bookingForm">
                    <div class="form-group">
                        <label for="customer_name">
                            <i class="fas fa-user"></i> Full Name *
                        </label>
                        <input type="text" id="customer_name" name="customer_name" required placeholder="Enter your full name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_email">
                                <i class="fas fa-envelope"></i> Email *
                            </label>
                            <input type="email" id="customer_email" name="customer_email" required placeholder="your@email.com">
                        </div>

                        <div class="form-group">
                            <label for="customer_phone">
                                <i class="fas fa-phone"></i> Phone *
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone" required placeholder="+1 (555) 123-4567">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pickup_location">
                            <i class="fas fa-map-marker-alt"></i> Pickup Location *
                        </label>
                        <input type="text" id="pickup_location" name="pickup_location" value="<?php echo htmlspecialchars($location); ?>" required placeholder="Enter pickup address">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="pickup_date">
                                <i class="fas fa-calendar"></i> Pickup Date *
                            </label>
                            <input type="date" id="pickup_date" name="pickup_date" value="<?php echo $pickup_date; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="return_date">
                                <i class="fas fa-calendar"></i> Return Date *
                            </label>
                            <input type="date" id="return_date" name="return_date" value="<?php echo $return_date; ?>" required>
                        </div>
                    </div>

                    <div class="loading" id="loadingDiv">
                        <div class="spinner"></div>
                        <p>Processing your booking...</p>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-credit-card"></i> Confirm Booking
                    </button>
                </form>
            </div>

            <!-- Car Summary -->
            <div class="car-summary">
                <h3 class="summary-title">
                    <i class="fas fa-car"></i> Booking Summary
                </h3>

                <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="car-image">

                <div class="car-name">
                    <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?>
                </div>

                <div class="car-specs">
                    <div class="spec-item">
                        <i class="fas fa-users"></i>
                        <span><?php echo $car['seats']; ?> Seats</span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-gas-pump"></i>
                        <span><?php echo $car['fuel_type']; ?></span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-cog"></i>
                        <span><?php echo $car['transmission']; ?></span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-star"></i>
                        <span><?php echo $car['rating']; ?> Rating</span>
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-row">
                        <span>Daily Rate:</span>
                        <span>$<?php echo number_format($car['price_per_day'], 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Rental Days:</span>
                        <span id="rentalDays"><?php echo $rental_days; ?> days</span>
                    </div>
                    <div class="detail-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Taxes & Fees:</span>
                        <span id="taxes">$<?php echo number_format($total_amount * 0.1, 2); ?></span>
                    </div>
                    <div class="detail-row total">
                        <span>Total Amount:</span>
                        <span id="totalAmount">$<?php echo number_format($total_amount * 1.1, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const carPricePerDay = <?php echo $car['price_per_day']; ?>;

        // Set minimum dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickup_date').min = today;
            document.getElementById('return_date').min = today;
        });

        // Update calculations when dates change
        function updateCalculations() {
            const pickupDate = document.getElementById('pickup_date').value;
            const returnDate = document.getElementById('return_date').value;

            if (pickupDate && returnDate) {
                const pickup = new Date(pickupDate);
                const returnD = new Date(returnDate);
                const timeDiff = returnD.getTime() - pickup.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

                if (daysDiff > 0) {
                    const subtotal = daysDiff * carPricePerDay;
                    const taxes = subtotal * 0.1;
                    const total = subtotal + taxes;

                    document.getElementById('rentalDays').textContent = daysDiff + ' days';
                    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
                    document.getElementById('taxes').textContent = '$' + taxes.toFixed(2);
                    document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
                } else {
                    alert('Return date must be after pickup date!');
                    document.getElementById('return_date').value = '';
                }
            }
        }

        // Add event listeners for date changes
        document.getElementById('pickup_date').addEventListener('change', function() {
            document.getElementById('return_date').min = this.value;
            updateCalculations();
        });

        document.getElementById('return_date').addEventListener('change', updateCalculations);

        // Handle form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading
            document.getElementById('loadingDiv').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
            
            // Validate dates
            const pickupDate = document.getElementById('pickup_date').value;
            const returnDate = document.getElementById('return_date').value;
            
            if (!pickupDate || !returnDate) {
                alert('Please select both pickup and return dates!');
                document.getElementById('loadingDiv').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
                return;
            }
            
            const pickup = new Date(pickupDate);
            const returnD = new Date(returnDate);
            
            if (returnD <= pickup) {
                alert('Return date must be after pickup date!');
                document.getElementById('loadingDiv').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
                return;
            }
            
            // Submit form
            setTimeout(() => {
                this.submit();
            }, 1000);
        });

        // Phone number formatting
        document.getElementById('customer_phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
