

<style>
/* Add this to your CSS file or inline in view.php */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 20px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

embed {
    display: block;
    margin: 0 auto;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

p {
    text-align: center;
    margin-top: 20px;
    font-size: 18px;
}


    
</style>
<!-- File List Table -->
<h2>Uploaded Files</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Class</th>
            <th>View</th>
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

                // View link for PDFs
                $fileExtension = pathinfo($row['file_name'], PATHINFO_EXTENSION);
                if (strtolower($fileExtension) == 'pdf') {
                    echo "<td><a href='view.php?id={$row['id']}&table=$table' target='_blank'>View</a></td>";
                } else {
                    echo "<td>N/A</td>";
                }

                echo "<td><a href='{$row['file_path']}' download>Download</a></td>";
                echo "<td><a href='delete.php?id={$row['id']}&table=$table' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
