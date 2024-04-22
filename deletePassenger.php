<?php
include 'db.php'; // Include database connection

// Check if the form is submitted for deleting a passenger
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $passportID = $_POST['passportID'];

    // Prepare a delete statement to remove the selected passenger
    $sql = "DELETE FROM Passenger WHERE passportID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $passportID);

    if ($stmt->execute()) {
        echo "<p class='success'>Passenger deleted successfully.</p>";
    } else {
        echo "<p class='error'>Error deleting passenger: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all passengers for the dropdown
$sql = "SELECT passportID, FullName FROM Passenger";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Passenger</title>
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
        select {
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
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Delete Passenger</h1>
    <form method="post" action="deletePassenger.php">
        Select a passenger to delete:
        <select name="passportID" required>
            <option value="">Please select a passenger</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['passportID'] . '">' . $row['FullName'] . ' (' . $row['passportID'] . ')</option>';
                }
            } else {
                echo '<option>No passengers available</option>';
            }
            ?>
        </select>
        <input type="submit" name="delete" value="Delete Passenger">
    </form>
</body>
</html>
