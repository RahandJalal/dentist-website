<?php
// 1. Set API Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
header("Content-Type: application/json; charset=UTF-8");

// Handle CORS preflight checks
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Put your API Key here
define('CHATBOT_API_KEY', 'AIzaSyAKxkMFjo9xjg4eOZivQ1RM4tNeRU4J-aI');

// 3. Verify API Key
$headers = getallheaders();
$incoming_key = $headers['X-API-KEY'] 
             ?? $headers['x-api-key'] 
             ?? $_GET['api_key'] 
             ?? null;

if (!$incoming_key || $incoming_key !== CHATBOT_API_KEY) {
    http_response_code(401);
    echo json_encode([
        "status"  => "error",
        "message" => "Unauthorized: Invalid or missing API Key."
    ]);
    exit();
}

// 4. Database Connection
$conn = mysqli_connect("localhost", "root", "", "contact_db");

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Database connection error: " . mysqli_connect_error()
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// GET REQUESTS
// ==========================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // Action A: Fetch list of active doctors
    if ($action === 'get_doctors') {
        $query = "SELECT id, name, specialty, experience, bio FROM `doctors` WHERE name NOT IN ('Dr. Amer Hamdani', 'Dr. Fatah Hamdani')";
        $result = mysqli_query($conn, $query);

        $doctors = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $doctors[] = $row;
        }

        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "doctors" => $doctors
        ]);
        exit();
    }

    // Action B: Check booked time slots for a doctor on a specific date
    if ($action === 'check_slots') {
        $doctor = mysqli_real_escape_string($conn, $_GET['doctor'] ?? '');
        $date   = mysqli_real_escape_string($conn, $_GET['date'] ?? '');

        if (empty($doctor) || empty($date)) {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "message" => "'doctor' and 'date' are required."
            ]);
            exit();
        }

        $query = "SELECT time FROM `appointments` WHERE `doctor` = '$doctor' AND `date` = '$date'";
        $result = mysqli_query($conn, $query);

        $booked_slots = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $booked_slots[] = $row['time'];
        }

        http_response_code(200);
        echo json_encode([
            "status"       => "success",
            "doctor"       => $doctor,
            "date"         => $date,
            "booked_slots" => $booked_slots
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid GET action. Valid options: 'get_doctors', 'check_slots'"
    ]);
    exit();
}

// ==========================================
// POST REQUEST (Chat Messages & Booking)
// ==========================================
if ($method === 'POST') {
    $raw_input = file_get_contents("php://input");
    $data = json_decode($raw_input, true);

    if (!$data) {
        $data = $_POST;
    }

    $action = $data['action'] ?? 'book';

    // ------------------------------------------
    // A. Casual Talk & Conversational Messages
    // ------------------------------------------
    if ($action === 'chat') {
        $user_msg = strtolower(trim($data['message'] ?? ''));

        if (empty($user_msg)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "reply" => "Please enter a message."]);
            exit();
        }

        // 1. Basic greetings
        if (preg_match('/\b(hi|hello|hey|salam|marhaba|good morning|good evening)\b/', $user_msg)) {
            $reply = "Hello! Welcome to DentalCare. How can I help you today? You can ask to book an appointment, check our available doctors, or view clinic working hours.";
        }
        // 2. How are you / small talk
        elseif (preg_match('/\b(how are you|how r u|what\'s up|whats up)\b/', $user_msg)) {
            $reply = "I'm doing great, thank you! Ready to help you with your dental appointments. How can I assist you?";
        }
        // 3. Clinic working hours
        elseif (preg_match('/\b(hours|open|opening|close|closing|time)\b/', $user_msg) && !preg_match('/\b(book|appointment)\b/', $user_msg)) {
            $reply = "We are open every day from 09:00 AM to 11:00 PM.";
        }
        // 4. Clinic address & location
        elseif (preg_match('/\b(location|where|address|place|city)\b/', $user_msg)) {
            $reply = "Our clinic is located in Erbil, Kurdistan. Feel free to visit us or book an appointment online!";
        }
        // 5. Inquire about doctors
        elseif (preg_match('/\b(doctor|doctors|dentist|staff|team)\b/', $user_msg)) {
            $doc_res = mysqli_query($conn, "SELECT name, specialty FROM doctors WHERE name NOT IN ('Dr. Amer Hamdani', 'Dr. Fatah Hamdani')");
            $doc_list = [];
            while ($d = mysqli_fetch_assoc($doc_res)) {
                $doc_list[] = "• {$d['name']} ({$d['specialty']})";
            }
            $reply = "Here are our available doctors:\n" . implode("\n", $doc_list) . "\n\nWould you like to schedule a visit with one of them?";
        }
        // 6. Booking intent
        elseif (preg_match('/\b(book|appointment|schedule|reserve)\b/', $user_msg)) {
            $reply = "To book an appointment, click the 'Book Appointment' button or use the form to select your doctor, date, and preferred time slot.";
        }
        // 7. Thanks / Farewell
        elseif (preg_match('/\b(thank|thanks|thx|bye|goodbye|see you)\b/', $user_msg)) {
            $reply = "You're very welcome! Let me know if you need anything else. Have a wonderful day! 😊";
        }
        // Fallback
        else {
            $reply = "I'm here to help! You can ask me about our doctors, clinic hours, location, or how to book an appointment.";
        }

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "reply"  => $reply
        ]);
        exit();
    }

    // ------------------------------------------
    // B. Booking Appointment Submission
    // ------------------------------------------
    $name   = mysqli_real_escape_string($conn, trim($data['name'] ?? ''));
    $email  = mysqli_real_escape_string($conn, trim($data['email'] ?? ''));
    $number = mysqli_real_escape_string($conn, trim($data['number'] ?? ''));
    $doctor = mysqli_real_escape_string($conn, trim($data['doctor'] ?? ''));
    $date   = mysqli_real_escape_string($conn, trim($data['date'] ?? ''));
    $time   = mysqli_real_escape_string($conn, trim($data['time'] ?? ''));

    // Validate fields
    if (empty($name) || empty($email) || empty($number) || empty($doctor) || empty($date) || empty($time)) {
        http_response_code(422);
        echo json_encode([
            "status"  => "error",
            "message" => "Missing required fields: name, email, number, doctor, date, time."
        ]);
        exit();
    }

    // Check if slot is already taken
    $check_query = mysqli_query($conn, "SELECT id FROM `appointments` WHERE `doctor` = '$doctor' AND `date` = '$date' AND `time` = '$time'");
    if (mysqli_num_rows($check_query) > 0) {
        http_response_code(409);
        echo json_encode([
            "status"  => "error",
            "message" => "This time slot is already booked for this doctor."
        ]);
        exit();
    }

    // Save appointment
    $insert_sql = "INSERT INTO `appointments` (name, email, number, doctor, date, time) 
                   VALUES ('$name', '$email', '$number', '$doctor', '$date', '$time')";

    if (mysqli_query($conn, $insert_sql)) {
        http_response_code(201);
        echo json_encode([
            "status"         => "success",
            "message"        => "Appointment booked successfully!",
            "appointment_id" => mysqli_insert_id($conn)
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Failed to book appointment: " . mysqli_error($conn)
        ]);
    }
    exit();
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);

mysqli_close($conn);
?>
