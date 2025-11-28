<?php require_once '../app/views/partials/header.php'; ?>

<section id="result" class="card center">
    <h2>Résultat Final</h2>
    <p>Score : <strong id="final-score" style="color: var(--accent);">0</strong>/<span id="final-total">0</span></p>
    <div id="result-message"></div>
    <div class="controls">
        <button id="retry-btn" class="btn primary">
            <span><i class="fi fi-rr-refresh"></i> Réessayer</span>
        </button>
        <button id="home-btn" class="btn">
            <span><i class="fi fi-rr-home"></i> Accueil</span>
        </button>
        <button id="leaderboard-btn" class="btn">
            <span><i class="fi fi-rr-trophy"></i> Voir le classement</span>
        </button>
    </div>
</section>

<script>
// Logique pour afficher les résultats
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les résultats depuis la session ou les paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    const score = urlParams.get('score') || 0;
    const total = urlParams.get('total') || 0;
    
    document.getElementById('final-score').textContent = score;
    document.getElementById('final-total').textContent = total;
    
    // Logique pour le message de résultat
    const percentage = (score / (total * 10)) * 100;
    let message, icon, color;
    
    if (percentage >= 90) {
        message = "Exceptionnel ! 🎉";
        color = "var(--success)";
    } else if (percentage >= 75) {
        message = "Très bien ! 👏";
        color = "var(--success)";
    } else if (percentage >= 60) {
        message = "Bon travail ! 👍";
        color = "var(--accent)";
    } else {
        message = "Continue tes efforts ! 📚";
        color = "var(--danger)";
    }
    
    document.getElementById('result-message').innerHTML = `
        <p style="color:${color}; font-size: 1.5rem;">${message}</p>
        <p>Pourcentage: ${Math.round(percentage)}%</p>
    `;
    
    // Boutons
    document.getElementById('retry-btn').addEventListener('click', function() {
        window.location.href = '/QuizInteractif/public/quiz/play?quiz=<?php echo $quiz['quiz_key']; ?>';
    });
    
    document.getElementById('home-btn').addEventListener('click', function() {
        window.location.href = '/QuizInteractif/public/quiz';
    });
    
    document.getElementById('leaderboard-btn').addEventListener('click', function() {
        window.location.href = '/QuizInteractif/public/leaderboard?quiz=<?php echo $quiz['quiz_key']; ?>';
    });
});
</script>

<?php require_once '../app/views/partials/footer.php'; ?>