<?php
// Categories page for Cooldown News Website

require_once 'config/database.php';
require_once 'includes/functions.php';

// Get all categories
$categories = getAllCategories();

// Get articles count for each category
$categoryArticles = [];
foreach ($categories as $category) {
    $articles = getArticlesByCategory($category['slug'], 3);
    $categoryArticles[$category['slug']] = $articles;
}

// Check if viewing a specific category
$selectedCategory = null;
$selectedArticles = [];
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;

if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $selectedCategorySlug = $_GET['cat'];
    foreach ($categories as $category) {
        if ($category['slug'] === $selectedCategorySlug) {
            $selectedCategory = $category;
            break;
        }
    }
    
    if ($selectedCategory) {
        $offset = ($page - 1) * $perPage;
        $selectedArticles = getArticlesByCategory($selectedCategorySlug, $perPage, $offset);
        $totalArticles = getTotalArticlesByCategory($selectedCategorySlug);
        $totalPages = ceil($totalArticles / $perPage);
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if ($selectedCategory): ?>
            <?= htmlspecialchars($selectedCategory['name']) ?> - Cooldown
        <?php else: ?>
            Categories - Cooldown
        <?php endif; ?>
    </title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
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
                    <li><a href="/">Home</a></li>
                    <li><a href="categories.php" class="active">Categories</a></li>
                    <li><a href="categories.php?cat=world">World</a></li>
                    <li><a href="categories.php?cat=technology">Tech</a></li>
                    <li><a href="categories.php?cat=business">Business</a></li>
                    <li><a href="categories.php?cat=politics">Politics</a></li>
                </ul>
                <a href="admin/" class="btn-admin">Admin</a>
            </div>
        </div>
    </nav>

    <main class="categories-page">
        <div class="container">
            <?php if ($selectedCategory): ?>
                <!-- Category-specific view -->
                <section class="category-view">
                    <header class="category-header">
                        <span class="category-badge"><?= htmlspecialchars($selectedCategory['name']) ?></span>
                        <h1><?= htmlspecialchars($selectedCategory['name']) ?> News</h1>
                        <?php if (!empty($selectedCategory['description'])): ?>
                            <p class="category-description"><?= htmlspecialchars($selectedCategory['description']) ?></p>
                        <?php endif; ?>
                        <div class="category-meta">
                            <span>📰 <?= $totalArticles ?> articles</span>
                            <span>·</span>
                            <span>Updated <?= date('H:i') ?></span>
                        </div>
                        <a href="categories.php" class="back-link">← Back to All Categories</a>
                    </header>
                    
                    <?php if (!empty($selectedArticles)): ?>
                        <div class="article-list">
                            <?php foreach ($selectedArticles as $article): ?>
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
                                                <?= htmlspecialchars(mb_substr($article['summary'] ?? $article['content'] ?? '', 0, 180)) ?>...
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
                                    <a href="?cat=<?= urlencode($selectedCategorySlug) ?>&page=<?= $page - 1 ?>" class="btn-page">← Previous</a>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1): 
                                ?>
                                    <a href="?cat=<?= urlencode($selectedCategorySlug) ?>&page=1" class="btn-page">1</a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="page-ellipsis">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a href="?cat=<?= urlencode($selectedCategorySlug) ?>&page=<?= $i ?>" class="btn-page <?= $i === $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <span class="page-ellipsis">...</span>
                                    <?php endif; ?>
                                    <a href="?cat=<?= urlencode($selectedCategorySlug) ?>&page=<?= $totalPages ?>" class="btn-page"><?= $totalPages ?></a>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?cat=<?= urlencode($selectedCategorySlug) ?>&page=<?= $page + 1 ?>" class="btn-page">Next →</a>
                                <?php endif; ?>
                            </nav>
                            
                            <div class="page-info">
                                Page <?= $page ?> of <?= $totalPages ?> (<?= $totalArticles ?> articles)
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No articles available in this category yet.</p>
                            <a href="categories.php" class="btn-primary">Browse Other Categories</a>
                        </div>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <!-- Main categories overview -->
                <section class="categories-overview">
                    <header class="page-header">
                        <h1>Browse by Category</h1>
                        <p>Find news and articles by topic</p>
                    </header>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="categories-grid">
                            <?php foreach ($categories as $category): ?>
                                <?php 
                                $articleCount = count($categoryArticles[$category['slug']] ?? []);
                                $hasArticles = $articleCount > 0;
                                ?>
                                <a href="categories.php?cat=<?= urlencode($category['slug']) ?>" class="category-card">
                                    <div class="category-icon">
                                        <?php
                                        $icons = [
                                            'world' => '🌍',
                                            'politics' => '🏛️',
                                            'business' => '💼',
                                            'technology' => '💻',
                                            'science' => '🔬',
                                            'health' => '🏥',
                                            'general' => '📰'
                                        ];
                                        $icon = $icons[$category['slug']] ?? '📌';
                                        ?>
                                        <?= $icon ?>
                                    </div>
                                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                                    <?php if (!empty($category['description'])): ?>
                                        <p class="category-desc"><?= htmlspecialchars($category['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="category-stats">
                                        <span class="article-count">
                                            <?= $articleCount ?> article<?= $articleCount !== 1 ? 's' : '' ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($hasArticles): ?>
                                        <div class="category-preview">
                                            <h4>Latest:</h4>
                                            <ul>
                                                <?php foreach (array_slice($categoryArticles[$category['slug']], 0, 2) as $article): ?>
                                                    <li>
                                                        <?= htmlspecialchars(mb_substr($article['title'], 0, 50)) ?><?= strlen($article['title']) > 50 ? '...' : '' ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No categories available.</p>
                            <a href="/" class="btn-primary">Go to Home</a>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Cooldown. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
