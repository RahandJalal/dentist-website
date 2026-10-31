<?php

$conn = mysqli_connect("localhost", "root", "", "contact_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $doctor = mysqli_real_escape_string($conn, $_POST['doctor']);
    $date   = mysqli_real_escape_string($conn, $_POST['date']);
    $time   = mysqli_real_escape_string($conn, $_POST['time']);

    $sql = "INSERT INTO appointments (name, email, number, doctor, date, time)
            VALUES ('$name', '$email', '$number', '$doctor', '$date', '$time')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Database error: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dental Care</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Scrollable Doctor Cards Carousel */
      .doctor-scroll-wrapper {
         width: 100%;
         max-width: 1200px;
         margin: 0 auto;
         padding: 10px 15px 25px;
         overflow-x: auto;
         -webkit-overflow-scrolling: touch;
      }

      .doctor-scroll-wrapper::-webkit-scrollbar {
         height: 8px;
      }
      .doctor-scroll-wrapper::-webkit-scrollbar-track {
         background: #eef2f5;
         border-radius: 10px;
      }
      .doctor-scroll-wrapper::-webkit-scrollbar-thumb {
         background: #00b8b8;
         border-radius: 10px;
      }

      .doctor-scroll-container {
         display: flex !important;
         flex-wrap: nowrap !important;
         gap: 20px;
         padding-bottom: 10px;
      }

      .custom-doc-card {
         flex: 0 0 240px !important;
         min-width: 240px !important;
         max-width: 240px !important;
         background: #fff;
         border-radius: 16px;
         padding: 20px 15px;
         box-shadow: 0 5px 15px rgba(0,0,0,0.08);
         text-align: center;
         border: 1px solid #edf2f7;
         transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .custom-doc-card:hover {
         transform: translateY(-4px);
         box-shadow: 0 8px 20px rgba(0,0,0,0.12);
      }

      .custom-doc-badge {
         display: inline-block;
         background: #e6f7f7;
         color: #00b8b8;
         font-size: 12px;
         font-weight: 600;
         padding: 4px 10px;
         border-radius: 20px;
         margin-bottom: 12px;
      }

      .custom-doc-img {
         width: 90px;
         height: 90px;
         margin: 0 auto 12px;
         border-radius: 50%;
         overflow: hidden;
         border: 3px solid #00b8b8;
      }

      .custom-doc-img img {
         width: 100%;
         height: 100%;
         object-fit: cover;
      }

      .custom-doc-card h3 {
         font-size: 16px;
         font-weight: 700;
         color: #333;
         margin-bottom: 4px;
      }

      .custom-doc-spec {
         font-size: 13px;
         color: #00b8b8;
         font-weight: 600;
         margin-bottom: 8px;
      }

      .custom-doc-bio {
         font-size: 12px;
         color: #777;
         line-height: 1.4;
         margin-bottom: 0;
      }

      /* Appointment Modal Popup Styles */
      .appointment-modal-overlay {
         position: fixed !important;
         top: 0 !important;
         left: 0 !important;
         width: 100vw !important;
         height: 100vh !important;
         background: rgba(0, 0, 0, 0.65) !important;
         backdrop-filter: blur(4px);
         display: none;
         align-items: center;
         justify-content: center;
         z-index: 9999999 !important;
         padding: 20px;
      }

      .appointment-modal-overlay.active {
         display: flex !important;
      }

      .appointment-modal-box {
         background: #fff !important;
         border-radius: 20px !important;
         width: 100% !important;
         max-width: 500px !important;
         max-height: 90vh !important;
         overflow-y: auto !important;
         padding: 30px 25px !important;
         position: relative !important;
         box-shadow: 0 15px 35px rgba(0,0,0,0.3) !important;
         animation: modalPop 0.25s ease-out;
      }

      @keyframes modalPop {
         from { transform: scale(0.92); opacity: 0; }
         to { transform: scale(1); opacity: 1; }
      }

      .appointment-modal-close {
         position: absolute !important;
         top: 15px !important;
         right: 20px !important;
         background: none !important;
         border: none !important;
         font-size: 32px !important;
         color: #777 !important;
         cursor: pointer !important;
         line-height: 1 !important;
      }

      .appointment-modal-close:hover {
         color: #000 !important;
      }

      .appointment-modal-box h2 {
         text-align: center;
         font-size: 24px;
         color: #333;
         margin-bottom: 20px;
         text-transform: uppercase;
         font-weight: 700;
      }

      .appointment-modal-box span {
         display: block;
         font-size: 14px;
         color: #555;
         margin-bottom: 6px;
         text-transform: capitalize;
         font-weight: 500;
      }

      .appointment-modal-box .box {
         width: 100%;
         margin-bottom: 14px;
         padding: 12px 14px;
         border: 1px solid #ddd;
         border-radius: 10px;
         font-size: 14px;
         outline: none;
      }

      .appointment-modal-box .box:focus {
         border-color: #00b8b8;
      }

      .appointment-modal-box .submit-btn {
         width: 100%;
         background: #00b8b8;
         color: #fff;
         padding: 14px;
         border-radius: 10px;
         border: none;
         font-size: 16px;
         font-weight: 600;
         cursor: pointer;
         margin-top: 10px;
         text-transform: uppercase;
         transition: background 0.2s ease;
      }

      .appointment-modal-box .submit-btn:hover {
         background: #009999;
      }

      /* Chatbot UI */
      .chat-toggle {
         position: fixed !important;
         bottom: 20px !important;
         right: 20px !important;
         width: 60px !important;
         height: 60px !important;
         border-radius: 50% !important;
         background: #00b8b8 !important;
         color: #fff !important;
         display: flex !important;
         align-items: center !important;
         justify-content: center !important;
         font-size: 24px !important;
         cursor: pointer !important;
         box-shadow: 0 10px 20px rgba(0,0,0,.2) !important;
         z-index: 999999 !important;
      }

      .chat-widget {
         position: fixed !important;
         bottom: 90px !important;
         right: 20px !important;
         width: 380px !important;
         max-width: calc(100% - 20px) !important;
         background: #fff !important;
         border-radius: 20px !important;
         box-shadow: 0 12px 30px rgba(0,0,0,.18) !important;
         overflow: hidden !important;
         display: none !important;
         flex-direction: column !important;
         z-index: 999999 !important;
         border: 1px solid #ddd !important;
      }

      .chat-widget.active {
         display: flex !important;
      }

      .chat-header {
         display: flex !important;
         align-items: center !important;
         justify-content: space-between !important;
         padding: 15px !important;
         background: #fff !important;
         border-bottom: 1px solid #eee !important;
      }

      .chat-header-info {
         display: flex !important;
         align-items: center !important;
         gap: 10px !important;
      }

      .chat-avatar {
         width: 50px !important;
         height: 50px !important;
         border-radius: 50% !important;
         overflow: hidden !important;
      }

      .chat-avatar img {
         width: 100% !important;
         height: 100% !important;
         object-fit: cover !important;
         display: block !important;
      }

      .chat-header h3 {
         font-size: 20px !important;
         color: #333 !important;
         margin: 0 !important;
         text-transform: none !important;
      }

      .chat-header span {
         font-size: 14px !important;
         color: #2bb673 !important;
         text-transform: none !important;
      }

      .chat-close {
         background: none !important;
         font-size: 30px !important;
         color: #777 !important;
         cursor: pointer !important;
         border: none !important;
      }

      .chat-body {
         padding: 15px !important;
         background: #f8f9fb !important;
         max-height: 420px !important;
         overflow-y: auto !important;
         display: flex !important;
         flex-direction: column !important;
         gap: 12px !important;
      }

      .bot-message,
      .user-message {
         max-width: 85% !important;
         padding: 12px 15px !important;
         border-radius: 16px !important;
         font-size: 15px !important;
         line-height: 1.6 !important;
         text-transform: none !important;
         word-break: break-word !important;
      }

      .bot-message {
         background: #ececf1 !important;
         color: #333 !important;
         align-self: flex-start !important;
      }

      .user-message {
         background: #00b8b8 !important;
         color: #fff !important;
         align-self: flex-end !important;
      }

      .chat-options {
         display: flex !important;
         flex-wrap: wrap !important;
         gap: 10px !important;
      }

      .chat-options button {
         padding: 10px 16px !important;
         border-radius: 30px !important;
         border: 1px solid #cfd8dc !important;
         background: #fff !important;
         color: #00b8b8 !important;
         font-size: 14px !important;
         cursor: pointer !important;
      }

      .chat-options button:hover {
         background: #00b8b8 !important;
         color: #fff !important;
      }

      .chat-footer {
         display: flex !important;
         gap: 10px !important;
         padding: 12px !important;
         border-top: 1px solid #eee !important;
         background: #fff !important;
      }

      .chat-footer input {
         flex: 1 !important;
         padding: 12px 14px !important;
         border-radius: 30px !important;
         border: 1px solid #ddd !important;
         font-size: 15px !important;
         text-transform: none !important;
      }

      .chat-footer button {
         padding: 10px 18px !important;
         border-radius: 30px !important;
         background: #00b8b8 !important;
         color: #fff !important;
         font-size: 15px !important;
         cursor: pointer !important;
         border: none !important;
      }
   </style>
</head>
<body>

<header class="header fixed-top">
   <div class="container">
      <div class="row align-items-center justify-content-between">
         <a href="#home" class="logo">dental<span>Care.</span></a>

         <nav class="nav">
            <a href="#home">home</a>
            <a href="#about">about</a>
            <a href="#services">services</a>
            <a href="#reviews">reviews</a>
            <a href="#doctor-profile">doctor</a>
         </nav>

         <button type="button" class="link-btn" onclick="openAppointmentModal()">make appointment</button>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>
   </div>
</header>

<section class="home" id="home">
   <div class="container">
      <div class="row min-vh-100 align-items-center">
         <div class="content text-center text-md-left">
            <h3>let us brighten your smile</h3>
            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laudantium itaque, quasi aliquam alias tempora voluptatibus.</p>
            <button type="button" class="link-btn" onclick="openAppointmentModal()">make appointment</button>
         </div>
      </div>
   </div>
</section>

<section class="about" id="about">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-md-6 image">
            <img src="images/about-img.jpg" class="w-100 mb-5 mb-md-0" alt="">
         </div>

         <div class="col-md-6 content">
            <span>about us</span>
            <h3>True Healthcare For Your Family</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laboriosam cupiditate vero in provident ducimus. Totam quas labore mollitia cum nisi, sint, expedita rem error ipsa, nesciunt ab provident. Aperiam, officiis!</p>
            <button type="button" class="link-btn" onclick="openAppointmentModal()">make appointment</button>
         </div>
      </div>
   </div>
</section>

<section class="services" id="services">
   <h1 class="heading">our services</h1>

   <div class="box-container container">
      <div class="box">
         <img src="images/icon-1.svg" alt="">
         <h3>Alignment specialist</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat, non?</p>
      </div>

      <div class="box">
         <img src="images/icon-2.svg" alt="">
         <h3>Cosmetic dentistry</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat, non?</p>
      </div>

      <div class="box">
         <img src="images/icon-3.svg" alt="">
         <h3>Oral hygiene experts</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima?</p>
      </div>

      <div class="box">
         <img src="images/icon-4.svg" alt="">
         <h3>Root canal specialist</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima?</p>
      </div>

      <div class="box">
         <img src="images/icon-5.svg" alt="">
         <h3>Live dental advisory</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima?</p>
      </div>

      <div class="box">
         <img src="images/icon-6.svg" alt="">
         <h3>Cavity inspection</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima?</p>
      </div>
   </div>
</section>

<section class="process">
   <h1 class="heading">work process</h1>

   <div class="box-container container">
      <div class="box">
         <img src="images/process-1.png" alt="">
         <h3>Cosmetic Dentistry</h3>
         <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, excepturi?</p>
      </div>

      <div class="box">
         <img src="images/process-2.png" alt="">
         <h3>Pediatric Dentistry</h3>
         <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, excepturi?</p>
      </div>

      <div class="box">
         <img src="images/process-3.png" alt="">
         <h3>Dental Implants</h3>
         <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maxime, excepturi?</p>
      </div>
   </div>
</section>

<section class="reviews" id="reviews">
   <h1 class="heading">satisfied clients</h1>

   <div class="box-container container">
      <div class="box">
         <img src="images/pic-1.png" alt="">
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eos, iure? Nemo est aspernatur voluptatum id, laboriosam asperiores iure omnis alias?</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>john deo</h3>
         <span>satisfied client</span>
      </div>

      <div class="box">
         <img src="images/pic-2.png" alt="">
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eos, iure? Nemo est aspernatur voluptatum id, laboriosam asperiores iure omnis alias?</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>john deo</h3>
         <span>satisfied client</span>
      </div>

      <div class="box">
         <img src="images/pic-3.png" alt="">
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eos, iure? Nemo est aspernatur voluptatum id, laboriosam asperiores iure omnis alias?</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>john deo</h3>
         <span>satisfied client</span>
      </div>
   </div>
</section>

<!-- DOCTOR PROFILES (HORIZONTAL SCROLL CAROUSEL) -->
<section class="doctor-profile" id="doctor-profile">
   <h1 class="heading">our doctors</h1>

   <div class="doctor-scroll-wrapper">
      <div class="doctor-scroll-container">
         <?php
            $doctors_res = mysqli_query($conn, "SELECT * FROM `doctors` WHERE name NOT IN ('Dr. Amer Hamdani', 'Dr. Fatah Hamdani')");
            if ($doctors_res && mysqli_num_rows($doctors_res) > 0) {
               while ($doc = mysqli_fetch_assoc($doctors_res)) {
                  $doc_name = htmlspecialchars($doc['name']);
                  $doc_specialty = htmlspecialchars($doc['specialty']);
                  $doc_bio = htmlspecialchars($doc['bio'] ?? 'Specialized dental care.');
                  $doc_image = !empty($doc['image']) ? htmlspecialchars($doc['image']) : 'about-img.jpg';
         ?>
            <div class="custom-doc-card">
               <span class="custom-doc-badge">
                  <?php echo htmlspecialchars($doc['experience'] ?? 'Specialist'); ?>
               </span>

               <div class="custom-doc-img">
                  <img src="images/<?php echo $doc_image; ?>" alt="<?php echo $doc_name; ?>" onerror="this.src='images/about-img.jpg';">
               </div>

               <h3><?php echo $doc_name; ?></h3>
               <p class="custom-doc-spec"><?php echo $doc_specialty; ?></p>
               <p class="custom-doc-bio"><?php echo $doc_bio; ?></p>
            </div>
         <?php 
               }
            } else {
               echo "<p class='text-center w-100'>No doctors available right now.</p>";
            }
         ?>
      </div>
   </div>
</section>

<!-- APPOINTMENT MODAL POPUP (OPENS ON CLICK) -->
<div class="appointment-modal-overlay" id="appointmentModal">
   <div class="appointment-modal-box">
      <button type="button" class="appointment-modal-close" onclick="closeAppointmentModal()">&times;</button>
      <h2>make appointment</h2>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="appointment-form">
         <span>your name :</span>
         <input type="text" name="name" placeholder="enter your name" class="box" required>

         <span>your email :</span>
         <input type="email" name="email" placeholder="enter your email" class="box" required>

         <span>your number :</span>
         <input type="text" name="number" placeholder="enter your number" class="box" required>

         <span>choose doctor :</span>
         <select name="doctor" class="box" id="doctor-select" required>
            <option value="">select doctor</option>
            <?php
               $select_doc_query = mysqli_query($conn, "SELECT name, specialty FROM `doctors` WHERE name NOT IN ('Dr. Amer Hamdani', 'Dr. Fatah Hamdani')");
               if ($select_doc_query) {
                  while ($doc_row = mysqli_fetch_assoc($select_doc_query)) {
                     $d_name = htmlspecialchars($doc_row['name']);
                     $d_spec = htmlspecialchars($doc_row['specialty']);
                     echo "<option value='{$d_name}'>{$d_name} - {$d_spec}</option>";
                  }
               }
            ?>
         </select>

         <span>appointment date :</span>
         <input type="date" name="date" class="box" required>

         <span>appointment time (9:00 AM - 11:00 PM) :</span>
         <select name="time" class="box" id="appointment-time" required>
            <option value="" disabled selected>select time slot</option>
            <option value="09:00 AM">09:00 AM</option>
            <option value="09:30 AM">09:30 AM</option>
            <option value="10:00 AM">10:00 AM</option>
            <option value="10:30 AM">10:30 AM</option>
            <option value="11:00 AM">11:00 AM</option>
            <option value="11:30 AM">11:30 AM</option>
            <option value="12:00 PM">12:00 PM</option>
            <option value="12:30 PM">12:30 PM</option>
            <option value="01:00 PM">01:00 PM</option>
            <option value="01:30 PM">01:30 PM</option>
            <option value="02:00 PM">02:00 PM</option>
            <option value="02:30 PM">02:30 PM</option>
            <option value="03:00 PM">03:00 PM</option>
            <option value="03:30 PM">03:30 PM</option>
            <option value="04:00 PM">04:00 PM</option>
            <option value="04:30 PM">04:30 PM</option>
            <option value="05:00 PM">05:00 PM</option>
            <option value="05:30 PM">05:30 PM</option>
            <option value="06:00 PM">06:00 PM</option>
            <option value="06:30 PM">06:30 PM</option>
            <option value="07:00 PM">07:00 PM</option>
            <option value="07:30 PM">07:30 PM</option>
            <option value="08:00 PM">08:00 PM</option>
            <option value="08:30 PM">08:30 PM</option>
            <option value="09:00 PM">09:00 PM</option>
            <option value="09:30 PM">09:30 PM</option>
            <option value="10:00 PM">10:00 PM</option>
            <option value="10:30 PM">10:30 PM</option>
            <option value="11:00 PM">11:00 PM</option>
         </select>

         <input type="submit" value="make appointment" name="submit" class="submit-btn">
      </form>
   </div>
</div>

<section class="footer">
   <div class="box-container container">
      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>phone number</h3>
         <p>+123-456-7890</p>
         <p>+111-222-3333</p>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>our address</h3>
         <p>erbil,kurdistan</p>
      </div>

      <div class="box">
         <i class="fas fa-clock"></i>
         <h3>opening hours</h3>
         <p>9:00am to 11:00pm</p>
      </div>

      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>email address</h3>
      </div>
   </div>
</section>

<div class="chat-toggle" id="chat-toggle">
   <i class="fas fa-comments"></i>
</div>

<div class="chat-widget" id="chat-widget">
   <div class="chat-header">
      <div class="chat-header-info">
         <div class="chat-avatar">
            <img src="images/about-img.jpg" alt="Assistant">
         </div>
         <div>
            <h3>DentalCare Assistant</h3>
            <span>Online Now</span>
         </div>
      </div>
      <button class="chat-close" id="chat-close">&times;</button>
   </div>

   <div class="chat-body" id="chat-body">
      <div class="bot-message">Hello! How can I assist you today?</div>

      <div class="chat-options">
         <button type="button" onclick="openAppointmentModal()">Book Appointment</button>
         <button type="button" onclick="showServices()">Our Services</button>
         <button type="button" onclick="showContact()">Contact Info</button>
      </div>
   </div>

   <div class="chat-footer">
      <input type="text" id="chat-input" placeholder="Write a message...">
      <button type="button" id="chat-send">Send</button>
   </div>
</div>



<script src="js/script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
<?php
mysqli_close($conn);
?>