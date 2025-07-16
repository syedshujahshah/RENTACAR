<?php
require_once 'db.php';

$booking_id = $_GET['booking_id'] ?? 0;

// Get booking details
$booking = getBookingById($booking_id);

if (!$booking) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - RentACar</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .confirmation-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .confirmation-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .confirmation-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .booking-details {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
            text-align: left;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.3rem 0;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        .car-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .car-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .car-details h4 {
            color: #333;
            margin-bottom: 0.3rem;
        }

        .car-details p {
            color: #666;
            font-size: 0.9rem;
        }

        .total-amount {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }

        .booking-id {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 1rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 2rem 1.5rem;
            }

            .confirmation-title {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .car-info {
                flex-direction: column;
                text-align: center;
            }

            .detail-row {
                flex-direction: column;
                gap: 0.2rem;
            }
        }

        /* Print styles */
        @media print {
            body {
                background: white;
            }

            .confirmation-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1 class="confirmation-title">Booking Confirmed!</h1>
        <p class="confirmation-subtitle">
            Your car rental has been successfully booked. We've sent a confirmation email to your registered email address.
        </p>

        <div class="booking-id">
            <i class="fas fa-ticket-alt"></i> Booking ID: #<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?>
        </div>

        <div class="booking-details">
            <div class="detail-section">
                <div class="section-title">
                    <i class="fas fa-user"></i> Customer Information
                </div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                </div>
            </div>

            <div class="detail-section">
                <div class="section-title">
                    <i class="fas fa-car"></i> Vehicle Details
                </div>
                <div class="car-info">
                    <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="Car" class="car-image">
                    <div class="car-details">
                        <h4><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h4>
                        <p>Premium rental vehicle</p>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <div class="section-title">
                    <i class="fas fa-calendar"></i> Rental Period
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['pickup_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Return Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['return_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Rental Days:</span>
                    <span class="detail-value"><?php echo $booking['total_days']; ?> days</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Location:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['pickup_location']); ?></span>
                </div>
            </div>

            <div class="total-amount">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Total Amount:</span>
                    <span>$<?php echo number_format($booking['total_amount'] * 1.1, 2); ?></span>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="cars.php" class="btn btn-secondary">
                <i class="fas fa-car"></i> Browse More Cars
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Confirmation
            </button>
        </div>

        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee; color: #666; font-size: 0.9rem;">
            <p><strong>Important Notes:</strong></p>
            <ul style="text-align: left; margin-top: 0.5rem;">
                <li>Please bring a valid driver's license and credit card for pickup</li>
                <li>Arrive 15 minutes early for vehicle inspection</li>
                <li>Contact us at (555) 123-4567 for any changes or questions</li>
            </ul>
        </div>
    </div>

    <script>
        // Auto-scroll to top on page load
        window.addEventListener('load', function() {
            window.scrollTo(0, 0);
        });

        // Add some interactive feedback
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>
</html>
