<?php
session_start();
require_once 'config/database.php';

$page_title = "Future of Digital Treasure Hunting";

// Get featured hunts
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT h.*, u.username as creator_name 
        FROM hunts h 
        JOIN users u ON h.creator_id = u.id 
        WHERE h.privacy = 'public' 
        ORDER BY h.average_rating DESC, h.total_players DESC 
        LIMIT 4
    ");
    $stmt->execute();
    $featured_hunts = $stmt->fetchAll();
    
    // Get leaderboard data
    $stmt = $db->prepare("
        SELECT u.username, u.level, u.total_score, u.hunts_completed, u.avg_time,
               ROW_NUMBER() OVER (ORDER BY u.total_score DESC) as rank
        FROM users u 
        ORDER BY u.total_score DESC 
        LIMIT 8
    ");
    $stmt->execute();
    $leaderboard = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $featured_hunts = [];
    $leaderboard = [];
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg" style="background-image: url('assets/images/cyber-treasure-hero.jpg');"></div>
    
    <div class="hero-content">
        <!-- Floating holographic title -->
        <h1 class="text-gradient-hologram">
            TREASURE<br>
            <span class="text-gradient-cyber">NEXUS</span>
        </h1>
        
        <!-- Subtitle with scan effect -->
        <div class="scan-effect">
            <p>
                Enter the future of puzzle hunting. Create, solve, and compete in
                <span class="neon-blue">immersive digital treasure hunts</span>
            </p>
        </div>
        
<!-- CTA Buttons -->
<div class="hero-buttons">
    <a href="browse_hunts.php" class="cyber-btn hologram" style="font-size: 1.1rem; padding: 16px 32px;">
        ▶ Start Your Hunt
    </a>
    
    <a href="create_hunt.php" class="cyber-btn cyber" style="font-size: 1.1rem; padding: 16px 32px;">
        🏆 Create Puzzles
    </a>
    
    <a href="leaderboard.php" class="cyber-btn" style="font-size: 1rem; padding: 12px 24px;">
        ⚡ Leaderboards
    </a>
</div>

        
        <!-- Stats -->
        <div class="hero-stats">
            <div class="stat-card glass-card">
                <div class="stat-number neon-blue">2,847</div>
                <div class="stat-label">Active Hunters</div>
            </div>
            
            <div class="stat-card glass-card">
                <div class="stat-number neon-purple">15,923</div>
                <div class="stat-label">Puzzles Solved</div>
            </div>
            
            <div class="stat-card glass-card">
                <div class="stat-number neon-green">1,204</div>
                <div class="stat-label">Treasure Hunts</div>
            </div>
        </div>
    </div>
    
    <!-- Floating geometric shapes -->
    <div style="position: absolute; top: 20%; left: 10%; width: 80px; height: 80px; border: 2px solid var(--neon-blue); transform: rotate(45deg); animation: float 6s ease-in-out infinite; opacity: 0.3;"></div>
    <div style="position: absolute; bottom: 20%; right: 10%; width: 64px; height: 64px; border: 2px solid var(--neon-purple); transform: rotate(12deg); animation: float 6s ease-in-out infinite 2s; opacity: 0.3;"></div>
    <div style="position: absolute; top: 33%; right: 20%; width: 48px; height: 48px; border: 2px solid var(--neon-green); animation: float 6s ease-in-out infinite 1s; opacity: 0.3;"></div>
</section>

<!-- Featured Hunts -->
<section class="hunt-grid">
    <div class="section-header">
        <h2 class="section-title text-gradient-cyber">Featured Hunts</h2>
        <p class="section-subtitle">Choose your challenge and dive into immersive puzzle experiences</p>
    </div>
    
    <div class="hunt-cards">
        <?php foreach ($featured_hunts as $hunt): ?>
            <div class="hunt-card glass-card" onclick="window.location.href='hunt.php?id=<?php echo $hunt['id']; ?>'">
                <!-- Hunt Header -->
                <div class="hunt-header">
                    <div>
                        <h3 class="hunt-title"><?php echo htmlspecialchars($hunt['title']); ?></h3>
                        <p class="hunt-creator">
                            by <span class="creator-name"><?php echo htmlspecialchars($hunt['creator_name']); ?></span>
                        </p>
                    </div>
                    
                    <?php if ($hunt['privacy'] === 'private'): ?>
                        <div class="glass-card" style="padding: 8px 12px; display: flex; align-items: center; gap: 4px;">
                            <span style="font-size: 0.8rem; color: var(--neon-purple);">🔒 Private</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <p class="hunt-description"><?php echo htmlspecialchars($hunt['description']); ?></p>
                
                <!-- Hunt Stats -->
                <div class="hunt-stats">
                    <div class="hunt-stat">
                        <span class="hunt-stat-icon">⏱</span>
                        <span><?php echo $hunt['estimated_duration']; ?> min</span>
                    </div>
                    
                    <div class="hunt-stat">
                        <span class="hunt-stat-icon">👥</span>
                        <span><?php echo $hunt['total_players']; ?> players</span>
                    </div>
                    
                    <div class="hunt-stat">
                        <span class="hunt-stat-icon">⭐</span>
                        <span><?php echo number_format($hunt['average_rating'], 1); ?>/5</span>
                    </div>
                    
                    <div class="hunt-stat">
                        <span class="hunt-stat-icon neon-blue">●</span>
                        <span><?php echo $hunt['total_puzzles']; ?> puzzles</span>
                    </div>
                </div>
                
                <!-- Difficulty Badge -->
                <div class="difficulty-badge difficulty-<?php echo strtolower($hunt['difficulty']); ?>">
                    <?php echo strtoupper($hunt['difficulty']); ?>
                </div>
                

            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Load More -->
    <div class="text-center">
        <a href="browse_hunts.php" class="cyber-btn" style="font-size: 1.1rem; padding: 16px 32px;">
            Discover More Hunts
        </a>
    </div>
</section>

<!-- Leaderboard -->
<section class="leaderboard">
    <div class="leaderboard-container">
        <!-- Section Header -->
        <div class="section-header">
            <h2 class="section-title text-gradient-hologram">Global Leaderboard</h2>
            <p class="section-subtitle">The ultimate puzzle masters of the digital realm</p>
        </div>
        
        <!-- Leaderboard Table -->
        <div class="leaderboard-table glass-card">
            <!-- Header -->
            <div class="leaderboard-header">
                <div class="leaderboard-header-row">
                    <div>Rank</div>
                    <div>Hunter</div>
                    <div>Score</div>
                    <div class="hide-mobile">Hunts</div>
                    <div class="hide-mobile">Avg Time</div>
                </div>
            </div>
            
            <!-- Leaderboard Entries -->
            <div class="leaderboard-body">
                <?php foreach ($leaderboard as $entry): ?>
                    <div class="leaderboard-row">
                        <!-- Rank -->
                        <div class="rank-icon <?php echo $entry['rank'] <= 3 ? 'rank-' . $entry['rank'] : ''; ?>">
                            <?php 
                            switch($entry['rank']) {
                                case 1: echo '👑'; break;
                                case 2: echo '🏆'; break;
                                case 3: echo '🥉'; break;
                                default: echo '#' . $entry['rank']; break;
                            }
                            ?>
                        </div>
                        
                        <!-- Player Info -->
                        <div class="player-info">
                            <div class="player-avatar">
                                <?php echo $entry['level']; ?>
                            </div>
                            <div class="player-details">
                                <h4><?php echo htmlspecialchars($entry['username']); ?></h4>
                                <div class="player-level">Level <?php echo $entry['level']; ?></div>
                            </div>
                        </div>
                        
                        <!-- Score -->
                        <div class="score">
                            <?php echo number_format($entry['total_score']); ?>
                        </div>
                        
                        <!-- Hunts Completed -->
                        <div class="hide-mobile">
                            <?php echo $entry['hunts_completed']; ?>
                        </div>
                        
                        <!-- Average Time -->
                        <div class="hide-mobile neon-green">
                            <?php echo $entry['avg_time']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Weekly Challenge -->
        <div class="glass-card" style="margin-top: 48px; padding: 32px; position: relative; overflow: hidden;">
            <div style="position: absolute; inset: 0; background: linear-gradient(45deg, var(--neon-blue), var(--neon-purple), var(--neon-green)); opacity: 0.05;"></div>
            <div style="position: relative; z-index: 10;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h3 style="font-size: 2rem; margin-bottom: 8px;" class="text-gradient-cyber">
                            Weekly Challenge
                        </h3>
                        <p style="color: var(--text-muted);">
                            Compete for exclusive rewards and eternal glory
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;" class="neon-green">
                        <span style="font-size: 1.5rem;">⚡</span>
                        <span style="font-size: 2rem; font-family: 'Orbitron', monospace; font-weight: 900;">2d 14h</span>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                    <div class="text-center">
                        <div style="font-size: 3rem; font-family: 'Orbitron', monospace; font-weight: 900; margin-bottom: 8px;" class="neon-blue">
                            1,247
                        </div>
                        <div style="color: var(--text-muted);">Participants</div>
                    </div>
                    
                    <div class="text-center">
                        <div style="font-size: 3rem; font-family: 'Orbitron', monospace; font-weight: 900; margin-bottom: 8px;" class="neon-purple">
                            50,000
                        </div>
                        <div style="color: var(--text-muted);">Prize Pool</div>
                    </div>
                    
                    <div class="text-center">
                        <div style="font-size: 3rem; font-family: 'Orbitron', monospace; font-weight: 900; margin-bottom: 8px;" class="neon-green">
                            Epic
                        </div>
                        <div style="color: var(--text-muted);">Difficulty</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>