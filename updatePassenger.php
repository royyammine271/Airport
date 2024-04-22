<?php
include 'db.php'; // Include the database connection file

// Check if the form has been submitted to update the passenger
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Sanitize and validate input data
    $passportID = $_POST['passportID'];
    $fullName = $_POST['fullName'];
    $nationality = $_POST['nationality'];
    $dob = $_POST['dob'];
    $phoneNumber = $_POST['phoneNumber'];

    // Prepare the update statement
    $sql = "UPDATE Passenger SET FullName = ?, nationality = ?, dob = ?, phoneNumber = ? WHERE passportID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fullName, $nationality, $dob, $phoneNumber, $passportID);

    // Execute and check if the update was successful
    if ($stmt->execute()) {
        echo "<p class='success'>Passenger updated successfully.</p>";
    } else {
        echo "<p class='error'>Error updating passenger: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch all passengers to display in the list for selection
$sql = "SELECT passportID, FullName, nationality, dob, phoneNumber FROM Passenger";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Passenger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        form {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
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
    <h1>Update Passenger Details</h1>
    <form action="updatePassenger.php" method="post">
        <select name="passportID" onchange="this.form.submit()">
            <option>Please select a passenger</option>
            <?php
            // Dynamically populate the options from the database
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['passportID']) . '"'
                    . (isset($_POST['passportID']) && $_POST['passportID'] === $row['passportID'] ? ' selected' : '')
                    . '>' . htmlspecialchars($row['FullName']) . '</option>';
            }
            ?>
        </select>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update'])) {
        // Display the details in a form when a passenger is selected
        $sql = "SELECT FullName, nationality, dob, phoneNumber FROM Passenger WHERE passportID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_POST['passportID']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            echo '<form action="updatePassenger.php" method="post">
                <input type="hidden" name="passportID" value="' . htmlspecialchars($_POST['passportID']) . '">
                Full Name: <input type="text" name="fullName" value="' . htmlspecialchars($row['FullName']) . '"><br>
                Nationality: <input type="text" name="nationality" value="' . htmlspecialchars($row['nationality']) . '"><br>
                Date of Birth: <input type="date" name="dob" value="' . htmlspecialchars($row['dob']) . '"><br>
                Phone Number: <input type="text" name="phoneNumber" value="' . htmlspecialchars($row['phoneNumber']) . '"><br>
                <button type="submit" name="update">Update Passenger</button>
            </form>';
        }
        $stmt->close();
    }
    ?>
</body>
</html>

<?php
$conn->close();
?>
