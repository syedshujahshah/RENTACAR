<?php
require_once 'db.php';

// Get search parameters
$location = $_GET['location'] ?? '';
$car_type = $_GET['car_type'] ?? '';
$fuel_type = $_GET['fuel_type'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 1000;
$sort = $_GET['sort'] ?? 'price_asc';
$pickup_date = $_GET['pickup_date'] ?? '';
$return_date = $_GET['return_date'] ?? '';

// Get filtered cars
$cars = getCars($location, $car_type, $fuel_type, $min_price, $max_price, $sort);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cars - RentACar</title>
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
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            padding: 2rem 0;
        }

        /* Filters Sidebar */
        .filters-sidebar {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .filters-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .price-range {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .apply-filters {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .apply-filters:hover {
            transform: translateY(-2px);
        }

        /* Cars Section */
        .cars-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .results-count {
            font-size: 1.2rem;
            color: #666;
        }

        .sort-dropdown {
            padding: 10px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            background: white;
            cursor: pointer;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }

        .car-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .car-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .car-info {
            padding: 1.5rem;
        }

        .car-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .car-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }

        .car-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #ffa500;
        }

        .car-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .detail-item {
            text-align: center;
            color: #666;
        }

        .detail-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            color: #667eea;
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

        .car-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .book-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .book-btn:hover {
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ccc;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
            }

            .cars-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .section-header {
                flex-direction: column;
                align-items: stretch;
            }
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
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <h3 class="filters-title">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                
                <form id="filtersForm">
                    <div class="filter-group">
                        <label for="filter_location">Location</label>
                        <input type="text" id="filter_location" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter city">
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter_car_type">Car Type</label>
                        <select id="filter_car_type" name="car_type">
                            <option value="">All Types</option>
                            <option value="Economy" <?php echo $car_type === 'Economy' ? 'selected' : ''; ?>>Economy</option>
                            <option value="Compact" <?php echo $car_type === 'Compact' ? 'selected' : ''; ?>>Compact</option>
                            <option value="SUV" <?php echo $car_type === 'SUV' ? 'selected' : ''; ?>>SUV</option>
                            <option value="Luxury" <?php echo $car_type === 'Luxury' ? 'selected' : ''; ?>>Luxury</option>
                            <option value="Sports" <?php echo $car_type === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter_fuel_type">Fuel Type</label>
                        <select id="filter_fuel_type" name="fuel_type">
                            <option value="">All Fuels</option>
                            <option value="Petrol" <?php echo $fuel_type === 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                            <option value="Diesel" <?php echo $fuel_type === 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                            <option value="Electric" <?php echo $fuel_type === 'Electric' ? 'selected' : ''; ?>>Electric</option>
                            <option value="Hybrid" <?php echo $fuel_type === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Price Range (per day)</label>
                        <div class="price-range">
                            <input type="number" name="min_price" value="<?php echo $min_price; ?>" placeholder="Min" min="0">
                            <span>-</span>
                            <input type="number" name="max_price" value="<?php echo $max_price; ?>" placeholder="Max" min="0">
                        </div>
                    </div>
                    
                    <input type="hidden" name="pickup_date" value="<?php echo htmlspecialchars($pickup_date); ?>">
                    <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($return_date); ?>">
                    
                    <button type="submit" class="apply-filters">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </form>
            </aside>

            <!-- Cars Section -->
            <main class="cars-section">
                <div class="section-header">
                    <div class="results-count">
                        <i class="fas fa-car"></i> <?php echo count($cars); ?> cars available
                    </div>
                    
                    <select class="sort-dropdown" id="sortSelect">
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Best Rated</option>
                    </select>
                </div>

                <?php if (empty($cars)): ?>
                <div class="no-results">
                    <i class="fas fa-car-crash"></i>
                    <h3>No cars found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                </div>
                <?php else: ?>
                <div class="cars-grid">
                    <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="car-image">
                        
                        <div class="car-info">
                            <div class="car-header">
                                <h3 class="car-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?></h3>
                                <div class="car-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $car['rating']; ?></span>
                                </div>
                            </div>
                            
                            <div class="car-details">
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $car['seats']; ?> Seats</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-gas-pump"></i>
                                    <span><?php echo $car['fuel_type']; ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-cog"></i>
                                    <span><?php echo $car['transmission']; ?></span>
                                </div>
                            </div>
                            
                            <div class="car-features">
                                <?php 
                                $features = explode(', ', $car['features']);
                                foreach (array_slice($features, 0, 4) as $feature): 
                                ?>
                                <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="car-footer">
                                <div class="car-price">
                                    $<?php echo number_format($car['price_per_day'], 2); ?>/day
                                </div>
                                <button class="book-btn" onclick="bookCar(<?php echo $car['id']; ?>)">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        // Handle filters form submission
        document.getElementById('filtersForm').addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });

        // Handle sort change
        document.getElementById('sortSelect').addEventListener('change', function() {
            applyFilters();
        });

        function applyFilters() {
            const formData = new FormData(document.getElementById('filtersForm'));
            const params = new URLSearchParams();
            
            // Add form data
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            // Add sort parameter
            const sort = document.getElementById('sortSelect').value;
            if (sort) params.append('sort', sort);
            
            // Redirect with new parameters
            window.location.href = 'cars.php?' + params.toString();
        }

        function bookCar(carId) {
            const pickup_date = '<?php echo $pickup_date; ?>';
            const return_date = '<?php echo $return_date; ?>';
            const location = '<?php echo $location; ?>';
            
            if (!pickup_date || !return_date) {
                alert('Please go back to home page and select pickup and return dates!');
                return;
            }
            
            // Redirect to booking page
            window.location.href = `booking.php?car_id=${carId}&pickup_date=${pickup_date}&return_date=${return_date}&location=${encodeURIComponent(location)}`;
        }

        // Auto-apply filters when typing (with debounce)
        let filterTimeout;
        document.querySelectorAll('#filtersForm input, #filtersForm select').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    if (this.type !== 'submit') {
                        applyFilters();
                    }
                }, 1000);
            });
        });
    </script>
</body>
</html>
