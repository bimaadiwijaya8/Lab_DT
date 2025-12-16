<?php
/**
 * Create User Account Script
 * Simple script to create user accounts with hashed passwords
 * 
 * Usage: Access this file via browser or CLI
 * Database: PostgreSQL with users table
 */

// Include database connection
require_once 'assets/php/db_connect.php';

// Initialize variables
$message = '';
$error = '';
$success_count = 0;

// Get database connection
try {
    $pdo = Database::getConnection();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $no_telp = trim($_POST['no_telp'] ?? '');
        $role = $_POST['role'] ?? '';
        
        // Validation
        if (empty($username) || empty($password) || empty($nama) || empty($email) || empty($role)) {
            throw new Exception("All required fields must be filled");
        }
        
        if (!in_array($role, ['admin', 'editor'])) {
            throw new Exception("Role must be either 'admin' or 'editor'");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Check if username already exists
        $check_sql = "SELECT id FROM users WHERE username = :username";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([':username' => $username]);
        
        if ($check_stmt->rowCount() > 0) {
            throw new Exception("Username '$username' already exists");
        }
        
        // Hash password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $sql = "INSERT INTO users (username, password, nama, email, no_telp, role) 
                VALUES (:username, :password, :nama, :email, :no_telp, :role)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':nama' => $nama,
            ':email' => $email,
            ':no_telp' => $no_telp,
            ':role' => $role
        ]);
        
        $success_count = $stmt->rowCount();
        $message = "Successfully created user account for '$username'!";
        
        // Clear form fields
        $_POST = [];
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle bulk creation from CSV-like data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_data'])) {
    try {
        $bulk_data = trim($_POST['bulk_data']);
        if (empty($bulk_data)) {
            throw new Exception("Bulk data cannot be empty");
        }
        
        $lines = explode("\n", $bulk_data);
        $success_count = 0;
        $failed_users = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Expected format: username,password,nama,email,no_telp,role
            $parts = explode(",", $line);
            if (count($parts) < 5) {
                $failed_users[] = "Invalid format: $line";
                continue;
            }
            
            list($username, $password, $nama, $email, $no_telp, $role) = array_pad($parts, 6, '');
            
            // Basic validation
            if (empty($username) || empty($password) || empty($nama) || empty($email) || empty($role)) {
                $failed_users[] = "Missing required data: $line";
                continue;
            }
            
            if (!in_array($role, ['admin', 'editor'])) {
                $failed_users[] = "Invalid role: $line";
                continue;
            }
            
            // Check if username exists
            $check_sql = "SELECT id FROM users WHERE username = :username";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([':username' => $username]);
            
            if ($check_stmt->rowCount() > 0) {
                $failed_users[] = "Username exists: $username";
                continue;
            }
            
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, nama, email, no_telp, role) 
                    VALUES (:username, :password, :nama, :email, :no_telp, :role)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password,
                ':nama' => $nama,
                ':email' => $email,
                ':no_telp' => $no_telp,
                ':role' => $role
            ]);
            
            $success_count++;
        }
        
        if ($success_count > 0) {
            $message = "Successfully created $success_count user accounts!";
        }
        
        if (!empty($failed_users)) {
            $error = "Failed to create " . count($failed_users) . " users:<br>" . implode("<br>", $failed_users);
        }
        
    } catch (Exception $e) {
        $error = "Bulk creation error: " . $e->getMessage();
    }
}

// Function to generate random password
function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Account</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .container { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .info { background: #e7f3ff; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        textarea { height: 150px; font-family: monospace; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>User Account Creation Tool</h1>
    
    <?php if ($message): ?>
        <div class="success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Single User Creation -->
    <div class="container">
        <h2>Create Single User</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <small>Minimum 6 characters</small>
            </div>
            
            <div class="form-group">
                <label for="nama">Full Name:</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="no_telp">Phone Number:</label>
                <input type="text" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($_POST['no_telp'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="editor" <?php echo ($_POST['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>Editor</option>
                </select>
            </div>
            
            <button type="submit">Create User</button>
        </form>
    </div>
    
    <!-- Bulk User Creation -->
    <div class="container">
        <h2>Bulk User Creation</h2>
        <div class="info">
            <strong>Format:</strong> username,password,nama,email,no_telp,role<br>
            <strong>Example:</strong> john123,pass123,John Doe,john@email.com,08123456789,editor<br>
            <strong>Roles:</strong> admin or editor<br>
            <small>One user per line</small>
        </div>
        
        <form method="post">
            <div class="form-group">
                <label for="bulk_data">Bulk User Data:</label>
                <textarea id="bulk_data" name="bulk_data" placeholder="username,password,nama,email,no_telp,role"><?php echo htmlspecialchars($_POST['bulk_data'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit">Create Bulk Users</button>
        </form>
    </div>
    
    <!-- Password Generator -->
    <div class="container">
        <h2>Password Generator</h2>
        <div class="info">
            <strong>Generated Password:</strong> 
            <code id="generatedPassword"><?php echo generateRandomPassword(); ?></code>
            <button type="button" onclick="generateNewPassword()">Generate New</button>
        </div>
    </div>
    
    <!-- Current Users -->
    <div class="container">
        <h2>Current Users</h2>
        <?php
        try {
            $sql = "SELECT id, username, nama, email, role, created_at FROM users ORDER BY created_at DESC";
            $stmt = $pdo->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($users) > 0) {
                echo "<table border='1' cellpadding='10' style='width: 100%; border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
                
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                echo "<p><strong>Total Users:</strong> " . count($users) . "</p>";
            } else {
                echo "<p>No users found in database.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
    
    <script>
        function generateNewPassword() {
            const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let password = '';
            for (let i = 0; i < 8; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('generatedPassword').textContent = password;
        }
    </script>
</body>
</html>
