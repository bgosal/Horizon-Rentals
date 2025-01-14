<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../model/registration_login.php';

$bedroomsFilter = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : null;
$bathroomsFilter = isset($_GET['bathrooms']) ? $_GET['bathrooms'] : null;
$kitchensFilter = isset($_GET['kitchens']) ? $_GET['kitchens'] : null;
$parkingFilter = isset($_GET['parking']) ? $_GET['parking'] : null;
$priceMinFilter = isset($_GET['priceMin']) && $_GET['priceMin'] !== '' ? $_GET['priceMin'] : null;
$priceMaxFilter = isset($_GET['priceMax']) && $_GET['priceMax'] !== '' ? $_GET['priceMax'] : null;

$cityFilter = isset($_GET['city']) ? $_GET['city'] : null;
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'date_desc';



function findClosestCity($input, $cities) {
    $shortest = -1;
    $closest = null;

    foreach ($cities as $city) {
        $lev = levenshtein(strtolower($input), strtolower($city));
        if ($lev == 0) {
            $closest = $city;
            break;
        }
        if ($lev <= $shortest || $shortest < 0) {
            $closest = $city;
            $shortest = $lev;
        }
    }
    return $closest;
}






$cities = ["Abbotsford", "Burnaby", "Chilliwack", "Coquitlam", "Delta", "Kamloops", "Kelowna", "Langley", "Maple Ridge", "Mission", "Nanaimo", "Penticton", "Prince George", "Richmond", "Surrey", "Vancouver", "Victoria", "White Rock"];

$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$closestCity = !empty($city) ? findClosestCity($city, $cities) : '';

$checkin = isset($_GET['checkin']) && !empty($_GET['checkin']) ? $_GET['checkin'] : null;
$checkout = isset($_GET['checkout']) && !empty($_GET['checkout']) ? $_GET['checkout'] : null;

$checkinDateObj = $checkin ? DateTime::createFromFormat('Y-m-d', $checkin) : null;
$checkoutDateObj = $checkout ? DateTime::createFromFormat('Y-m-d', $checkout) : null;


if ($checkinDateObj && $checkoutDateObj && $checkoutDateObj > $checkinDateObj) {
    $datesValid = true;
    $checkinDate = $checkinDateObj->format('Y-m-d');
    $checkoutDate = $checkoutDateObj->format('Y-m-d');
} else {
    $datesValid = false;
}


$posts_per_page = 8;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if ($page === false || $page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $posts_per_page;









$baseQuery = 'SELECT rp.*, MIN(img.imageID) AS imageId FROM rental_posts AS rp LEFT JOIN images AS img ON rp.postID = img.postID';

$whereConditions = []; 

if ($bedroomsFilter !== null) {
    $whereConditions[] = 'rp.bedrooms >= :bedrooms';
}
if ($bathroomsFilter !== null) {
    $whereConditions[] = 'rp.bathrooms >= :bathrooms';
}
if ($kitchensFilter !== null) {
    $whereConditions[] = 'rp.kitchens >= :kitchens';
}
if ($parkingFilter !== null) {
    $whereConditions[] = 'rp.parking >= :parking';
}
if ($priceMinFilter !== null) {
    $whereConditions[] = 'rp.price >= :priceMin';
}
if ($priceMaxFilter !== null) {
    $whereConditions[] = 'rp.price <= :priceMax';
}

//var_dump($priceMinFilter);
//var_dump($priceMaxFilter);

if (!empty($closestCity)) {
    
    $whereConditions[] = 'rp.city = :city';
}
if ($checkinDateObj || $checkoutDateObj) {
    if ($checkinDateObj && !$checkoutDateObj) {
        
        $whereConditions[] = 'NOT EXISTS (
            SELECT 1 FROM bookings AS b
            WHERE b.postID = rp.postID
            AND b.CheckInDate < :checkin
        AND b.CheckOutDate > :checkin
        )';
    } elseif (!$checkinDateObj && $checkoutDateObj) {
       
        $whereConditions[] = 'NOT EXISTS (
            SELECT 1 FROM bookings AS b
            WHERE b.postID = rp.postID
            AND b.CheckInDate < :checkout
        AND b.CheckOutDate > :checkout
        )';
    } else {
       
        $whereConditions[] = 'NOT EXISTS (
            SELECT 1 FROM bookings AS b
            WHERE b.postID = rp.postID
            AND NOT (b.CheckOutDate <= :checkin or b.CheckInDate >= :checkout)
        )';
    }
}


