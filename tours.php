<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">All Tours</h1>
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Tours</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
                           placeholder="Search by title or location...">
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label">Max Price</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" 
                           value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>" 
                           placeholder="Maximum price">
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo isset($_GET['location']) ? $_GET['location'] : ''; ?>" 
                           placeholder="Filter by location">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tours Grid -->
    <div class="row">
        <?php
        // Krijimi i query-it bazë
        $sql = "SELECT * FROM tours WHERE 1=1";
        $params = [];
        
        // Shto filtera nëse ekzistojnë
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search = mysqli_real_escape_string($conn, $_GET['search']);
            $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR location LIKE '%$search%')";
        }
        
        if(isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $max_price = mysqli_real_escape_string($conn, $_GET['max_price']);
            $sql .= " AND price <= $max_price";
        }
        
        if(isset($_GET['location']) && !empty($_GET['location'])) {
            $location = mysqli_real_escape_string($conn, $_GET['location']);
            $sql .= " AND location LIKE '%$location%'";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo '
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card tour-card h-100">
                        <img src="assets/images/tours/' . ($row['image'] ? $row['image'] : 'default.jpg') . '" 
                             class="card-img-top" alt="' . $row['title'] . '" 
                             style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">' . $row['title'] . '</h5>
                            <p class="card-text flex-grow-1">' . $row['description'] . '</p>
                            <div class="mt-auto">
                                <p class="card-text"><strong>Price: $' . $row['price'] . '</strong></p>
                                <p class="card-text"><small class="text-muted">Duration: ' . $row['duration'] . '</small></p>
                                <p class="card-text"><small class="text-muted">Location: ' . $row['location'] . '</small></p>';
                
                if(isset($_SESSION['user_id'])) {
                    echo '<a href="booking.php?tour_id=' . $row['id'] . '" class="btn btn-primary w-100">Book Now</a>';
                } else {
                    echo '<a href="login.php" class="btn btn-outline-primary w-100">Login to Book</a>';
                }
                
                echo '
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info text-center">No tours found matching your criteria.</div></div>';
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>