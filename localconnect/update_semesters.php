<?php
include 'db_connect.php'; // Include your DB connection

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    // Define semester mapping
    $semesterMapping = [
        "S1" => 1, "S2" => 2, "S3" => 3, "S4" => 4,
        "S5" => 5, "S6" => 6, "S7" => 7, "S8" => 8
    ];

    if ($action == "promote") {
        $sql = "UPDATE students SET 
                semester = CASE 
                    WHEN semester = 'S8' THEN 'S8' -- If already S8, stay at S8 (no promotion beyond)
                    ELSE CONCAT('S', CAST(SUBSTRING(semester, 2) AS UNSIGNED) + 1) 
                END,
                semester_st = CASE 
                    WHEN semester_st = 8 THEN 8 -- Keep as 8 if max semester reached
                    ELSE semester_st + 1
                END";
    } elseif ($action == "demote") {
        $sql = "UPDATE students SET 
                semester = CASE 
                    WHEN semester = 'S1' THEN 'S1' -- If already S1, stay at S1 (no demotion beyond)
                    ELSE CONCAT('S', CAST(SUBSTRING(semester, 2) AS UNSIGNED) - 1) 
                END,
                semester_st = CASE 
                    WHEN semester_st = 1 THEN 1 -- Keep as 1 if minimum semester reached
                    ELSE semester_st - 1
                END";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit();
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Students updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating students: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
