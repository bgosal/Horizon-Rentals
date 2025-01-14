<?php
require_once '../model/registration_login.php'; 






if (isset($_GET['postID']) && !empty($_GET['postID'])) {
    $postID = $_GET['postID'];

    
    $query = "SELECT * FROM rental_posts WHERE postID = :postID";
    $statement = $db->prepare($query);
    $statement->bindValue(':postID', $postID, PDO::PARAM_INT);
    $statement->execute();

    
    $listing = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    
    
    $queryImages = "SELECT imageData, imageType FROM images WHERE postID = :postID";
    $statementImages = $db->prepare($queryImages);
    $statementImages->bindValue(':postID', $postID, PDO::PARAM_INT);
    $statementImages->execute();
    $images = $statementImages->fetchAll(PDO::FETCH_ASSOC);
    $statementImages->closeCursor();

    
    
    
    $queryBookings = "SELECT CheckInDate, CheckOutDate FROM bookings WHERE postID = :postID";
$statementBookings = $db->prepare($queryBookings);
$statementBookings->bindValue(':postID', $postID, PDO::PARAM_INT);
$statementBookings->execute();

$bookings = $statementBookings->fetchAll(PDO::FETCH_ASSOC);
$statementBookings->closeCursor();


$unavailableDates = [];


if ($bookings) { 
        foreach ($bookings as $booking) {
            $period = new DatePeriod(
                new DateTime($booking['CheckInDate']),
                new DateInterval('P1D'),
                (new DateTime($booking['CheckOutDate']))->modify('+1 day') 
            );

            foreach ($period as $date) {
                $unavailableDates[] = $date->format("Y-m-d");
            }
        }
    }



}
function generate_time_options() {
    $options = '';
    for ($hour = 0; $hour < 24; $hour++) {
        for ($minute = 0; $minute < 60; $minute += 30) {
            $time = sprintf('%02d:%02d', $hour, $minute);
            $options .= "<option value=\"$time\">$time</option>";
        }
    }
    return $options;
}



echo '<script>';
echo 'var unavailableDates = ' . json_encode($unavailableDates) . ';';
echo '</script>';




?>








<html>
    <head>
        <meta charset="UTF-8">
        <title> Listing Information </title>
         <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
         
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script type ="text/javascript" <script src="../lightbox/lightbox-plus-jquery.min.js"></script>

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
   
        <link rel ='stylesheet' href ='../style.css'>
        <link rel="stylesheet" type="text/css" href ="../lightbox/lightbox.min.css">
        

        

    </head>
   
    <?php include '../view/nav.php'; ?>
    

    
    <body class ='listing_page'>
        

        
 <div class="search-bar-new">  
    <form action="search_results.php" method="get"> 
        
        <input type="text" name="city" placeholder="Enter city name..." value="<?php echo htmlspecialchars(isset($_GET['city']) ? $_GET['city'] : ''); ?>">
        
        
        <input type="text" name="checkin" id="checkin" placeholder="Check-in Date" value="<?php echo htmlspecialchars(isset($_GET['checkin']) ? $_GET['checkin'] : ''); ?>">
        
        
        <input type="text" name="checkout" id="checkout" placeholder="Check-out Date" value="<?php echo htmlspecialchars(isset($_GET['checkout']) ? $_GET['checkout'] : ''); ?>">
        
        
        <button type="submit" name="submit">Search</button>
        </form>
     
 </div>
    

        

            
                <?php
