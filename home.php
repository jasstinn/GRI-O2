<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Brewstack Coffee - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* BODY */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    height: 100%;
    overflow: hidden;
}

/* HEADER */
header {
    background: #1a1412b5;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    width: 100%;
    z-index: 10;
}

.nav-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: #c09d74;
    text-decoration: none;
}

.nav-links a, .logout-form button {
    color: #c09d74;
    margin-left: 25px;
    text-decoration: none;
    font-weight: 500;
    border: none;
    background: none;
    cursor: pointer;
    transition: color 0.3s;
}

.nav-links a:hover, .logout-form button:hover {
    color: #fbf5f0ff;
}


/* HERO SECTION */
.hero-section {
    background: url("../image/1stimage.jpg") no-repeat center center/cover;
    height: 100vh;  
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 20px;
}

.hero-content {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(10px);
    padding: 50px 40px;
    border-radius: 25px;
    color: #f8f3e9;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    max-width: 800px;
}

.hero-content h1 {
    font-size: 4rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 1px 1px 8px rgba(0,0,0,0.6);
     color: #c09d74;
}

.hero-content p {
    font-size: 1.3rem;
    margin-bottom: 30px;
    color: #c09d74;
}

.btn-primary {
    background-color: #84450eff;
    color: #f5f2f2f9;
    border: none;
    padding: 15px 35px;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    text-transform: uppercase;
}

.btn-primary:hover {
    background-color: #d49a6a;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
/* FOOTER */
footer {
    text-align: center;
    padding: 10px 0;
    font-size: 12px;
    background: #1a1412b5;
    color: #c09d74;
    position: absolute;
    bottom: 0;
    width: 100%;
}

/* RESPONSIVE */
@media(max-width: 768px){
    .hero-content h1 {
        font-size: 2.2rem;
    }

    .hero-content p {
        font-size: 1rem;
    }

    .nav-links a, .logout-form button {
        margin-left: 10px;
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>

<header>
    <a href="home.php" class="nav-brand">Brewstack Coffee</a>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <?php if (!empty($_SESSION['username'])): ?>
            <form action="logout.php" method="post" class="logout-form" style="display:inline;">
                <button type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</header>

<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome to Brewstack Coffee</h1>
        <p>Debug your day with a fresh brew. Taste the perfect coffee experience.</p>
        <?php if (empty($_SESSION['username'])): ?>
            <a href="login.php" class="btn-primary">Get Started</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; 2025 Brewstack Coffee System. All rights reserved.
</footer>

<?php if (!empty($_SESSION['username'])): ?>
<script>
    (function() {
        if (!window.history || !history.pushState) return;
        const lockHistory = () => history.pushState(null, '', location.href);
        lockHistory();
        window.addEventListener('popstate', lockHistory);
    })();
</script>
<?php endif; ?>

</body>
</html>
