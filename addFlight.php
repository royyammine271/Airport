<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flightName = htmlspecialchars($_POST['flightName']);
    $flightID = htmlspecialchars($_POST['flightID']);
    $departureTime = htmlspecialchars($_POST['departureTime']);
    $arrivalTime = htmlspecialchars($_POST['arrivalTime']);
    $departureLoc = htmlspecialchars($_POST['departureLoc']);
    $arrivalLoc = htmlspecialchars($_POST['arrivalLoc']);
    $distance = htmlspecialchars($_POST['distance']);
    $airlineID = htmlspecialchars($_POST['airlineID']);

    // Prepare an insert statement for the new flight details
    $sql = "INSERT INTO Flight (flightName, flightID, departureTime, arrivalTime, departureLoc, arrivalLoc, distance, AirlineID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssi", $flightName, $flightID, $departureTime, $arrivalTime, $departureLoc, $arrivalLoc, $distance, $airlineID);

    if ($stmt->execute()) {
        echo "<p class='success'>Flight added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all airlines for the dropdown
$sql = "SELECT airlineID, name FROM Airlines";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Flight</title>
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
            width: 350px;
            margin: auto;
        }
        input[type=text], input[type=datetime-local], input[type=number], select {
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
    <h1>Add Flight</h1>
    <form method="post" action="addFlight.php">
        Flight Name: <input type="text" name="flightName" required><br>
        Flight ID: <input type="number" name="flightID" required><br>
        Departure Time: <input type="datetime-local" name="departureTime" required><br>
        Arrival Time: <input type="datetime-local" name="arrivalTime" required><br>
        Departure Location: <input type="text" name="departureLoc" required><br>
        Arrival Location: <input type="text" name="arrivalLoc" required><br>
        Distance (in km): <input type="number" name="distance" required><br>
        Airline: <select name="airlineID" required>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['airlineID'] . '">' . $row['name'] . '</option>';
                }
            } else {
                echo '<option value="">No airlines available</option>';
            }
            ?>
        </select><br>
        <input type="submit" value="Add Flight">
    </form>
</body>
</html>
