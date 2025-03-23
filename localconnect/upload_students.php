<?php
header('Content-Type: application/json');
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['students']) || !is_array($data['students'])) {
        echo json_encode(["success" => false, "message" => "Invalid data format"]);
        exit;
    }

    // Mapping semester to semester_st
    $semester_map = [
        'S1' => 1,
        'S2' => 2,
        'S3' => 3,
        'S4' => 4,
        'S5' => 5,
        'S6' => 6,
        'S7' => 7,
        'S8' => 8
    ];

    mysqli_begin_transaction($conn);
    try {
        foreach ($data['students'] as $student) {
            $name = trim($student['name']);
            $username = trim($student['username']);
            $email = trim($student['email']);
            $password = trim($student['password']); // Consider hashing this
            $department_id = trim($student['department_id']);
            $semester = trim($student['semester']);

            // Convert semester to semester_st
            $semester_st = isset($semester_map[$semester]) ? $semester_map[$semester] : null;

            if ($semester_st === null) {
                throw new Exception("Invalid semester value: $semester");
            }

            // Check if department exists
            $checkDeptQuery = "SELECT depid FROM departments WHERE depid = ?";
            $stmtDept = mysqli_prepare($conn, $checkDeptQuery);
            mysqli_stmt_bind_param($stmtDept, "s", $department_id);
            mysqli_stmt_execute($stmtDept);
            mysqli_stmt_store_result($stmtDept);

            if (mysqli_stmt_num_rows($stmtDept) == 0) {
                throw new Exception("Invalid department ID: $department_id");
            }

            // Insert into `users` table
            $userQuery = "INSERT INTO users (username, password, role) VALUES (?, ?, 'student')";
            $stmtUser = mysqli_prepare($conn, $userQuery);
            mysqli_stmt_bind_param($stmtUser, "ss", $username, $password);
            mysqli_stmt_execute($stmtUser);

            if (mysqli_stmt_affected_rows($stmtUser) > 0) {
                $user_id = mysqli_insert_id($conn);

                // Insert into `students` table with semester_st
                $studentQuery = "INSERT INTO students (user_id, name, department_id, email, semester, semester_st) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtStudent = mysqli_prepare($conn, $studentQuery);
                mysqli_stmt_bind_param($stmtStudent, "issssi", $user_id, $name, $department_id, $email, $semester, $semester_st);
                mysqli_stmt_execute($stmtStudent);

                if (mysqli_stmt_affected_rows($stmtStudent) == 0) {
                    throw new Exception("Failed to insert student data");
                }
            } else {
                throw new Exception("Failed to insert user data");
            }
        }

        mysqli_commit($conn);
        echo json_encode(["success" => true, "message" => "Students added successfully"]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
