<?php
session_start();
require_once '../config/db.php'; // use your old db.php

// Function to generate next ID number
function getNextIdNumber($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id_number FROM users ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last && isset($last['id_number'])) {
            $num = intval(substr($last['id_number'], 5)) + 1; // get number after "2025-"
            return "2025-" . str_pad($num, 4, "0", STR_PAD_LEFT);
        } else {
            return "2025-0001";
        }

    } catch (PDOException $e) {
        return "2025-0001"; // fallback if something goes wrong
    }
}

// Example: auto-fill ID number for form
$nextIdNumber = getNextIdNumber($pdo);

// Create users table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_number VARCHAR(20) NOT NULL UNIQUE,
            first_name VARCHAR(50) NOT NULL,
            middle_name VARCHAR(50),
            last_name VARCHAR(50) NOT NULL,
            extension_name VARCHAR(10),
            sex VARCHAR(10) NOT NULL,
            birthdate DATE NOT NULL,
            age INT,
            purok VARCHAR(50),
            barangay VARCHAR(50),
            city VARCHAR(50),
            province VARCHAR(50),
            country VARCHAR(50),
            zip_code VARCHAR(10),
            email VARCHAR(100) NOT NULL UNIQUE,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            a1_question VARCHAR(100),
            a1_answer VARCHAR(100),
            a2_question VARCHAR(100),
            a2_answer VARCHAR(100),
            a3_question VARCHAR(100),
            a3_answer VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    die("Table creation failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = $_POST['id_number'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'];
    $extension_name = $_POST['extension_name'] ?? '';
    $sex = $_POST['sex'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'] ?? null;
    $purok = $_POST['purok'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $a1_question = $_POST['a1_question'];
    $a1_answer = $_POST['a1_answer'];
    $a2_question = $_POST['a2_question'];
    $a2_answer = $_POST['a2_answer'];
    $a3_question = $_POST['a3_question'];
    $a3_answer = $_POST['a3_answer'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users
            (id_number, first_name, middle_name, last_name, extension_name, sex, birthdate, age, purok, barangay, city, province, country, zip_code, email, username, password_hash, a1_question, a1_answer, a2_question, a2_answer, a3_question, a3_answer)
            VALUES
            (:id_number, :first_name, :middle_name, :last_name, :extension_name, :sex, :birthdate, :age, :purok, :barangay, :city, :province, :country, :zip_code, :email, :username, :password, :a1_question, :a1_answer, :a2_question, :a2_answer, :a3_question, :a3_answer)
        ");
        $stmt->execute([
            ':id_number' => $id_number,
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':last_name' => $last_name,
            ':extension_name' => $extension_name,
            ':sex' => $sex,
            ':birthdate' => $birthdate,
            ':age' => $age,
            ':purok' => $purok,
            ':barangay' => $barangay,
            ':city' => $city,
            ':province' => $province,
            ':country' => $country,
            ':zip_code' => $zip_code,
            ':email' => $email,
            ':username' => $username,
            ':password' => $password,
            ':a1_question' => $a1_question,
            ':a1_answer' => $a1_answer,
            ':a2_question' => $a2_question,
            ':a2_answer' => $a2_answer,
            ':a3_question' => $a3_question,
            ':a3_answer' => $a3_answer
        ]);

        $_SESSION['success'] = "Registration successful! You can now login.";
        echo json_encode(['success' => true, 'message' => "Registration successful! You can now login."]);
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Registration failed: " . $e->getMessage();
        error_log($errorMsg);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Brewstack Coffee - Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/register.css">

</head>

<body>

    <div class="page-wrapper">

        <header class="d-flex justify-content-between align-items-center">
            <h4 class="m-0">Brewstack Coffee</h4>

            <?php if (!empty($_SESSION['username'])): ?>
            <form action="logout.php" method="post">
                <button type="submit" class="btn btn-sm btn-secondary">Logout</button>
            </form>
            <?php else: ?>
            <a href="login.php" class="btn btn-sm btn-warning">Login</a>
            <?php endif; ?>
        </header>

        <div class="register-container ">
            <div class="register-box ">
                <h2>Registration Form</h2>

                <div class="progress-container">
                    <div class="step-circle active">1</div>
                    <div class="step-line"></div>
                    <div class="step-circle">2</div>
                    <div class="step-line"></div>
                    <div class="step-circle">3</div>
                </div>
                <div class="step-labels">
                    <span>Personal Info</span>
                    <span>Address</span>
                    <span>Credentials & Security</span>
                </div>

                <form id="register-form" action="" method="post">
                    <div class="form-step active" id="step-1">
                        <!-- PERSONAL INFO -->
                        <h3>Personal Information</h3>
                        <div class="row g-2">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>ID No. <span class="span">*</span></label>
                                    <input type="text" class="form-control " id="id_number" name="id_number" readonly
                                        value="<?php echo $nextIdNumber; ?>" data-validate="required|id_number">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>First Name <span class="span">*</span></label>
                                    <input type="text" class="form-control " id="first_name" name="first_name" required
                                        data-validate="required|first_name" placeholder="First Name">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Middle Name <span class="optional">(optional)</span></label>
                                    <input type="text" class="form-control " id="middle_name" name="middle_name"
                                        data-validate="middle_name" placeholder="Middle Name">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Last Name <span class="span">*</span></label>
                                    <input type="text" class="form-control " id="last_name" name="last_name" required
                                        data-validate="required|last_name" placeholder="Last Name">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Extension <span class="optional">(optional)</span></label>
                                    <select id="extension_name" class="form-control " name="extension_name"
                                        data-validate="">
                                        <option value="">None</option>
                                        <option value="Jr">Jr</option>
                                        <option value="Sr">Sr</option>
                                    </select>
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sex <span class="span">*</span></label>
                                    <select id="sex" class="form-control " name="sex" required data-validate="required">
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Birthdate <span class="span">*</span></label>
                                    <input type="date" class="form-control " id="birthdate" name="birthdate" required
                                        data-validate="required|birthdate">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Age <span class="span">*</span></label>
                                    <input type="text" class="form-control " id="age" name="age" readonly
                                        data-validate="required|age" placeholder="Age">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-next">Next &rarr;</button>
                        </div>
                    </div>

                    <div class="form-step" id="step-2">
                        <!-- ADDRESS -->
                        <h3>Address</h3>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Purok <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="purok" name="purok" required
                                        data-validate="required|purok">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Barangay <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="barangay" name="barangay" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>City <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Province <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="province" name="province" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Country <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="country" name="country" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Zip Code <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code" required
                                        data-validate="required|zip_code">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn-prev">&larr; Previous</button>
                            <button type="button" class="btn-next">Next &rarr;</button>
                        </div>
                    </div>

                    <div class="form-step" id="step-3">
                        <!-- ACCOUNT CREDENTIALS -->
                        <h3>Account Credentials & Security</h3>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="span">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                        data-validate="required|email">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="span">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required
                                        data-validate="required|username">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <span class="span">*</span></label>
                                    <div class="password-container">
                                        <input type="password" class="form-control" id="password" name="password"
                                            required data-validate="required|password">
                                        <button type="button" class="toggle-password-btn" data-type="password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password <span class="span">*</span></label>
                                    <div class="password-container">
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" required data-validate="required|confirm_password">
                                        <button type="button" class="toggle-password-btn" data-type="confirm_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                        </div>

                        <!-- SECURITY QUESTIONS -->
                        <h3>Security Questions</h3>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Question 1 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a1_question" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Answer 1 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a1_answer" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Question 2 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a2_question" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Answer 2 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a2_answer" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Question 3 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a3_question" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Answer 3 <span class="span">*</span></label>
                                    <input type="text" class="form-control" name="a3_answer" required
                                        data-validate="required">
                                    <small class="error-message invalid-feedback"></small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn-prev">&larr; Previous</button>
                            <button type="submit" class="btn-register">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer>
            <p>&copy; 2025 Brewstack Coffee — All rights reserved.</p>
        </footer>

    </div>



    <script type="module" src="../js/register.js"></script>
</body>

</html>