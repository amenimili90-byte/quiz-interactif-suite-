<?php 
require_once __DIR__ . '/../partials/header.php'; 

if (!defined('BASE_URL')) {
    define('BASE_URL', '/quiz_interactif_suite/public/index.php');
}

$quizKey = $_GET['quiz'] ?? '';
?>

<section class="card">
    <h2>🏆 Classement</h2>
    
    <!-- Sélecteur de quiz -->
    <div style="margin: 20px 0;">
        <label for="quiz-select" style="display: block; margin-bottom: 10px; font-weight: bold;">
            Filtrer par quiz :
        </label>
        <select id="quiz-select" style="padding: 10px; border-radius: 8px; border: 2px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); font-size: 1rem; width: 100%; max-width: 400px;">
            <option value="">Tous les quiz (Classement global)</option>
            <?php foreach ($quizzes as $quiz): ?>
            <option value="<?php echo htmlspecialchars($quiz['quiz_key']); ?>" 
                    <?php echo $quizKey === $quiz['quiz_key'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($quiz['title']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Affichage du classement -->
    <?php if (empty($leaderboard)): ?>
        <div style="padding: 40px; text-align: center;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">
                <i class="fi fi-rr-info"></i> Aucun score enregistré pour le moment
            </p>
        </div>
    <?php else: ?>
        <div class="leaderboard-list" style="margin-top: 30px;">
            <?php foreach ($leaderboard as $index => $entry): ?>
            <div class="leaderboard-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; margin-bottom: 10px; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid <?php echo $index < 3 ? 'var(--accent)' : 'var(--border-color)'; ?>;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span class="rank" style="font-size: 1.5rem; font-weight: bold; color: <?php echo $index < 3 ? 'var(--accent)' : 'var(--text-muted)'; ?>; min-width: 40px;">
                        #<?php echo $entry['rank'] ?? ($index + 1); ?>
                    </span>
                    <div>
                        <div style="font-weight: bold; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($entry['username']); ?>
                        </div>
                        <?php if (isset($entry['completed_at'])): ?>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">
                            <?php echo date('d/m/Y', strtotime($entry['completed_at'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="text-align: right;">
                    <?php if (isset($entry['percentage'])): ?>
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--success);">
                        <?php echo round($entry['percentage']); ?>%
                    </div>
                    <?php elseif (isset($entry['avg_percentage'])): ?>
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--success);">
                        <?php echo round($entry['avg_percentage']); ?>%
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($entry['score']) && isset($entry['total_questions'])): ?>
                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                        <?php echo $entry['score']; ?>/<?php echo $entry['total_questions'] * 10; ?> points
                    </div>
                    <?php elseif (isset($entry['total_attempts'])): ?>
                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                        <?php echo $entry['total_attempts']; ?> tentatives
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Position de l'utilisateur connecté -->
    <?php if (isset($_SESSION['user_id']) && $userPosition): ?>
    <div style="margin-top: 30px; padding: 20px; background: var(--bg-hover); border-radius: 8px; border: 2px solid var(--accent);">
        <h3 style="margin-bottom: 10px;">Votre position</h3>
        <p style="font-size: 1.2rem;">
            Vous êtes classé <strong style="color: var(--accent);">#<?php echo $userPosition; ?></strong>
        </p>
    </div>
    <?php endif; ?>

    <!-- Boutons d'action -->
    <div style="margin-top: 30px; text-align: center;">
        <a href="<?php echo BASE_URL; ?>?page=quiz" class="btn primary">
            <i class="fi fi-rr-play"></i> Jouer à un quiz
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo BASE_URL; ?>?page=stats" class="btn">
            <i class="fi fi-rr-chart-line"></i> Mes statistiques
        </a>
        <?php endif; ?>
    </div>
</section>

<script>
document.getElementById('quiz-select').addEventListener('change', function() {
    const quizKey = this.value;
    if (quizKey) {
        window.location.href = '<?php echo BASE_URL; ?>?page=leaderboard&quiz=' + encodeURIComponent(quizKey);
    } else {
        window.location.href = '<?php echo BASE_URL; ?>?page=leaderboard';
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>