$todayDate = date("Y-m-d");
?>
                   
                
            
                <?php if (isset($listing) && $listing): ?>
        
                
                    <div class="listing-wrapper">
                        <div class ="top-row">
                    <div class="image-gallery">
                        <h1><?php echo $listing['address'].", ". $listing['city'] . ", " . $listing['province'] . " " . $listing['postal_code']; ?></h1>
 
    

                        <div class="property-features">
                            <span class="feature"><i class='bx bx-bed'></i> <?php echo $listing['bedrooms']; ?> Bedroom(s)</span>
                            <span class="feature"><i class='bx bx-bath'></i> <?php echo $listing['bathrooms']; ?> Bathroom(s)</span>
                            <span class="feature"><i class='bx bx-restaurant' ></i><?php echo $listing['kitchens']; ?> Kitchen(s)</span>
                            <span class="feature"><i class='bx bx-car'></i> <?php echo $listing['parking']; ?> Parking(s)</span>
                            <span class="feature"><i class='bx bx-area' ></i> <?php echo $listing['area']; ?> Area(sqft)</span>
        
        
                        </div>
                        
                        <?php foreach ($images as $index => $image): ?>
                            <?php
                                $base64Image = base64_encode($image['imageData']);
                                $imageSrc = 'data:' . $image['imageType'] . ';base64,' . $base64Image;
                                $style = $index === 0 ? "" : "display: none;"; 
                            ?>
                            <a href="<?php echo $imageSrc; ?>" data-lightbox="rental-post-slideshow" data-title="Image <?php echo ($index + 1); ?>" style="<?php echo $style; ?>">
                                <img src="<?php echo $imageSrc; ?>" alt="Image <?php echo ($index + 1); ?>">

                            </a>
                        <?php endforeach; ?>
                    </div>
                    
        
        <div class ="BookingInfo">
                    
                <div id="listing-cost">
        <span class="bx bx-money"></span> $<?php echo htmlspecialchars($listing['price']); ?> / night
        <span class="bx bx-money"></span>
    </div>
    
                        <h2> Book Now: </h2>
                        
                        <div class ="booking">
                            
<div id ="total-cost">Total cost: $0.00</div>
                        <label for="checkInDate">Check In Date:</label>
                        
                <input type="text" id="checkInDate" name="checkInDate" placeholder="Select Check-In Date and Time">

                <label for="checkOutDate">Check Out Date:</label>
                <input type="text" id="checkOutDate" name="checkOutDate" placeholder="Select Check-Out Date and Time">


            
                    <div id="cost-for-stay">Cost for Stay: $0.00</div>
                    <div class="cleaning-fee">Cleaning Fee: $60.00</div>
                <div id="service-fee">Service Fee (15%): $0.00</div> 
                <div id="taxes">Taxes: $0.00</div>
                
                
                



<?php if(isset($_SESSION['clientID']) && $_SESSION['clientID'] == $listing['clientID']): ?>
    
    <form action="../rental_postings/editListing.php" method="get">
       
        <input type="hidden" name="postID" value="<?php echo htmlspecialchars($postID); ?>">
        <button type="submit" class="btn btn-secondary">Modify Listing</button>
    </form>
        <?php else: ?>       
                    <form action="../booking/booking.php" method="post">
                        
                        
  
                        <input type="hidden" name="postID" value="<?php echo htmlspecialchars($postID); ?>">
                        <input type="hidden" name="check_in_date" id="hidden-check-in-date">
                        <input type="hidden" name="check_out_date" id="hidden-check-out-date">

                        <label for="check-in-time">Check-in time:</label>
                        <select id="checkin" name="check-in-time"> <?php echo generate_time_options(); ?></select>
                        <label for="checkout-out-time">Check-out time:</label>
                                <select id="checkout" name="check-out-time"><?php echo generate_time_options(); ?>
                        </select>
                        
                        <input type="hidden" name="total_cost" id="hidden-total-cost">
                        <input type="hidden" name="cost_for_stay" id="hidden-cost-for-stay">
                        <input type="hidden" name="cleaning_fee" id="hidden-cleaning-fee">
                        <input type="hidden" name="service_fee" id="hidden-service-fee">
                        <input type="hidden" name="taxes" id="hidden-taxes">
                        
                        
                        
                        
                        <input type="hidden" name="price_per_night" value="<?php echo $listing['price']; ?>">
                        <input type="hidden" name="address" value="<?php echo $listing['address']; ?>">
                        <input type="hidden" name="city" value="<?php echo $listing['city']; ?>">
                        <input type="hidden" name="province" value="<?php echo $listing['province']; ?>">
                        <input type="hidden" name="postal_code" value="<?php echo $listing['postal_code']; ?>">
                        <input type="hidden" name="bedrooms" value="<?php echo $listing['bedrooms']; ?>">
                        <input type="hidden" name="bathrooms" value="<?php echo $listing['bathrooms']; ?>">
                        <input type="hidden" name="area" value="<?php echo $listing['area']; ?>">
                        
                        <button type="submit" class="btn btn-primary" id="bookNowButton" disabled>Book Now</button>

                        
                    </form>

<?php endif; ?>


                </div>
            
            
    </div>
                    </div>
                        
                        

                    </div>
        
        
            
 

            <?php else: ?>
                <p>Listing not found or invalid listing.</p>
            <?php endif; ?>

            
            
            
            
            
            
            
            

                
                
           
            
            
       
         <?php include '../view/footer.php'; ?>
        
        
         
       



            
    
        
    </body>
