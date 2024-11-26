
<?php
session_start();
include('db.php');
// Check if the user is logged in by verifying the session
if (!isset($_SESSION['username'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> CMS for Course library</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to external CSS -->
</head>
<style>
    /* Basic Reset */
/* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif; /* Modern and elegant font */
    background-color: #f0f4f8; /* Soft background color for clean look */
    color: #333;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Smooth Hover Effect */
.container:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

h1 {
    font-size: 2.5em;
    text-align: center;
    margin-bottom: 30px;
    color: #007bff;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

form {
    display: flex;
    flex-direction: column;
    margin-bottom: 30px;
    gap: 20px; /* Spacing between form elements */
}

label {
    font-size: 1.1em;
    margin-bottom: 5px;
    font-weight: 600;
}

input[type="file"],
select,
button {
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1.1em;
    transition: all 0.3s ease;
}

input[type="file"],
select {
    background-color: #f9f9f9;
}

button {
    background-color: #007bff;
    color: white;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    background-color: #f9f9f9;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

table thead {
    background-color: #007bff;
    color: white;
    text-transform: uppercase;
    font-size: 1.1em;
}

table th, table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table tr:hover {
    background-color: none;
}

th, td {
    font-weight: 500;
}

/* Style for links in the table */
a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color:red;
    font-weight:700;
}

/* Responsive Design using Media Queries */
@media (max-width: 768px) {
    h1 {
        font-size: 2em;
    }

    table, th, td {
        font-size: 0.9em;
    }

    form {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8em;
    }

    table, th, td {
        font-size: 0.8em;
    }

    form {
        gap: 15px;
    }
}

</style>
<body>
    <div class="container">
        <h1>CMS for course library</h1>

        <!-- File Upload Form -->
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="class">Select Class:</label>
            <select name="class" id="class" required>
                <option value="class_10_files">Class 10</option>
                <option value="class_11_science_files">Class 11 Science</option>
                <option value="class_11_management_files">Class 11 Management</option>
                <option value="class_12_science_files">Class 12 Science</option>
                <option value="class_12_management_files">Class 12 Management</option>
                <option value="bit_files">BIT</option>
                <option value="bbs_files">BBS</option>
                <option value="bca_files">BCA</option>
                <option value="bba_files">BBA</option>
            </select>

            <label for="file">Select File:</label>
            <input type="file" name="file" id="file" required>

            <button type="submit" name="submit">Upload File</button>
        </form>

        <!-- File List Table -->
        <h2>Uploaded Files</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>File Name</th>
                    <th>Class</th>
                    <th>View</th>
                    <th>Update</th>
                    <th>Download</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';

                // Define the tables to loop through
                $tables = ['class_10_files', 'class_11_science_files', 'class_11_management_files', 'class_12_science_files', 'class_12_management_files', 'bit_files', 'bbs_files', 'bca_files', 'bba_files'];
                
                foreach ($tables as $table) {
                    $query = $conn->prepare("SELECT * FROM $table");
                    $query->execute();
                    $result = $query->get_result();

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['file_name']}</td>";
                        echo "<td>{$table}</td>";

                        // File view link for PDFs
                        $fileExtension = pathinfo($row['file_name'], PATHINFO_EXTENSION);
                        if (strtolower($fileExtension) == 'pdf') {
                            echo "<td><a href='{$row['file_path']}' target='_blank'>View</a></td>";
                        } else {
                            echo "<td>N/A</td>";
                        }
                        echo "<td><a href='update.php?id={$row['id']}&table=$table'>Edit</a></td>";

                        echo "<td><a href='{$row['file_path']}' download>Download</a></td>";
                        echo "<td><a href='delete.php?id={$row['id']}&table=$table' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
