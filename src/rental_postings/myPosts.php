<?php


session_start();


require_once '../model/registration_login.php'; 


$clientID = $_SESSION['clientID'];


$numBookings = isset($_GET['num_bookings']) ? $_GET['num_bookings'] : '5'; 
$sortOption = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_desc';

$query = "SELECT *
          FROM rental_posts
          WHERE rental_posts.clientID = :clientID";
          
if ($sortOption === 'date_asc') {
    $query .= " ORDER BY postDate ASC";
} elseif ($sortOption === 'date_desc') {
    $query .= " ORDER BY postDate DESC";
} elseif ($sortOption === 'cost_asc') {
    $query .= " ORDER BY price ASC";
} elseif ($sortOption === 'cost_desc') {
    $query .= " ORDER BY price DESC";
}


if ($numBookings !== 'all') {
    $query .= " LIMIT " . intval($numBookings);
}



try {
    $statement = $db->prepare($query);
    $statement->bindValue(':clientID', $clientID);
    $statement->execute();
    $userPosts = $statement->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
     $e->getMessage();
}




?>

<html>
    <head>
        <meta charset="UTF-8">
        <title> Client Bookings</title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        
        <link rel ='stylesheet' href ='../style.css'>
    </style>
    </head>
    <body class = 'myPosts'>
         <?php include '../view/nav.php'; ?>
        
     <h1>My Listings</h1>
      
     <?php if (isset($_SESSION['message'])) {
                    echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
                        unset($_SESSION['message']);
                    }
                    ?>
     
     
     <div class="filter-options">
            <form action="" method="GET">
                
                
                
                <select name="sort_by">
                    
                     <option value="date_asc" <?php if ($sortOption === 'date_asc') echo 'selected'; ?>>Date (Oldest First)</option>
                     <option value="date_desc" <?php if ($sortOption === 'date_desc') echo 'selected'; ?>>Date (Newest First)</option>
                    <option value="cost_asc" <?php if ($sortOption === 'cost_asc') echo 'selected'; ?>>Total Cost (Low to High)</option>
                    <option value="cost_desc" <?php if ($sortOption === 'cost_desc') echo 'selected'; ?>>Total Cost (High to Low)</option>

                </select>
                <select name="num_bookings">
                    <option value="2" <?php if ($numBookings === '2') echo 'selected'; ?>>2 Listings</option>
                    <option value="5" <?php if ($numBookings === '5') echo 'selected'; ?>>5 Listings</option>
                    <option value="10" <?php if ($numBookings === '10') echo 'selected'; ?>>10 Listings</option>
                    <option value="all" <?php if ($numBookings === 'all') echo 'selected'; ?>>All</option>
                </select>

                
                
                    <button type="submit">Filter</button>
                </form>
            </div>

    <?php if (!empty($userPosts)): ?>
        
            
        <table>
            <thead>
                <tr>
                    <th>Listing ID</th>
                    <th>Listing Date</th>
                    <th>Address</th>
                    <th>Property Details</th>
                    <th>Cost Per Night</th>
                    <th>Modify Post </th>
                    
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userPosts as $userPost): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($userPost['postID']); ?></td>
                        
                        <td><?php echo htmlspecialchars($userPost['postDate']); ?></td>
                        <td>
                            <a href="../rental_postings/listing.php?postID=<?php echo htmlspecialchars($userPost['postID']); ?>">
                            <?php echo htmlspecialchars($userPost['address']);
                        if (!empty($userPost['unit_number'])) {
                            echo ', Unit ' . htmlspecialchars($userPost['unit_number']);
                        }
                        echo ', ' . htmlspecialchars($userPost['city']);
                        echo ', ' . htmlspecialchars($userPost['province']);
                        echo ', ' . htmlspecialchars($userPost['postal_code']);
                        ?></td>
                        
                        <td><?php
                        
                        echo "Bedrooms: " . htmlspecialchars($userPost['bedrooms']);
                        echo ", Bathrooms: " . htmlspecialchars($userPost['bathrooms']);
                        if (!empty($userPost['kitchens'])) {
                            echo ", Kitchens: " . htmlspecialchars($userPost['kitchens']);
                        }
                        echo ", Area: " . htmlspecialchars($userPost['area']) . " sqft";
                        if (!empty($booking['parking'])) {
                            echo ", Parking: " . htmlspecialchars($userPost['parking']);
                        }
                        ?></td>
                        
                        <td><?php echo "$" . number_format((float)htmlspecialchars($userPost['price']), 2, '.', ''); ?></td>

                       
                        <td>
                
                        <form action="editListing.php" method="get" style="margin-bottom: 10px;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="postID" value="<?php echo htmlspecialchars($userPost['postID']); ?>">
                            <button type="submit">Update Listing</button>
                        </form>
                
                        <form action="updatePost.php" method="post" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="postID" value="<?php echo htmlspecialchars($userPost['postID']); ?>">
                            <button type="submit" style="background-color: red; color: white;">Delete Listing</button>
                        </form>
                        </td>
                        
                        
                        
                       
                        
                            
                         
                   
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            
    <?php else: ?>
        <p style="color: white;font-size: 50px; font-weight: bold; text-align: center;">You have no listings.</p>
    <?php endif; ?>

<?php include '../view/footer.php'; ?>
</body>
</html>
    