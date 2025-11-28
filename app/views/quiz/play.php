<?php require_once '../app/views/partials/header.php'; ?>

<section id="quiz" class="card">
    <div class="quiz-header">
        <div class="meta">
            <span id="q-index">1</span>/<span id="q-total"><?php echo count($questions); ?></span>
        </div>
        <div class="progress" aria-hidden="true">
            <div id="progress-bar"></div>
        </div>
        <div class="score" aria-hidden="true">
            <i class="fi fi-rr-trophy"></i> <span id="score">0</span>
        </div>
    </div>

    <article id="question-area" class="question-area">
        <h3 id="question-text">Question ?</h3>
        <div id="choices" class="choices" role="list"></div>
    </article>

    <div class="quiz-footer">
        <button id="next-btn" class="btn primary" disabled>
            <span>Suivant <i class="fi fi-rr-arrow-right"></i></span>
        </button>
        <button id="quit-btn" class="btn">
            <span><i class="fi fi-rr-cross-small"></i> Quitter</span>
        </button>
    </div>

    <div id="feedback" class="feedback" aria-live="assertive"></div>
</section>

<script>
// Données du quiz
const quizData = {
    key: '<?php echo $quiz['quiz_key']; ?>',
    questions: <?php echo json_encode($questions); ?>
};

// Initialiser le quiz
document.addEventListener('DOMContentLoaded', function() {
    initializeQuiz(quizData);
});

function initializeQuiz(data) {
    // Votre logique JavaScript existante pour gérer le quiz
    console.log('Quiz initialisé:', data);
    
    // Ici, vous intégrerez votre code JavaScript existant
    // pour gérer l'affichage des questions, le chronomètre, etc.
}
</script>

<?php require_once '../app/views/partials/footer.php'; ?>