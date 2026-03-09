<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get article ID from URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($article_id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Fetch article details
    $stmt = $db->prepare("SELECT * FROM news_articles WHERE id = ? LIMIT 1");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: index.php');
        exit;
    }

    // Increment view count
    $db->prepare("UPDATE news_articles SET views = views + 1 WHERE id = ?")->execute([$article_id]);

    // Fetch related articles (same category, excluding current)
    $stmt = $db->prepare("
        SELECT id, title, summary, published_date, image_url, category 
        FROM news_articles 
        WHERE category = ? AND id != ? 
        ORDER BY published_date DESC 
        LIMIT 3
    ");
    $stmt->execute([$article['category'], $article_id]);
    $related_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Article fetch error: " . $e->getMessage());
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - Cooldown</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="description" content="<?= htmlspecialchars($article['summary']) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['summary']) ?>">
    <?php if (!empty($article['image_url'])): ?>
        <meta property="og:image" content="<?= htmlspecialchars($article['image_url']) ?>">
    <?php endif; ?>
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
                    <li><a href="categories.php?cat=world">World</a></li>
                    <li><a href="categories.php?cat=technology">Tech</a></li>
                    <li><a href="categories.php?cat=business">Business</a></li>
                    <li><a href="categories.php?cat=politics">Politics</a></li>
                </ul>
                <a href="admin/" class="btn-admin">Admin</a>
            </div>
        </div>
    </nav>

    <main class="article-page">
        <article class="article-container">
            <!-- Article Header -->
            <header class="article-header">
                <span class="article-category"><?= htmlspecialchars($article['category'] ?? 'News') ?></span>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                
                <?php if (!empty($article['summary'])): ?>
                    <p class="article-lead"><?= htmlspecialchars($article['summary']) ?></p>
                <?php endif; ?>
                
                <div class="article-meta">
                    <div class="meta-left">
                        <time datetime="<?= $article['published_date'] ?>">
                            📅 <?= date('M d, Y', strtotime($article['published_date'])) ?>
                        </time>
                        <span class="meta-divider">·</span>
                        <span class="meta-reading-time">⏱️ <?= max(1, ceil(str_word_count($article['content']) / 200)) ?> min read</span>
                    </div>
                    <div class="meta-right">
                        <span class="meta-views">👁️ <?= number_format($article['views']) ?> views</span>
                        <?php if (!empty($article['source'])): ?>
                            <span class="meta-divider">·</span>
                            <span class="meta-source">📍 <?= htmlspecialchars($article['source']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if (!empty($article['image_url'])): ?>
                <figure class="article-image">
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                         alt="<?= htmlspecialchars($article['title']) ?>"
                         loading="lazy">
                    <figcaption class="image-caption">
                        <?= htmlspecialchars($article['title']) ?>
                        <?php if (!empty($article['source'])): ?>
                            <span class="image-credit"> | <?= htmlspecialchars($article['source']) ?></span>
                        <?php endif; ?>
                    </figcaption>
                </figure>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="article-content">
                <?php
                // Process content for better readability
                $content = $article['content'];
                
                // Convert line breaks to paragraphs
                $paragraphs = preg_split('/\n\s*\n/', $content);
                
                foreach ($paragraphs as $paragraph):
                    $paragraph = trim($paragraph);
                    if (empty($paragraph)) continue;
                    
                    // Check if it's a heading
                    if (preg_match('/^#{1,3}\s+(.*)/', $paragraph, $matches)):
                        $level = strlen($matches[0]) - strlen(ltrim($matches[0], '#'));
                        $heading_text = trim($matches[1]);
                        if ($level == 1):
                            echo '<h2>' . htmlspecialchars($heading_text) . '</h2>';
                        elseif ($level == 2):
                            echo '<h3>' . htmlspecialchars($heading_text) . '</h3>';
                        else:
                            echo '<h4>' . htmlspecialchars($heading_text) . '</h4>';
                        endif;
                    else:
                        echo '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                    endif;
                endforeach;
                ?>
            </div>

            <!-- Article Footer -->
            <footer class="article-footer">
                <div class="article-tags">
                    <span class="tags-label">Tags:</span>
                    <span class="tag"><?= htmlspecialchars($article['category']) ?></span>
                    <span class="tag">News</span>
                </div>
                
                <div class="article-actions">
                    <button onclick="shareArticle()" class="btn-share">
                        🔗 Share Article
                    </button>
                    <a href="/" class="btn-back">
                        ← Back to Home
                    </a>
                </div>
            </footer>
        </article>

        <!-- Related Articles -->
        <?php if (!empty($related_articles)): ?>
            <section class="related-section">
                <div class="container">
                    <h2 class="section-title">Related Articles</h2>
                    <div class="related-grid">
                        <?php foreach ($related_articles as $related): ?>
                            <a href="article.php?id=<?= $related['id'] ?>" class="related-card">
                                <?php if (!empty($related['image_url'])): ?>
                                    <div class="related-image">
                                        <img src="<?= htmlspecialchars($related['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($related['title']) ?>"
                                             loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <div class="related-content">
                                    <span class="related-category"><?= htmlspecialchars($related['category']) ?></span>
                                    <h3><?= htmlspecialchars($related['title']) ?></h3>
                                    <div class="related-meta">
                                        <time><?= date('M d, Y', strtotime($related['published_date'])) ?></time>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Cooldown. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: '<?= addslashes($article['title']) ?>',
                    text: 'Check out this article on Cooldown',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    const btn = document.querySelector('.btn-share');
                    const originalText = btn.textContent;
                    btn.textContent = '✓ Link Copied!';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.remove('copied');
                    }, 2000);
                });
            }
        }
    </script>
</body>
</html>