</html>





 <script>
document.addEventListener('DOMContentLoaded', function() {
    
    var checkInDatepicker, checkOutDatepicker;

    function updateTotalPrice() {
        var checkInDate = checkInDatepicker.selectedDates[0]; 
        var checkOutDate = checkOutDatepicker.selectedDates[0]; 


       if (checkInDate && checkOutDate) {
            var timeDiff = checkOutDate.getTime() - checkInDate.getTime();
            var daysDiff = timeDiff / (1000 * 3600 * 24);

             daysDiff = Math.ceil(daysDiff);

            if (daysDiff < 1) {
                alert('Check-out date must be after check-in date.');
                return;
            }

            var pricePerNight = <?php echo json_encode($listing['price']); ?>;
            var cleaningFee = <?php echo "60"; ?>;
            var serviceFeePercentage = 0.15; 
            var taxRate = 0.12; 

            var totalPriceForStay = daysDiff * pricePerNight;
            var serviceFee = totalPriceForStay * serviceFeePercentage;
            var taxes = (totalPriceForStay + serviceFee) * taxRate;
            var totalCost = totalPriceForStay + cleaningFee + serviceFee + taxes;

            
            document.getElementById('total-cost').textContent = 'Total cost: $' + totalCost.toFixed(2);
            document.getElementById('service-fee').textContent = 'Service Fee: $' + serviceFee.toFixed(2);
            document.getElementById('taxes').textContent = 'Taxes: $' + taxes.toFixed(2);
            document.getElementById('cost-for-stay').textContent = 'Cost for Stay: $' + totalPriceForStay.toFixed(2);

            
            document.getElementById('hidden-check-in-date').value = checkInDate.toISOString().split('T')[0];
            document.getElementById('hidden-check-out-date').value = checkOutDate.toISOString().split('T')[0];
            document.getElementById('hidden-total-cost').value = totalCost.toFixed(2);
            document.getElementById('hidden-cost-for-stay').value = totalPriceForStay.toFixed(2);
            document.getElementById('hidden-cleaning-fee').value = cleaningFee.toFixed(2);
            document.getElementById('hidden-service-fee').value = serviceFee.toFixed(2);
            document.getElementById('hidden-taxes').value = taxes.toFixed(2);
        }
    }


    

        
        var checkInDatepicker = flatpickr("#checkInDate", {
        
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: unavailableDates.map(date => new Date(date)),
        static: true,
    appendTo: document.querySelector('.BookingInfo'),
        onChange: function(selectedDates) {
            
             updateTotalPrice();
            
            
             const nextUnavailableDate = unavailableDates.find(date => new Date(date) > selectedDates[0]);
             if (nextUnavailableDate) {
                
                var maxDate = new Date(nextUnavailableDate);
                maxDate.setDate(maxDate.getDate() - 1);
                checkOutDatepicker.set("maxDate", maxDate);
            } else {
                
                checkOutDatepicker.set("maxDate", null);
            }
            
            
            if (checkOutDatepicker.selectedDates.length > 0 &&
                selectedDates[0] >= checkOutDatepicker.selectedDates[0]) {
                checkOutDatepicker.clear();
            }
            checkOutDatepicker.set("minDate", selectedDates[0].fp_incr(1));
        }
    });

    var checkOutDatepicker = flatpickr("#checkOutDate", {
    
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1),
        disable: unavailableDates.map(date => new Date(date)),
        static: true,
    appendTo: document.querySelector('.BookingInfo'),
        onChange: function(selectedDates) {
            
           
            
            updateTotalPrice();
        
    }
    });
        
        });

</script>



<script>
    flatpickr("#checkin", {
        dateFormat: "Y-m-d",
        minDate: "today",
        static: true,
        
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
        static: true,
        minDate: new Date().fp_incr(1)
    });
</script>


<script>
   
    function updateButtonState() {
        var checkinValue = document.getElementById('checkInDate').value; 
        var checkoutValue = document.getElementById('checkOutDate').value;
        
    
        document.getElementById('bookNowButton').disabled = !(checkinValue && checkoutValue);
    }

   
    document.getElementById('checkInDate').addEventListener('change', updateButtonState);
    document.getElementById('checkOutDate').addEventListener('change', updateButtonState);

   
    window.onload = updateButtonState;
</script>
