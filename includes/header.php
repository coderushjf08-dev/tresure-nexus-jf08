<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Treasure Nexus - 2050 Digital Treasure Hunting</title>
    <meta name="description" content="Enter the future of puzzle hunting. Create, solve, and compete in immersive digital treasure hunts.">
    
    <!-- Futuristic Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300d4ff'%3E%3Cpath d='M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z'/%3E%3C/svg%3E">
    
    <!-- Cyber Styles -->
    <link rel="stylesheet" href="assets/css/cyber-style.css">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Treasure Nexus - 2050 Digital Treasure Hunting">
    <meta property="og:description" content="Enter the future of puzzle hunting. Create, solve, and compete in immersive digital treasure hunts.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="assets/images/cyber-treasure-hero.jpg">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Treasure Nexus - 2050 Digital Treasure Hunting">
    <meta name="twitter:description" content="Enter the future of puzzle hunting. Create, solve, and compete in immersive digital treasure hunts.">
    <meta name="twitter:image" content="assets/images/cyber-treasure-hero.jpg">
</head>
<body>
    <!-- Particle Background -->
    <div class="particle-bg"></div>
    
    <!-- Cyber Grid Background -->
    <div class="cyber-grid" style="position: fixed; inset: 0; opacity: 0.3; z-index: -1;"></div>
    
    <!-- Navigation -->
    <nav class="cyber-nav">
        <div class="nav-container">
            <!-- Logo -->
            <a href="index.php" class="logo text-gradient-cyber">
                <div class="logo-icon">⚡</div>
                TREASURE NEXUS
            </a>
            
            <!-- Navigation Links -->
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse_hunts.php">Discover</a></li>
                <li><a href="create_hunt.php">Create</a></li>
                <li><a href="leaderboard.php">Rankings</a></li>
            </ul>
            
            <!-- User Actions -->
            <div class="flex items-center" style="gap: 16px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="cyber-btn">
                        👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <a href="logout.php" class="cyber-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="cyber-btn">Sign In</a>
                    <a href="register.php" class="cyber-btn hologram">Join Hunt</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>