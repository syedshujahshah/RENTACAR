<?php
// Database configuration
$host = 'localhost';
$dbname = 'dbrzmfgzb2tc8v';
$username = 'ulnrcogla9a1t';
$password = 'yolpwow1mwr2';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get all cars with filters
function getCars($location = '', $car_type = '', $fuel_type = '', $min_price = 0, $max_price = 1000, $sort = 'price_asc') {
    global $pdo;
    
    $sql = "SELECT * FROM cars WHERE available = 1";
    $params = [];
    
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    
    if (!empty($car_type)) {
        $sql .= " AND car_type = ?";
        $params[] = $car_type;
    }
    
    if (!empty($fuel_type)) {
        $sql .= " AND fuel_type = ?";
        $params[] = $fuel_type;
    }
    
    $sql .= " AND price_per_day BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    
    // Add sorting
    switch($sort) {
        case 'price_asc':
            $sql .= " ORDER BY price_per_day ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY price_per_day DESC";
            break;
        case 'rating':
            $sql .= " ORDER BY rating DESC";
            break;
        default:
            $sql .= " ORDER BY price_per_day ASC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Function to get car by ID
function getCarById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Function to create booking
function createBooking($car_id, $customer_name, $customer_email, $customer_phone, $pickup_location, $pickup_date, $return_date, $total_days, $total_amount) {
    global $pdo;
    
    $sql = "INSERT INTO bookings (car_id, customer_name, customer_email, customer_phone, pickup_location, pickup_date, return_date, total_days, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$car_id, $customer_name, $customer_email, $customer_phone, $pickup_location, $pickup_date, $return_date, $total_days, $total_amount]);
}

// Function to get booking by ID
function getBookingById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT b.*, c.brand, c.model, c.image_url FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
?>
