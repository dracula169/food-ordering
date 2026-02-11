<?php
    session_start();
    
    include "connect.php";
    include 'Includes/templates/header.php';
    include "Includes/templates/navbar.php";

    $next = isset($_GET['next']) ? htmlspecialchars($_GET['next']) : 'index.php';
    $session_expired = isset($_GET['session_expired']) ? true : false;
    $login_error = '';
    $login_success = '';

    // Process login form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($username) || empty($password)) {
            $login_error = "Username and password are required!";
        } else {
            try {
                $stmt = $con->prepare("SELECT user_id, username, password, full_name FROM users WHERE username = ?");
                $stmt->execute(array($username));
                $user = $stmt->fetch();

                if ($user && sha1($password) === $user['password']) {
                    // Login successful
                    $_SESSION['user_id_restaurant'] = $user['user_id'];
                    $_SESSION['username_restaurant'] = $user['username'];
                    $_SESSION['full_name_restaurant'] = $user['full_name'];
                    $_SESSION['last_activity'] = time();

                    $login_success = "Login successful! Redirecting...";
                    
                    // Redirect to next page or index
                    header("refresh:1;url=" . $next);
                } else {
                    $login_error = "Invalid username or password!";
                }
            } catch (Exception $e) {
                $login_error = "Login failed: " . $e->getMessage();
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

    .login-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 450px;
        padding: 40px;
        margin: 20px;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h1 {
        color: #333;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .login-header p {
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

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .login-btn {
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

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .login-btn:active {
        transform: translateY(0);
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .login-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .remember-me {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .remember-me input {
        width: auto;
        margin-right: 8px;
    }

    .remember-me label {
        margin: 0;
        font-size: 13px;
    }
</style>

<section class="login-container">
    <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Login to your account to order food and make reservations</p>
    </div>

    <?php if ($session_expired): ?>
        <div class="alert alert-warning">
            <strong>Session Expired!</strong> Your session has expired. Please login again.
        </div>
    <?php endif; ?>

    <?php if (!empty($login_error)): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> <?php echo $login_error; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($login_success)): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?php echo $login_success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" id="loginForm">
        <input type="hidden" name="next" value="<?php echo $next; ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                placeholder="Enter your username"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-container">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    required
                >
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>
        </div>

        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember">Remember me</label>
        </div>

        <button type="submit" name="login_submit" class="login-btn">Login</button>
    </form>

    <div class="login-footer">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</section>

<script>
    function togglePassword() {
        const field = document.getElementById('password');
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    // Form validation on submit
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (!username) {
            alert('Username is required!');
            e.preventDefault();
            return;
        }

        if (!password) {
            alert('Password is required!');
            e.preventDefault();
            return;
        }
    });
</script>

<?php include "Includes/templates/footer.php"; ?>
