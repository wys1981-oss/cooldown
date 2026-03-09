<?php
require_once __DIR__ . '/includes/functions.php';

// Pagination settings
$perPage = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Get articles and total count
$articles = getLatestArticles($perPage, $offset);
$totalArticles = getTotalArticles();
$totalPages = ceil($totalArticles / $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cooldown - Latest Tech News & Insights">
    <title>Cooldown | Latest News</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-dark">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="/" class="logo">
                    <img src="logo.svg" alt="Cooldown" class="logo-icon">
                    <span>Cooldown</span>
                </a>
                <ul class="nav-links">
                    <li><a href="/" class="active">Home</a></li>
                    <li><a href="categories.php?cat=world">World</a></li>
                    <li><a href="categories.php?cat=technology">Tech</a></li>
                    <li><a href="categories.php?cat=business">Business</a></li>
                    <li><a href="categories.php?cat=politics">Politics</a></li>
                </ul>
                <a href="admin/" class="btn-admin">Admin</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h1>Latest News</h1>
            <div class="header-meta">
                <span><?= $totalArticles ?> articles</span>
                <span>·</span>
                <span>Updated <?= date('H:i') ?></span>
            </div>
        </div>

        <!-- News List -->
        <section class="news-list">
            <div class="article-list">
                <?php foreach ($articles as $article): ?>
                    <article class="article-item">
                        <div class="article-main">
                            <?php if (!empty($article['image_url'])): ?>
                                <a href="article.php?id=<?= $article['id'] ?>" class="article-image-link">
                                    <div class="article-image">
                                        <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($article['title']) ?>"
                                             loading="lazy">
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <div class="article-content">
                                <span class="article-category"><?= htmlspecialchars($article['category'] ?? 'News') ?></span>
                                <h2 class="article-title">
                                    <a href="article.php?id=<?= $article['id'] ?>">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a>
                                </h2>
                                <p class="article-summary">
                                    <?= htmlspecialchars(mb_substr($article['content'] ?? $article['summary'] ?? '', 0, 180)) ?>...
                                </p>
                                <div class="article-meta">
                                    <span class="meta-time"><?= date('M d, Y · H:i', strtotime($article['published_date'])) ?></span>
                                    <span class="meta-source"><?= htmlspecialchars($article['source'] ?? 'Cooldown') ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn-page">← Previous</a>
                    <?php endif; ?>
                    
                    <?php 
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if ($startPage > 1): 
                    ?>
                        <a href="?page=1" class="btn-page">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="?page=<?= $i ?>" class="btn-page <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                        <a href="?page=<?= $totalPages ?>" class="btn-page"><?= $totalPages ?></a>
                    <?php endif; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn-page">Next →</a>
                    <?php endif; ?>
                </nav>
                
                <div class="page-info">
                    Page <?= $page ?> of <?= $totalPages ?> (<?= $totalArticles ?> articles)
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Cooldown. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
