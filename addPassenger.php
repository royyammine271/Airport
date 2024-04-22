<?php
include 'db.php'; // Include database connection

// Fetch all flights for the dropdown
$sqlFlights = "SELECT flightID, flightName, departureLoc, departureTime, arrivalLoc, arrivalTime FROM Flight";
$flightsResult = $conn->query($sqlFlights);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addPassenger'])) {
    // Sanitize input
    $passportID = htmlspecialchars($_POST['passportID']);
    $fullName = htmlspecialchars($_POST['fullName']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $dob = htmlspecialchars($_POST['dob']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $selectedFlight = htmlspecialchars($_POST['flightID']); // Flight selection

    // Prepare an insert statement for Passenger
    $sql = "INSERT INTO Passenger (passportID, FullName, nationality, dob, phoneNumber) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $passportID, $fullName, $nationality, $dob, $phoneNumber);

    // Attempt to execute
    if ($stmt->execute()) {
        echo "<p class='success'>Passenger added successfully!</p>";

        // If passenger is added successfully, link the passenger to the selected flight
        $sqlLink = "INSERT INTO PassengerToFlight (passportID, flightID) VALUES (?, ?)";
        $stmtLink = $conn->prepare($sqlLink);
        $stmtLink->bind_param("ii", $passportID, $selectedFlight);

        if ($stmtLink->execute()) {
            echo "<p class='success'>Passenger linked to flight successfully!</p>";
        } else {
            echo "<p class='error'>Error linking passenger to flight: " . $stmtLink->error . "</p>";
        }
        $stmtLink->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Passenger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            width: 300px;
            margin: auto;
        }
        input[type=text], input[type=date], select {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Add Passenger</h1>
    <form method="post" action="addPassenger.php">
        Passport ID: <input type="text" name="passportID" required><br>
        Full Name: <input type="text" name="fullName" required><br>
        Nationality: <input type="text" name="nationality" required><br>
        Date of Birth: <input type="date" name="dob" required><br>
        Phone Number: <input type="text" name="phoneNumber" required><br>
        Flight ID: <select name="flightID" required>
            <?php
            if ($flightsResult->num_rows > 0) {
                while ($row = $flightsResult->fetch_assoc()) {
                    echo '<option value="' . $row['flightID'] . '">' . htmlspecialchars($row['flightName']) . ' - From ' . htmlspecialchars($row['departureLoc']) . ' (' . htmlspecialchars($row['departureTime']) . ') to ' . htmlspecialchars($row['arrivalLoc']) . ' (' . htmlspecialchars($row['arrivalTime']) . ')</option>';
                }
            } else {
                echo '<option>No flights available</option>';
            }
            ?>
        </select><br>
        <input type="submit" name="addPassenger" value="Add Passenger">
    </form>
</body>
</html>
