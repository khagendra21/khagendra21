<?php
include 'db.php'; // Database connection

// Check if the ID and table parameters are provided
if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Fetch the current file details
    $query = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $file = $result->fetch_assoc();

    if (!$file) {
        echo "File not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Check if the form has been submitted for updating
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $table = $_POST['table'];
    $newFileName = $file['file_name'];  // Default to current file name
    $filePath = $file['file_path'];     // Default to current file path

    // If a new file is uploaded, replace the old file
    if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] == 0) {
        $uploadedFile = $_FILES['new_file'];
        $fileExt = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];

        if (in_array($fileExt, $allowed)) {
            // Remove the old file from the server
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Upload the new file
            $newFileName = uniqid('', true) . "." . $fileExt;
            $newFilePath = 'uploads/' . $newFileName;
            move_uploaded_file($uploadedFile['tmp_name'], $newFilePath);

            // Update the new file path in the database
            $filePath = $newFilePath;
        } else {
            echo "Invalid file type.";
            exit;
        }
    }

    // Update the database record with the new file details
    $query = $conn->prepare("UPDATE $table SET file_name = ?, file_path = ? WHERE id = ?");
    $query->bind_param("ssi", $newFileName, $filePath, $id);

    if ($query->execute()) {
        echo "File updated successfully!";
    } else {
        echo "Error updating file.";
    }
}
?>

<!-- HTML form to update the file -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update File</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Your custom CSS -->
</head>
<style>

    /* Global styling */
body {
    font-family: 'Roboto', sans-serif;  /* Modern font for a clean look */
    background-color: #f5f5f5;          /* Light background color */
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;                  /* Maximum width of the form container */
    margin: 50px auto;                 /* Center alignment */
    padding: 20px;
    background-color: #fff;            /* White background for the form */
    border-radius: 10px;               /* Soft rounded corners */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);  /* Light shadow for a polished look */
}

/* Header styling */
h1 {
    text-align: center;
    color: #343a40;                    /* Dark, professional header color */
    font-size: 2.2em;
    margin-bottom: 20px;
}

/* Form styling */
form {
    display: flex;
    flex-direction: column;           /* Stack form elements vertically */
    gap: 20px;                        /* Gap between input fields */
}

label {
    font-weight: bold;
    font-size: 1.1em;
    color: #495057;
}

input[type="text"], input[type="file"], select {
    padding: 12px 15px;
    font-size: 1.1em;
    border: 1px solid #ced4da;
    border-radius: 5px;
    width: 100%;
    box-sizing: border-box;           /* Ensures padding doesn't affect width */
    transition: all 0.3s ease;        /* Smooth transition for focus effect */
}

input[type="text"]:focus, input[type="file"]:focus, select:focus {
    border-color: #007bff;            /* Blue border on focus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);  /* Light glow effect on focus */
    outline: none;
}

/* Button styling */
button {
    padding: 12px 20px;
    font-size: 1.2em;
    color: #fff;
    background-color: #28a745;        /* Green button for success (Update) */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

button:hover {
    background-color: #218838;        /* Darker green on hover */
    transform: scale(1.05);           /* Slight scaling on hover */
}

/* Error and success messages */
.error {
    color: #dc3545;                   /* Red for error messages */
    font-size: 1.1em;
    text-align: center;
    margin-bottom: 20px;
}

.success {
    color: #28a745;                   /* Green for success messages */
    font-size: 1.1em;
    text-align: center;
    margin-bottom: 20px;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .container {
        width: 90%;                   /* More compact layout on tablets and small screens */
    }

    h1 {
        font-size: 1.8em;
    }

    button {
        font-size: 1.1em;
        padding: 10px 15px;
    }
}

@media screen and (max-width: 576px) {
    h1 {
        font-size: 1.6em;
    }

    button {
        font-size: 1em;
        padding: 8px 12px;
    }
}
/* Button Base Styling */
.action-button {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1.1em;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease; /* Smooth transitions for hover effects */
    margin-right: 10px; /* Small space between buttons */
    color: #fff; /* White text for contrast */
}

/* Specific Button Colors */
.edit-btn {
    background-color: #17a2b8; /* Teal for Edit */
}

.edit-btn:hover {
    background-color: #138496; /* Darker teal on hover */
}

.view-btn {
    background-color: #007bff; /* Blue for View */
}

.view-btn:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.update-btn {
    background-color: #28a745; /* Green for Update */
}

.update-btn:hover {
    background-color: #218838; /* Darker green on hover */
}

.delete-btn {
    background-color: #dc3545; /* Red for Delete */
}

.delete-btn:hover {
    background-color: #c82333; /* Darker red on hover */
}

/* Add subtle shadow and scaling for hover effect */
.action-button:hover {
    transform: scale(1.05); /* Slight scaling on hover */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Soft shadow on hover */
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .action-button {
        font-size: 1em;
        padding: 8px 16px;
    }
}

@media screen and (max-width: 576px) {
    .action-button {
        font-size: 0.9em;
        padding: 6px 14px;
    }
}

</style>
<body>

<div class="container">
    <h1>Update File</h1>
    <form action="update.php?id=<?php echo $file['id']; ?>&table=<?php echo $table; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
        <input type="hidden" name="table" value="<?php echo $table; ?>">

        <p>Current File: <?php echo $file['file_name']; ?></p>
        
        <label for="new_file">Replace File (optional):</label>
        <input type="file" name="new_file" id="new_file">

        <button type="submit" name="update">Update File</button>
    </form>

    <a href="user.php">Back to File List</a>
</div>

</body>
</html>
