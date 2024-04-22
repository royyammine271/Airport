<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $airlineID = htmlspecialchars($_POST['airlineID']);
    $name = htmlspecialchars($_POST['name']);
    $fleetSize = htmlspecialchars($_POST['fleetSize']);
    $countryOfOrigin = htmlspecialchars($_POST['countryOfOrigin']);

    $sql = "INSERT INTO Airlines (airlineID, name, FleetSize, countryOfOrigin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $airlineID, $name, $fleetSize, $countryOfOrigin);

    if ($stmt->execute()) {
        echo "<p class='success'>Airline added successfully!</p>";
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
    <title>Add Airline</title>
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
        input[type=text], input[type=number] {
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
    <h1>Add Airline</h1>
    <form method="post" action="addAirline.php">
        Airline ID: <input type="number" name="airlineID" required><br>
        Name: <input type="text" name="name" required><br>
        Fleet Size: <input type="number" name="fleetSize" required><br>
        Country of Origin: <input type="text" name="countryOfOrigin" required><br>
        <input type="submit" value="Add Airline">
    </form>
</body>
</html>
