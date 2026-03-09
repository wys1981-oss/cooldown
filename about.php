<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$themeClass = getCurrentThemeClass();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Cooldown</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo htmlspecialchars($themeClass); ?>">
    <header>
        <div class="container">
            <h1><a href="/">Cooldown</a></h1>
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                    <li><a href="admin/">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="about-content">
            <h2>About Cooldown</h2>
            <p>Cooldown is a news aggregation platform that collects and curates news from various sources across the internet. Our mission is to provide you with the latest and most relevant news in a clean, easy-to-navigate interface.</p>
            
            <h3>How It Works</h3>
            <p>Our automated Python scripts continuously scan trusted news sources and collect articles. These articles are then processed, categorized, and stored in our database for you to browse and read.</p>
            
            <h3>Features</h3>
            <ul>
                <li>Real-time news updates</li>
                <li>Categorized content</li>
                <li>Search functionality</li>
                <li>Responsive design</li>
                <li>Admin panel for content management</li>
            </ul>
            
            <h3>Technology Stack</h3>
            <p>Built with PHP, MySQL, HTML5, CSS3, and JavaScript. The backend uses a custom Python scraper to collect news articles.</p>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Cooldown News. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>