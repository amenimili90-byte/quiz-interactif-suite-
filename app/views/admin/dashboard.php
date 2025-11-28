<?php require_once '../app/views/partials/header.php'; ?>

<div class="admin-header">
    <h1>Tableau de Bord Administrateur</h1>
    <p>Gestion du système de quiz</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $stats['total_users'] ?? 0; ?></h3>
        <p>Utilisateurs</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_quizzes'] ?? 0; ?></h3>
        <p>Quiz</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_questions'] ?? 0; ?></h3>
        <p>Questions</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_scores'] ?? 0; ?></h3>
        <p>Parties Jouées</p>
    </div>
</div>

<div class="admin-actions">
    <a href="/QuizInteractif/public/admin/quizzes" class="btn primary">
        <i class="fi fi-rr-list"></i> Gérer les Quiz
    </a>
    <a href="/QuizInteractif/public/admin/questions" class="btn primary">
        <i class="fi fi-rr-question"></i> Gérer les Questions
    </a>
</div>

<?php require_once '../app/views/partials/footer.php'; ?>