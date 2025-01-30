<?php
include('db.php'); // Database connection
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HOTEL CASABLANCA</title>
    <!-- Bootstrap Styles-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FontAwesome Styles-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom Styles-->
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
    <!-- Google Fonts-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <!-- Google reCAPTCHA JS -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div id="wrapper">
    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li>
                    <a href="../index.php"><i class="fa fa-home"></i> Homepage</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper">
        <div id="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-header">RESERVATION <small></small></h1>
                </div>
            </div>

            <!-- Reservation Form -->
            <div class="row">
                <div class="col-md-5 col-sm-5">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            PERSONAL INFORMATION
                        </div>
                        <div class="panel-body">

                            <?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                // 1. Validate reCAPTCHA
                                $recaptchaResponse = $_POST['g-recaptcha-response'];
                                $secretKey = "YOUR_SECRET_KEY"; // Replace with your reCAPTCHA secret key
                                $verifyURL = "https://www.google.com/recaptcha/api/siteverify";
                                $response = file_get_contents($verifyURL . "?secret=" . $secretKey . "&response=" . $recaptchaResponse);
                                $responseKeys = json_decode($response, true);

                                if (!$responseKeys["success"]) {
                                    echo "<script>alert('reCAPTCHA verification failed. Please try again.');</script>";
                                } else {
                                    // 2. If reCAPTCHA success, proceed with storing the reservation in `roombook`

                                    $check = "SELECT * FROM roombook WHERE email = '$_POST[email]'";
                                    $rs = mysqli_query($con, $check);
                                    $data = mysqli_fetch_array($rs, MYSQLI_NUM);

                                    if ($data[0] > 1) {
                                        echo "<script>alert('User Already Exists');</script>";
                                    } else {
                                        $new = "Not Conform";
                                        $newUser = "INSERT INTO `roombook`(
                                            `Title`, `FName`, `LName`, `Email`, `National`, `Country`, `Phone`,
                                            `TRoom`, `Bed`, `NRoom`, `Meal`, `cin`, `cout`, `stat`, `nodays`
                                        )
                                        VALUES (
                                            '$_POST[title]', '$_POST[fname]', '$_POST[lname]',
                                            '$_POST[email]', '$_POST[nation]', '$_POST[country]',
                                            '$_POST[phone]', '$_POST[troom]', '$_POST[bed]',
                                            '$_POST[nroom]', '$_POST[meal]', '$_POST[cin]', '$_POST[cout]',
                                            '$new', datediff('$_POST[cout]', '$_POST[cin]')
                                        )";

                                        if (mysqli_query($con, $newUser)) {
                                            echo "<script>alert('Your Booking application has been sent');</script>";
                                        } else {
                                            echo "<script>alert('Error adding user in database');</script>";
                                        }
                                    }
                                }
                            }
                            ?>

                            <!-- Reservation Form Fields -->
                            <form name="form" method="post">
                                <div class="form-group">
                                    <label>Title*</label>
                                    <select name="title" class="form-control" required>
                                        <option value selected ></option>
                                        <option value="Dr.">Dr.</option>
                                        <option value="Miss.">Miss.</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Prof.">Prof.</option>
                                        <option value="Rev .">Rev .</option>
                                        <option value="Rev . Fr">Rev . Fr .</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input name="fname" class="form-control" required />
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input name="lname" class="form-control" required />
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input name="email" type="email" class="form-control" required />
                                </div>
                                <div class="form-group">
                                    <label>Nationality*</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="nation" value="Bangladeshi" checked>Bangladeshi
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="nation" value="Non Bangladeshi">Non Bangladeshi
                                    </label>
                                </div>

                                <?php
                                $countries = array(
                                    "Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola",
                                    // ... other countries ...
                                    "Yugoslavia", "Zambia", "Zimbabwe"
                                );
                                ?>
                                <div class="form-group">
                                    <label>Passport Country*</label>
                                    <select name="country" class="form-control" required>
                                        <option value selected ></option>
                                        <?php
                                        foreach ($countries as $value) {
                                            echo "<option value='$value'>$value</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input name="phone" type="text" class="form-control" required />
                                </div>
                               
                                <!-- Reservation Information -->
                                <div class="form-group">
                                    <label>Type Of Room *</label>
                                    <select name="troom" class="form-control" required>
                                        <option value selected ></option>
                                        <option value="Superior Room">SUPERIOR ROOM</option>
                                        <option value="Deluxe Room">DELUXE ROOM</option>
                                        <option value="Guest House">GUEST HOUSE</option>
                                        <option value="Single Room">SINGLE ROOM</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Bedding Type</label>
                                    <select name="bed" class="form-control" required>
                                        <option value selected ></option>
                                        <option value="Single">Single</option>
                                        <option value="Double">Double</option>
                                        <option value="Triple">Triple</option>
                                        <option value="Quad">Quad</option>
                                        <option value="None">None</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>No.of Rooms *</label>
                                    <select name="nroom" class="form-control" required>
                                        <option value selected ></option>
                                        <option value="1">1</option>
                                        <!-- Add more options if needed -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Meal Plan</label>
                                    <select name="meal" class="form-control" required>
                                        <option value selected ></option>
                                        <option value="Room only">Room only</option>
                                        <option value="Breakfast">Breakfast</option>
                                        <option value="Half Board">Half Board</option>
                                        <option value="Full Board">Full Board</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Check-In</label>
                                    <input name="cin" type="date" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Check-Out</label>
                                    <input name="cout" type="date" class="form-control" />
                                </div>

                                <!-- reCAPTCHA Section -->
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                <div class="g-recaptcha" data-sitekey="6LelLqkqAAAAAPWks-vFV4gPbRqNNZzOsHk2NfrN"></div>
                                <br>

                                <button type="submit" class="btn btn-primary">Submit Reservation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Scripts-->
<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.metisMenu.js"></script>
<script src="assets/js/custom-scripts.js"></script>
</body>
</html>
