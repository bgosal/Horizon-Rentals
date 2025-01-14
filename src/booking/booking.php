<?php
   
session_start();


if (!isset($_SESSION['clientID']) || !$_SESSION['clientID']) {
    $postID = $_POST["postID"];
    $bookNowURL = urlencode('../rental_postings/listing.php?postID=' . $postID);
    header('Location: ../registration/login.php?return=' . $bookNowURL);
    exit; 
}
foreach ($_POST as $key => $value) {
    $_SESSION[$key] = $value;
}

?>



<html>
    <head>
        <meta charset="UTF-8">
        <title> Booking Page </title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        
        <link rel ='stylesheet' href ='../style.css'>
        
    </head>
    <?php include '../view/nav.php'; ?>
    <body class = 'mbooking_page'>
        <h1>Booking Summary</h1>
        <div class ="new_container">
            
             <div class="cost-details">
            <h2>Cost Details</h2>
            <div class="detail">
                <strong>Price per Night:</strong>
                <span><?php echo htmlspecialchars($_POST["price_per_night"]); ?></span>
            </div>
            <div class="detail">
                <strong>Cost for Stay:</strong>
                <span><?php echo htmlspecialchars($_POST["cost_for_stay"]); ?></span>
            </div>
            <div class="detail">
                <strong>Cleaning Fee:</strong>
                <span><?php echo htmlspecialchars($_POST["cleaning_fee"]); ?></span>
            </div>
            <div class="detail">
                <strong>Service Fee:</strong>
                <span><?php echo htmlspecialchars($_POST["service_fee"]); ?></span>
            </div>
            <div class="detail">
                <strong>Taxes:</strong>
                <span><?php echo htmlspecialchars($_POST["taxes"]); ?></span>
            </div>
            <div class="detail">
                <strong>Total Cost:</strong>
                <span><?php echo htmlspecialchars($_POST["total_cost"]); ?></span>
            </div>
        </div>
        
        
        
        <div class="booking-details">
            <h2> Booking Details </h2>
        <div class="detail">
            <strong>Check In Date:</strong> <span> <?php echo htmlspecialchars($_POST["check_in_date"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check Out Date:</strong>
        <span><?php echo htmlspecialchars($_POST["check_out_date"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check In Time:</strong>
        <span><?php echo htmlspecialchars($_POST["check-in-time"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check Out Time:</strong>
        <span><?php echo htmlspecialchars($_POST["check-out-time"]); ?></span>
        </div>
        
        
        </div>
        
        
       

        
                <div class="property-info">
            <h2>Property Information</h2>
            <div class="detail">
                <strong>Address:</strong>
                <span><?php echo htmlspecialchars($_POST["address"]); ?></span>
            </div>

            <div class="detail">
                <strong>City:</strong>
                <span><?php echo htmlspecialchars($_POST["city"]); ?></span>
            </div>
            <div class="detail">
                <strong>Province:</strong>
                <span><?php echo htmlspecialchars($_POST["province"]); ?></span>
            </div>
            <div class="detail">
                <strong>Postal Code:</strong>
                <span><?php echo htmlspecialchars($_POST["postal_code"]); ?></span>
            </div>
            <div class="detail">
                <strong>Bedrooms:</strong>
                <span><?php echo htmlspecialchars($_POST["bedrooms"]); ?></span>
            </div>
            <div class="detail">
                <strong>Bathrooms:</strong>
                <span><?php echo htmlspecialchars($_POST["bathrooms"]); ?></span>
            </div>
            <div class="detail">
                <strong>Area:</strong>
                <span><?php echo htmlspecialchars($_POST["area"]); ?> sqft</span>
            </div>

        </div>

        
        </div>
        
        <form action="booking_confirmationDB.php" method="post">
            
            <?php
            
            foreach ($_POST as $key => $value) {
                echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
            }
            ?>

            
            
        <div class="payment-section">
            <h4>Total Cost: $<?php echo htmlspecialchars($_POST["total_cost"]); ?></h4>
            
             <button type="button" class="update-button" onclick="history.back()">Update Booking</button>
                <h3> Payment Information </h3>
            

                <label for="cardName">Name on Card:</label>
            <input type="text" id="cardName" name="cardName" required>
            
            <label for="cardNumber">Card Number:</label>
            <input type="text" id="cardNumber" name="cardNumber" pattern="\d*" minlength="16" maxlength="16" required>
            
            <label for="expDate">Expiry Date (MM/YY):</label>
            <input type="text" id="expDate" name="expDate" pattern="\d{2}/\d{2}" required>
            
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" pattern="\d*" minlength="3" maxlength="3" required>
        
            <h3> Billing Information </h3>
            
            <label for="billingAddress">Billing Address:</label>
            <input type="text" id="billingAddress" name="billingAddress" required>
            
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
            
            <label for="province">Province/State:</label>
            <input type="text" id="province" name="province" required>
            
            <label for="postalCode">Postal Code:</label>
            <input type="text" id="postalCode" name="postalCode" required>
            
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required>
       
        
       
        <button type="submit" class="submit-button">Confirm Payment</button>
    </form>
            
            
        
        
         </div>
        
            
        
        
        
       
</form>
             
        
        
            
            

            
     
            
    
   
        
        
       
    </body>
</html>

