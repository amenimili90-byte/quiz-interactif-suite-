<?php require_once '../app/views/partials/header.php'; ?>

<div class="admin-header">
    <h1>Gestion des Quiz</h1>
    <button class="btn primary" onclick="toggleQuizForm()">
        <i class="fi fi-rr-plus"></i> Nouveau Quiz
    </button>
</div>

<!-- Formulaire de création (caché par défaut) -->
<div id="quiz-form" class="card hidden">
    <h3>Créer un nouveau quiz</h3>
    <form method="POST" action="/quiz_interactif_suite/public/admin/quiz/create">
        <div class="form-group">
            <label>Clé du quiz:</label>
            <input type="text" name="quiz_key" required placeholder="ex: informatique">
        </div>
        <div class="form-group">
            <label>Titre:</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <div class="form-group">
            <label>Icône (Flaticon):</label>
            <input type="text" name="icon" required placeholder="ex: fi-rr-laptop">
        </div>
        <button type="submit" class="btn primary">Créer le quiz</button>
    </form>
</div>

<!-- Liste des quiz -->
<div class="card">
    <h3>Quiz existants</h3>
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Clé</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?php echo $quiz['id']; ?></td>
                    <td><?php echo htmlspecialchars($quiz['quiz_key']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['description']); ?></td>
                    <td>
                        <span class="status <?php echo $quiz['active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $quiz['active'] ? 'Actif' : 'Inactif'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="/quiz_interactif_suite/public/admin/questions?quiz_id=<?php echo $quiz['id']; ?>" 
                           class="btn small">
                            Questions
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleQuizForm() {
    const form = document.getElementById('quiz-form');
    form.classList.toggle('hidden');
}
</script>

<?php require_once '../app/views/partials/footer.php'; ?>