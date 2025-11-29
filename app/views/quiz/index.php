<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <section class="card">
        <h2 class="center">Choisissez un Quiz</h2>
        
        <?php if (empty($quizzes)): ?>
            <div class="center" style="padding: 40px;">
                <p>Aucun quiz disponible pour le moment.</p>
                <a href="<?php echo BASE_URL; ?>" class="btn primary">
                    <i class="fi fi-rr-home"></i> Retour à l'accueil
                </a>
            </div>
        <?php else: ?>
            <div class="quizzes-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <?php
                    // Nettoyer et normaliser la clé du quiz
                    $cleanQuizKey = trim(strtolower($quiz['quiz_key']));
                    $quizUrl = BASE_URL . '?page=quiz&action=play&quiz=' . urlencode($cleanQuizKey);
                    ?>
                    <div class="quiz-card">
                        <div class="quiz-icon">
                            <?php if (!empty($quiz['icon'])): ?>
                                <i class="<?php echo htmlspecialchars($quiz['icon']); ?>"></i>
                            <?php else: ?>
                                <i class="fi fi-rr-help"></i>
                            <?php endif; ?>
                        </div>
                        
                        <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                        <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                        
                        <a href="<?php echo $quizUrl; ?>" 
                           class="btn primary"
                           onclick="console.log('Cliqué sur:', '<?php echo $cleanQuizKey; ?>'); return true;">
                            Commencer <i class="fi fi-rr-arrow-right"></i>
                        </a>
                        
                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>