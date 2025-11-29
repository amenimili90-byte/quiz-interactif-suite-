<?php 
require_once __DIR__ . '/../partials/header.php'; 

if (!defined('BASE_URL')) {
    define('BASE_URL', '/quiz_interactif_suite/public/index.php');
}
?>

<section class="card">
    <h2>📊 Mes Statistiques</h2>
    
    <!-- Statistiques globales -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $stats['global']['quizzes_completed'] ?? 0; ?></h3>
            <p>Quiz complétés</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['global']['total_attempts'] ?? 0; ?></h3>
            <p>Tentatives totales</p>
        </div>
        <div class="stat-card">
            <h3><?php echo round($stats['global']['avg_percentage'] ?? 0); ?>%</h3>
            <p>Moyenne générale</p>
        </div>
        <div class="stat-card">
            <h3><?php echo round($stats['global']['best_percentage'] ?? 0); ?>%</h3>
            <p>Meilleur score</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['global']['total_points'] ?? 0; ?></h3>
            <p>Points totaux</p>
        </div>
        <div class="stat-card">
            <h3><?php echo gmdate("H:i:s", $stats['global']['total_time_spent'] ?? 0); ?></h3>
            <p>Temps total</p>
        </div>
    </div>

    <!-- Statistiques par quiz -->
    <h3 style="margin-top: 40px; margin-bottom: 20px;">Détails par quiz</h3>
    
    <?php if (empty($stats['by_quiz'])): ?>
        <div style="padding: 40px; text-align: center;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">
                <i class="fi fi-rr-info"></i> Vous n'avez pas encore complété de quiz
            </p>
            <p style="margin-top: 16px;">
                <a href="<?php echo BASE_URL; ?>?page=quiz" class="btn primary">
                    <span>Commencer un quiz</span>
                </a>
            </p>
        </div>
    <?php else: ?>
        <div class="quiz-stats">
            <?php foreach ($stats['by_quiz'] as $quizStat): ?>
            <div class="quiz-stat-item">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <i class="fi <?php echo htmlspecialchars($quizStat['icon']); ?>" 
                       style="font-size: 2rem; color: var(--accent);"></i>
                    <h4><?php echo htmlspecialchars($quizStat['title']); ?></h4>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Tentatives</div>
                        <div style="font-size: 1.1rem; font-weight: bold;"><?php echo $quizStat['attempts']; ?></div>
                    </div>
                    
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Moyenne</div>
                        <div style="font-size: 1.1rem; font-weight: bold; color: var(--success);">
                            <?php echo round($quizStat['avg_percentage']); ?>%
                        </div>
                    </div>
                    
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Meilleur score</div>
                        <div style="font-size: 1.1rem; font-weight: bold; color: var(--accent);">
                            <?php echo round($quizStat['best_percentage']); ?>%
                        </div>
                    </div>
                    
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Meilleur temps</div>
                        <div style="font-size: 1.1rem; font-weight: bold;">
                            <?php echo gmdate("i:s", $quizStat['best_time']); ?>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <a href="<?php echo BASE_URL; ?>?page=quiz&action=play&quiz=<?php echo urlencode($quizStat['quiz_key']); ?>" 
                       class="btn primary" style="width: 100%; justify-content: center;">
                        Rejouer
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Boutons d'action -->
    <div style="margin-top: 30px; text-align: center;">
        <a href="<?php echo BASE_URL; ?>?page=quiz" class="btn primary">
            <i class="fi fi-rr-play"></i> Jouer à un quiz
        </a>
        <a href="<?php echo BASE_URL; ?>?page=leaderboard" class="btn">
            <i class="fi fi-rr-trophy"></i> Voir le classement
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>