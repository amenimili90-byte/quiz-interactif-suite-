<nav class="main-nav">
    <div class="nav-container">
        <a href="/QuizInteractif/public/" class="nav-logo">QuizInteractif</a>
        <div class="nav-links">
            <a href="/QuizInteractif/public/quiz">Quiz</a>
            <a href="/QuizInteractif/public/leaderboard">Classement</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/QuizInteractif/public/stats">Mes Stats</a>
                <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="/QuizInteractif/public/logout">Déconnexion</a>
            <?php else: ?>
                <a href="/QuizInteractif/public/login">Connexion</a>
                <a href="/QuizInteractif/public/register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>