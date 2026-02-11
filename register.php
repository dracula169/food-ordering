<?php
    include "connect.php";
    include 'Includes/functions/functions.php';
    include "Includes/templates/header.php";
    include "Includes/templates/navbar.php";

    $error = '';
    $success = '';

    // Process registration form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
        $username = test_input($_POST['username']);
        $email = test_input($_POST['email']);
        $full_name = test_input($_POST['full_name']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // Validation
        if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($password_confirm)) {
            $error = "All fields are required!";
        } elseif (strlen($username) < 3) {
            $error = "Username must be at least 3 characters!";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters!";
        } elseif ($password !== $password_confirm) {
            $error = "Passwords do not match!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            // Check if username or email already exists
            $stmt_check = $con->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt_check->execute(array($username, $email));

            if ($stmt_check->rowCount() > 0) {
                $error = "Username or email already exists!";
            } else {
                // Hash password
                $hashed_password = sha1($password);

                try {
                    $stmt_register = $con->prepare("INSERT INTO users (username, email, full_name, password) VALUES (?, ?, ?, ?)");
                    $stmt_register->execute(array($username, $email, $full_name, $hashed_password));

                    $success = "Registration successful! Redirecting to login page...";
                    header("refresh:2; url=login.php");
                } catch (Exception $e) {
                    $error = "Registration failed: " . $e->getMessage();
                }
            }
        }
    }
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .register-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 500px;
        padding: 40px;
        margin: 20px;
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .register-header h1 {
        color: #333;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .register-header p {
        color: #666;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 14px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input::placeholder {
        color: #999;
    }

    .password-container {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #667eea;
        font-size: 18px;
    }

    .password-container input {
        padding-right: 40px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 500px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }

    .alert {
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .register-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 10px;
    }

    .register-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .register-btn:active {
        transform: translateY(0);
    }

    .register-footer {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .register-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .register-footer a:hover {
        text-decoration: underline;
    }

    .divider {
        margin: 20px 0;
        text-align: center;
        color: #999;
    }

    .input-icon {
        position: relative;
    }

    .input-icon::before {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 16px;
    }

    .input-icon input {
        padding-left: 40px;
    }
</style>

<section class="register-container">
    <div class="register-header">
        <h1>Create Account</h1>
        <p>Join us today to order food and make reservations</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="registerForm">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input 
                type="text" 
                id="full_name" 
                name="full_name" 
                placeholder="Enter your full name"
                onkeyup="this.value=this.value.replace(/[^a-zA-Z\s]/g,'');"
                required
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter username"
                    required
                >
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter email"
                    required
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="At least 6 characters"
                        required
                    >
                    <span class="toggle-password" onclick="togglePassword('password')">👁</span>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Confirm password"
                        required
                    >
                    <span class="toggle-password" onclick="togglePassword('password_confirm')">👁</span>
                </div>
            </div>
        </div>

        <button type="submit" name="register_submit" class="register-btn">Create Account</button>
    </form>

    <div class="register-footer">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</section>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    // Form validation on submit
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const username = document.getElementById('username').value;
        const fullName = document.getElementById('full_name').value;
        const email = document.getElementById('email').value;

        // Client-side validation
        if (fullName.trim().length < 3) {
            alert('Full name must be at least 3 characters!');
            e.preventDefault();
            return;
        }

        if (username.trim().length < 3) {
            alert('Username must be at least 3 characters!');
            e.preventDefault();
            return;
        }

        if (password.length < 6) {
            alert('Password must be at least 6 characters!');
            e.preventDefault();
            return;
        }

        if (password !== passwordConfirm) {
            alert('Passwords do not match!');
            e.preventDefault();
            return;
        }
    });
</script>

<?php include "Includes/templates/footer.php"; ?>
