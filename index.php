<?php
require_once 'db.php';

// Get featured cars
$featured_cars = getCars('', '', '', 0, 1000, 'rating');
$featured_cars = array_slice($featured_cars, 0, 6); // Get top 6 cars
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentACar - Premium Car Rental Service</title>
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
            position: fixed;
            width: 100%;
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

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 150px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Search Form */
        .search-form {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-top: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Featured Cars Section */
        .featured-section {
            background: white;
            padding: 100px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #333;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .car-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .car-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .car-info {
            padding: 1.5rem;
        }

        .car-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .car-details {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            color: #666;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 1rem 0;
        }

        .car-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .feature-tag {
            background: #f0f2ff;
            color: #667eea;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .book-btn {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .book-btn:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .cars-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Premium Car Rental Service</h1>
                <p>Find the perfect car for your journey. Luxury, comfort, and reliability guaranteed.</p>
                
                <form class="search-form" id="searchForm">
                    <div class="form-group">
                        <label for="location">Pickup Location</label>
                        <input type="text" id="location" name="location" placeholder="Enter city or location" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_date">Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="return_date">Return Date</label>
                        <input type="date" id="return_date" name="return_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="car_type">Car Type</label>
                        <select id="car_type" name="car_type">
                            <option value="">All Types</option>
                            <option value="Economy">Economy</option>
                            <option value="Compact">Compact</option>
                            <option value="SUV">SUV</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Search Cars
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">Featured Rental Cars</h2>
            
            <div class="cars-grid">
                <?php foreach ($featured_cars as $car): ?>
                <div class="car-card">
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="car-image">
                    <div class="car-info">
                        <h3 class="car-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?></h3>
                        
                        <div class="car-details">
                            <span><i class="fas fa-users"></i> <?php echo $car['seats']; ?> Seats</span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo $car['fuel_type']; ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo $car['transmission']; ?></span>
                        </div>
                        
                        <div class="car-features">
                            <?php 
                            $features = explode(', ', $car['features']);
                            foreach (array_slice($features, 0, 3) as $feature): 
                            ?>
                            <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="car-price">
                            $<?php echo number_format($car['price_per_day'], 2); ?>/day
                        </div>
                        
                        <button class="book-btn" onclick="bookCar(<?php echo $car['id']; ?>)">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 RentACar. All rights reserved. Premium car rental service.</p>
        </div>
    </footer>

    <script>
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickup_date').min = today;
            document.getElementById('return_date').min = today;
            
            // Update return date minimum when pickup date changes
            document.getElementById('pickup_date').addEventListener('change', function() {
                document.getElementById('return_date').min = this.value;
            });
        });

        // Search form submission
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            // Redirect to cars page with search parameters
            window.location.href = 'cars.php?' + params.toString();
        });

        // Book car function
        function bookCar(carId) {
            const pickup_date = document.getElementById('pickup_date').value;
            const return_date = document.getElementById('return_date').value;
            const location = document.getElementById('location').value;
            
            if (!pickup_date || !return_date) {
                alert('Please select pickup and return dates first!');
                return;
            }
            
            // Redirect to booking page
            window.location.href = `booking.php?car_id=${carId}&pickup_date=${pickup_date}&return_date=${return_date}&location=${encodeURIComponent(location)}`;
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
