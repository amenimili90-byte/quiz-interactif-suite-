<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="card center">
    <h2>Choisissez un Quiz</h2>
    <div class="quiz-grid">
        <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz-card">
                <div class="quiz-card-icon">
                    <i class="fi <?php echo $quiz['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                <a href="/QuizInteractif/public/quiz/play?quiz=<?php echo $quiz['quiz_key']; ?>" 
                   class="btn primary">
                    Commencer <i class="fi fi-rr-arrow-right"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once '../app/views/partials/footer.php'; ?>