<?php
// Définir BASE_URL s'il n'est pas défini
if (!defined('BASE_URL')) {
    define('BASE_URL', '/quiz_interactif_suite/public/index.php');
}
?>
<nav class="main-nav">
    <div class="nav-container">
        <a href="<?php echo BASE_URL; ?>" class="nav-logo">QuizInteractif</a>
        <div class="nav-links">
            <a href="<?php echo BASE_URL; ?>?page=quiz">Quiz</a>
            <a href="<?php echo BASE_URL; ?>?page=leaderboard">Classement</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>?page=stats">Mes Stats</a>
                <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="<?php echo BASE_URL; ?>?page=logout">Déconnexion</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>?page=login">Connexion</a>
                <a href="<?php echo BASE_URL; ?>?page=register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>