$whereClause = !empty($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';

$orderByClause = '';


if ($sortBy === 'priceDesc') {
    $orderByClause = ' ORDER BY rp.price DESC';
} elseif ($sortBy === 'priceAsc') {
    $orderByClause = ' ORDER BY rp.price ASC';
} elseif ($sortBy === 'bedroomsMost') {
    $orderByClause = ' ORDER BY rp.bedrooms DESC';
} elseif ($sortBy === 'bedroomsFewest') {
    $orderByClause = ' ORDER BY rp.bedrooms ASC';
} elseif ($sortBy === 'areaDesc') {
    $orderByClause = ' ORDER BY rp.area DESC';
} elseif ($sortBy === 'areaAsc') {
    $orderByClause = ' ORDER BY rp.area ASC';
} elseif ($sortBy === 'newest') {
    $orderByClause = ' ORDER BY rp.postDate DESC';
} elseif ($sortBy === 'oldest') {
    $orderByClause = ' ORDER BY rp.postDate  ASC';
}



$countQuery = "SELECT COUNT(DISTINCT rp.postID) FROM rental_posts AS rp LEFT JOIN images AS img ON rp.postID = img.postID " . $whereClause;

$countStatement = $db->prepare($countQuery);


$query = $baseQuery . $whereClause . ' GROUP BY rp.postID ' . $orderByClause . ' LIMIT :offset, :limit';




$statement = $db->prepare($query);




if (!empty($closestCity)) {
    $statement->bindValue(':city', $closestCity);
    $countStatement->bindValue(':city', $closestCity);
}

if ($checkinDateObj) {
    $statement->bindValue(':checkin', $checkinDateObj->format('Y-m-d'));
    $countStatement->bindValue(':checkin', $checkinDateObj->format('Y-m-d'));
}
if ($checkoutDateObj) {
    $statement->bindValue(':checkout', $checkoutDateObj->format('Y-m-d'));
    $countStatement->bindValue(':checkout', $checkoutDateObj->format('Y-m-d'));
}

if ($bedroomsFilter !== null) {
    $statement->bindValue(':bedrooms', $bedroomsFilter, PDO::PARAM_INT);
   $countStatement->bindValue(':bedrooms', $bedroomsFilter, PDO::PARAM_INT);
}
if ($bathroomsFilter !== null) {
    $statement->bindValue(':bathrooms', $bathroomsFilter, PDO::PARAM_INT);
    $countStatement->bindValue(':bathrooms', $bathroomsFilter, PDO::PARAM_INT);
}

if ($kitchensFilter !== null) {
    $statement->bindValue(':kitchens', $kitchensFilter, PDO::PARAM_INT);
    $countStatement->bindValue(':kitchens', $kitchensFilter, PDO::PARAM_INT);
}

if ($parkingFilter !== null) {
    $statement->bindValue(':parking', $parkingFilter, PDO::PARAM_INT);
     $countStatement->bindValue(':parking', $parkingFilter, PDO::PARAM_INT);
}



if ($priceMinFilter !== null) {
    $statement->bindValue(':priceMin', $priceMinFilter);
    $countStatement->bindValue(':priceMin', $priceMinFilter);
}

if ($priceMaxFilter !== null) {
    $statement->bindValue(':priceMax', $priceMaxFilter);
    $countStatement->bindValue(':priceMax', $priceMaxFilter);
}

$statement->bindValue(':offset', $offset, PDO::PARAM_INT);
$statement->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);



$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);




$countStatement->execute();
$totalRecords = $countStatement->fetchColumn();
$totalPages = ceil($totalRecords / $posts_per_page);






?>

