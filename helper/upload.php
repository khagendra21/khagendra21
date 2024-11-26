<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $class = $_POST['class']; // The table selected (e.g., class_10_files)
    $file = $_FILES['file'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'png', 'jpg'];

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 50000000000) { // 5MB size limit
                $fileNewName = uniqid('', true) . "." . $fileExt;
                $fileDestination = 'uploads/' . $fileNewName;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $query = $conn->prepare("INSERT INTO $class (file_name, file_path) VALUES (?, ?)");
                    $query->bind_param("ss", $fileNewName, $fileDestination);
                    $query->execute();

                    header("Location: user.php?uploadsuccess");
                } else {
                    echo "There was an error uploading the file.";
                }
            } else {
                echo "Your file is too large!";
            }
        } else {
            echo "There was an error uploading your file!";
        }
    } else {
        echo "You cannot upload files of this type!";
    }
}
?>
