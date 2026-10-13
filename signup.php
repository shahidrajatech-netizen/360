
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $roll = trim($_POST["roll"] ?? "");
    $dob = $_POST["dob"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $address = trim($_POST["address"] ?? "");
    $branch = trim($_POST["branch"] ?? "");
    $year = $_POST["year"] ?? "";
    $section = trim($_POST["section"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $fullname === "" || $roll === "" ||
        $dob === "" || $email === "" || $password === ""
    ) {
        $message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO students
            (full_name, roll_no, email, dob, gender, address,
             branch, study_year, section, phone, password_hash)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Lib360 prepare failed: " . $conn->error);
            $message = "Registration could not be processed. Please try again.";
        } else {
            $stmt->bind_param(
                "sssssssssss",
                $fullname, $roll, $email, $dob, $gender,
                $address, $branch, $year, $section, $phone,
                $passwordHash
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: success.html");
                exit;
            }

            if ($stmt->errno === 1062) {
                $message = "This roll number or email is already registered.";
            } else {
                error_log("Lib360 registration failed: " . $stmt->error);
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Student Registration</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Playfair Display", serif;
    background: url('bg.jpg') no-repeat center/cover;
    padding: 40px;
}

.container {
    max-width: 800px;
    margin: auto;
}

form {
    background: white;
    padding: 30px;
    border-radius: 10px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.full {
    grid-column: 1 / -1;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
}

textarea {
    min-height: 80px;
}

button {
    padding: 12px;
    background: black;
    color: white;
    border: none;
    cursor: pointer;
    width: 100%;
}

.section-title {
    margin-top: 25px;
    font-weight: bold;
}

.login-box {
    border-top: 2px solid #eee;
    margin-top: 15px;
    padding-top: 15px;
}

.link {
    text-align: center;
    margin-top: 10px;
}
</style>
</head>

<body>

<div class="container">

<form action="signup.php" method="POST">
<h1>Student Registration</h1>

<div class="form-grid">

<label for="fullname">Full Name</label>
<input id="fullname" type="text" name="fullname" placeholder="Full Name" required>

<label for="roll">Roll Number</label>
<input id="roll" type="text" name="roll" placeholder="Roll Number" required>

<label for="dob">Date of Birth</label>
<input id="dob" type="date" name="dob" required>

<label for="gender">Gender</label>
<select id="gender" name="gender">
<option value="" disabled selected>Select Gender</option>
<option>Male</option>
<option>Female</option>
</select>

<label for="address">Address</label>
<textarea id="address" class="full" name="address" placeholder="Address"></textarea>

<label for="branch">Branch</label>
<select id="branch" name="branch">
<option value="" disabled selected>Select Branch</option>
<option> Bachelor of Computer science and Engineering</option>
<option> Bachelor of Business Administration</option>
<option> Bachelor of Electrical Engineering</option>
<option> Bachelor of electronics and Communication Engineering</option>
<option> Bachelor of Mechanical Engineering</option>
</select>

<label for="year">Year</label>
<select id="year" name="year">
<option value="" disabled selected>Select Year</option>
<option>1st</option>
<option>2nd</option>
<option>3rd</option>
<option>4th</option>
</select>

<label for="section">Section</label>
<input id="section" type="text" name="section" placeholder="Section">

<label for="phone">Phone</label>
<input id="phone" type="tel" name="phone" placeholder="Phone">

<label for="email">Email</label>
<input id="email" type="email" name="email" placeholder="Email" required>

<div class="login-box">
<div class="section-title">Login Details</div>
<br>

<div class="form-grid">
<label for="password">Password</label>
<input id="password" type="password" name="password" placeholder="Create a Password" minlength="8" required>
</div>
</div>

<br>


<button class="full" type="submit">Register</button>

<?php if ($message !== ""): ?>
    <p style="color: #b00020; text-align: center;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<div class="link">
    <a href="login.html">Back to Home</a>
</div>

</form>
</div>

</body>
</html>