<html>
    <head>
        <meta charset="UTF-8">
        <title> Search Results </title>
         <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
          <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <link rel ='stylesheet' href ='../style.css'>
        
        
    </head>
     
    <body class ='search_page'>
        
         <?php include '../view/nav.php'; ?>
        
        
        <div class ="container4" >
            
          
             <div class="search-bar-new">  
    <form action="search_results.php" method="get"> 
        
        <input type="text" name="city" placeholder="Enter city name..." value="<?php echo htmlspecialchars(isset($_GET['city']) ? $_GET['city'] : ''); ?>">
        
        
        <input type="text" name="checkin" id="checkin" placeholder="Check-in Date" value="<?php echo htmlspecialchars(isset($_GET['checkin']) ? $_GET['checkin'] : ''); ?>">
        
        
        <input type="text" name="checkout" id="checkout" placeholder="Check-out Date" value="<?php echo htmlspecialchars(isset($_GET['checkout']) ? $_GET['checkout'] : ''); ?>">
        
        <button type="button" onclick="toggleFilterMenu()" class="filter-btn"><i class='bx bx-filter-alt'></i></button>
        <button type="submit" name="submit">Search</button>
        </form>
        
        <div class="user_filters">

    
    <div id="filterMenu" style="display: none;">
        <form action="search_results.php" method="get">
            <div class="filter-option">
                <i class='bx bx-bed'></i>
                <input type="number" name="bedrooms" placeholder="Bedrooms..."value="<?php echo isset($_GET['bedrooms']) ? $_GET['bedrooms'] : ''; ?>">
            </div>
            <div class="filter-option">
                <i class='bx bx-bath'></i>
                <input type="number" name="bathrooms" placeholder="Bathrooms..."value="<?php echo isset($_GET['bathrooms']) ? $_GET['bathrooms'] : ''; ?>">
            </div>
            <div class="filter-option">
                <i class='bx bx-food-menu'></i>
                <input type="number" name="kitchens" placeholder="Kitchens..."value="<?php echo isset($_GET['kitchens']) ? $_GET['kitchens'] : ''; ?>">
            </div>
            <div class="filter-option">
                <i class='bx bx-car'></i>
                <input type="number" name="parking" placeholder="Parking Spots..."value="<?php echo isset($_GET['parking']) ? $_GET['parking'] : ''; ?>">
            </div>
            <div class="filter-option">
                <i class='bx bx-money'></i>
                <input type="number" name="priceMin" placeholder="Min Price"value="<?php echo isset($_GET['priceMin']) ? $_GET['priceMin'] : ''; ?>">
                <input type="number" name="priceMax" placeholder="Max Price"value="<?php echo isset($_GET['priceMax']) ? $_GET['priceMax'] : ''; ?>">
            </div>
            <div class="filter-option">
            <i class='bx bx-sort'></i>
            <select name="sortBy">
                <option value="priceDesc" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'priceDesc') echo 'selected'; ?>>Price: High to Low</option>
        <option value="priceAsc" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'priceAsc') echo 'selected'; ?>>Price: Low to High</option>
        <option value="bedroomsMost" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'bedroomsMost') echo 'selected'; ?>>Bedrooms: Most to Fewest</option>
        <option value="bedroomsFewest" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'bedroomsFewest') echo 'selected'; ?>>Bedrooms: Fewest to Most</option>
        <option value="areaDesc" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'areaDesc') echo 'selected'; ?>>Area: High to Low</option>
        <option value="areaAsc" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'areaAsc') echo 'selected'; ?>>Area: Low to High</option>
        <option value="newest" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'newest') echo 'selected'; ?>>Newest Listings to Oldest Listings</option>
        <option value="oldest" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'oldest') echo 'selected'; ?>>Oldest Listings to Newest Listings</option>
            </select>
        </div>
            
            
             <div class="filter-option">
            <i class='bx bx-buildings'></i>
            <select name="city">
                <option value="">Select a city...</option>
                <?php
                
                 foreach ($cities as $cityOption) {
            
               $isSelected = ($cityOption == $closestCity) ? ' selected' : '';
            echo "<option value=\"".htmlspecialchars($cityOption)."\"$isSelected>".htmlspecialchars($cityOption)."</option>";
                }
                ?>
            </select>
        </div>
            
            
            <input type="submit" value="Update">
        </form>
    </div>
