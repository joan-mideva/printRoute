<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - printRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="w-100 m-auto" style="max-width: 400px;">
        <div class="form-container">
            <form action="../backend/login.php" method="POST">
                <div class="text-center mb-4">
                    <img class="mb-4" src="assets/images/logo.png" alt="printRoute Logo" height="57">
                    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
                </div>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php 
                            echo htmlspecialchars($_SESSION['message']); 
                            unset($_SESSION['message']); // Clear the message after displaying
                        ?>
                    </div>
                <?php endif; ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="example@xyz.com" required>
                    <label for="email">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="password" required>
                    <label for="password">Password</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
                <p class="mt-3 text-center">Don't have an account? <a href="register.html">Sign up</a></p>
            </form>
        </div>
    </main>
</body>
</html>