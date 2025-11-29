<?php 
// Utiliser des chemins absolus pour les includes
require_once __DIR__ . '/../partials/header.php'; 

// Récupérer les données de la requête
$quizKey = $_GET['quiz'] ?? '';
$score = $_GET['score'] ?? 0;
$total = $_GET['total'] ?? 0;

// Charger les informations du quiz si disponible
$quiz = null;
if ($quizKey) {
    require_once __DIR__ . '/../../models/Quiz.php';
    $quizModel = new Quiz();
    $quiz = $quizModel->findByKey($quizKey);
}
?>

<section id="result" class="card center">
    <h2>Résultat Final</h2>
    
    <div style="margin: 30px 0;">
        <p style="font-size: 2rem; margin: 20px 0;">
            Score : <strong id="final-score" style="color: var(--accent);"><?php echo htmlspecialchars($score); ?></strong>/<span id="final-total"><?php echo htmlspecialchars($total * 10); ?></span>
        </p>
        <div id="result-message"></div>
    </div>
    
    <div class="controls" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <button id="retry-btn" class="btn primary">
            <span><i class="fi fi-rr-refresh"></i> Réessayer</span>
        </button>
        <button id="home-btn" class="btn">
            <span><i class="fi fi-rr-home"></i> Liste des Quiz</span>
        </button>
        <?php if (isset($_SESSION['user_id'])): ?>
        <button id="leaderboard-btn" class="btn">
            <span><i class="fi fi-rr-trophy"></i> Voir le classement</span>
        </button>
        <?php endif; ?>
    </div>
</section>

<script>
// Logique pour afficher les résultats
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les résultats depuis les paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    const score = parseInt(urlParams.get('score')) || 0;
    const total = parseInt(urlParams.get('total')) || 0;
    const quizKey = urlParams.get('quiz') || '';
    
    document.getElementById('final-score').textContent = score;
    document.getElementById('final-total').textContent = total * 10;
    
    // Calculer le pourcentage
    const maxScore = total * 10;
    const percentage = maxScore > 0 ? (score / maxScore) * 100 : 0;
    
    let message, color;
    
    if (percentage >= 90) {
        message = "🎉 Exceptionnel !";
        color = "var(--success)";
    } else if (percentage >= 75) {
        message = "👍 Très bien !";
        color = "var(--success)";
    } else if (percentage >= 60) {
        message = "👏 Bon travail !";
        color = "var(--accent)";
    } else if (percentage >= 40) {
        message = "📚 Pas mal, continuez !";
        color = "var(--warning)";
    } else {
        message = "📚 Continue tes efforts !";
        color = "var(--danger)";
    }
    
    document.getElementById('result-message').innerHTML = `
        <p style="color:${color}; font-size: 1.8rem; font-weight: bold; margin: 20px 0;">${message}</p>
        <p style="font-size: 1.3rem;">Pourcentage: ${Math.round(percentage)}%</p>
        <p style="color: var(--text-muted); margin-top: 10px;">
            Questions correctes: ${Math.round(score / 10)} / ${total}
        </p>
    `;
    
    // Boutons
    document.getElementById('retry-btn').addEventListener('click', function() {
        if (quizKey) {
            window.location.href = '/quiz_interactif_suite/public/quiz/play?quiz=' + encodeURIComponent(quizKey);
        } else {
            window.location.href = '/quiz_interactif_suite/public/quiz';
        }
    });
    
    document.getElementById('home-btn').addEventListener('click', function() {
        window.location.href = '/quiz_interactif_suite/public/quiz';
    });
    
    const leaderboardBtn = document.getElementById('leaderboard-btn');
    if (leaderboardBtn) {
        leaderboardBtn.addEventListener('click', function() {
            if (quizKey) {
                window.location.href = '/quiz_interactif_suite/public/leaderboard?quiz=' + encodeURIComponent(quizKey);
            } else {
                window.location.href = '/quiz_interactif_suite/public/leaderboard';
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>