</div>

            
            </div>
           

            
            
            
                <div class="search-feedback">
                    <?php if (!empty($city)): ?>
                        <p>
                            <?php if (strtolower($city) !== strtolower($closestCity)): ?>
                                Did you mean: 
                                <a href="search_results.php?city=<?php echo urlencode($closestCity); ?>&checkin=<?php echo urlencode(isset($_GET['checkin']) ? $_GET['checkin'] : ''); ?>&checkout=<?php echo urlencode(isset($_GET['checkout']) ? $_GET['checkout'] : ''); ?>" style="color: white;">
                                    <strong><?php echo htmlspecialchars($closestCity); ?></strong>
                                </a>? 
                            <?php endif; ?>
                            Showing results for: <strong style="color: white; "><?php echo htmlspecialchars($closestCity); ?></strong>.
                        </p>
                    <?php endif; ?>
                </div>


            
           <div class="pagination">
    <?php 
    for ($i = 1; $i <= $totalPages; $i++):
        
        $queryParams = array(
            'city' => $closestCity, 
            'checkin' => $checkin,
            'checkout' => $checkout,
            'bedrooms' => $bedroomsFilter,
            'bathrooms' => $bathroomsFilter,
            'kitchens' => $kitchensFilter,
            'parking' => $parkingFilter,
            'priceMin' => $priceMinFilter,
            'priceMax' => $priceMaxFilter,
            'sortBy' => $sortBy,
            'page' => $i, 
        );

        
        $filteredParams = array_filter($queryParams, function($value) { 
            return !is_null($value) && ($value !== ''); 
        });

        
        $queryString = http_build_query($filteredParams);

        
        $link = "search_results.php?" . $queryString;
    ?>
        <a href="<?php echo htmlspecialchars($link); ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>


            
            
            
            
            <div class="listings_grid">
    <?php foreach ($posts as $post): ?>
        <?php
            
            $imageSrc = "../images/no-image-found.png"; 

            if (!empty($post['imageId'])) {
                $imgQuery = 'SELECT imageData, imageType FROM images WHERE imageID = :imageID';
                $imgStatement = $db->prepare($imgQuery);
                $imgStatement->bindValue(':imageID', $post['imageId'], PDO::PARAM_INT); 
                $imgStatement->execute();
                $image = $imgStatement->fetch(PDO::FETCH_ASSOC);

                if ($image) {
                    $imageSrc = "data:" . $image['imageType'] . ";base64," . base64_encode($image['imageData']);
                }
            }
        ?>
                
                <div class="listing_tile">
            <a href="listing.php?postID=<?php echo $post['postID']; ?>" class="listing_link">
                <div class="listing_image">
                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Listing Image">
                </div>
            <div class="listing_info">
    <div class="listing_price"><i class='bx bx-money'></i> <?php echo "$".$post['price']; ?> / night</div>
    <div class="listing_details">
        <i class='bx bx-bed'></i> <?php echo $post['bedrooms']; ?>
        <i class='bx bx-bath'></i> <?php echo $post['bathrooms']; ?>
        
        <i class='bx bx-food-menu'></i> <?php echo $post['kitchens']; ?> 
        <i class='bx bx-car'></i> <?php echo $post['parking']; ?> 
        <i class='bx bx-expand'></i> <?php echo $post['area']." sqft"; ?>
    </div>
    <div class="listing_address"><?php echo $post['address'].", ".$post['city']." ".$post['province']; ?></div>
</div>

            </a>
        </div>
    <?php endforeach; ?>
                
                
</div>


        </div>
        
        
        
              
        </div>

        
    </body>
</html>

<script>
    flatpickr("#checkin", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            var checkOutPicker = flatpickr("#checkout", {minDate: "today"});
        var minCheckOutDate = selectedDates[0].fp_incr(1); 
        checkOutPicker.set('minDate', minCheckOutDate);

        
        var currentCheckOutDate = checkOutPicker.selectedDates[0];
        if (currentCheckOutDate && currentCheckOutDate < minCheckOutDate) {
            checkOutPicker.setDate(minCheckOutDate);
        }
        }
    });

    flatpickr("#checkout", {
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1)
    });
</script>


<script>
    function toggleFilterMenu() {
        var menu = document.getElementById('filterMenu');
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }
</script>
   
