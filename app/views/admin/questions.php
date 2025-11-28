<?php require_once '../app/views/partials/header.php'; ?>

<div class="admin-header">
    <h1>Gestion des Questions</h1>
    
    <!-- Sélecteur de quiz -->
    <div class="form-group">
        <label>Sélectionner un quiz:</label>
        <select onchange="location = this.value;">
            <option value="">Choisir un quiz...</option>
            <?php foreach ($quizzes as $quiz): ?>
            <option value="/QuizInteractif/public/admin/questions?quiz_id=<?php echo $quiz['id']; ?>"
                    <?php echo ($_GET['quiz_id'] ?? '') == $quiz['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($quiz['title']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (isset($_GET['quiz_id'])): ?>
    <button class="btn primary" onclick="toggleQuestionForm()">
        <i class="fi fi-rr-plus"></i> Nouvelle Question
    </button>
    <?php endif; ?>
</div>

<?php if (isset($_GET['quiz_id'])): ?>
<!-- Formulaire de création de question -->
<div id="question-form" class="card hidden">
    <h3>Créer une nouvelle question</h3>
    <form method="POST" action="/QuizInteractif/public/admin/question/create">
        <input type="hidden" name="quiz_id" value="<?php echo $_GET['quiz_id']; ?>">
        
        <div class="form-group">
            <label>Question:</label>
            <textarea name="question_text" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Type:</label>
                <select name="question_type" required>
                    <option value="qcm">QCM</option>
                    <option value="truefalse">Vrai/Faux</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Difficulté:</label>
                <select name="difficulty" required>
                    <option value="facile">Facile</option>
                    <option value="moyen">Moyen</option>
                    <option value="difficile">Difficile</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Temps limite (sec):</label>
                <input type="number" name="time_limit" value="30" required>
            </div>
            
            <div class="form-group">
                <label>Points:</label>
                <input type="number" name="points" value="10" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Explication:</label>
            <textarea name="explanation"></textarea>
        </div>
        
        <!-- Choix de réponses -->
        <div id="choices-container">
            <h4>Choix de réponses</h4>
            <div class="choice-item">
                <input type="text" name="choices[0][text]" placeholder="Texte du choix" required>
                <label>
                    <input type="radio" name="correct_choice" value="0" required> Correct
                </label>
            </div>
            <div class="choice-item">
                <input type="text" name="choices[1][text]" placeholder="Texte du choix" required>
                <label>
                    <input type="radio" name="correct_choice" value="1"> Correct
                </label>
            </div>
        </div>
        
        <button type="button" class="btn" onclick="addChoice()">Ajouter un choix</button>
        <button type="submit" class="btn primary">Créer la question</button>
    </form>
</div>

<!-- Liste des questions -->
<div class="card">
    <h3>Questions du quiz</h3>
    <?php if (empty($questions)): ?>
        <p>Aucune question pour ce quiz.</p>
    <?php else: ?>
        <div class="questions-list">
            <?php foreach ($questions as $question): ?>
            <div class="question-item">
                <h4><?php echo htmlspecialchars($question['question_text']); ?></h4>
                <p>
                    <strong>Type:</strong> <?php echo $question['question_type']; ?> |
                    <strong>Difficulté:</strong> <?php echo $question['difficulty']; ?> |
                    <strong>Points:</strong> <?php echo $question['points']; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
let choiceCount = 2;

function toggleQuestionForm() {
    const form = document.getElementById('question-form');
    form.classList.toggle('hidden');
}

function addChoice() {
    const container = document.getElementById('choices-container');
    const newChoice = document.createElement('div');
    newChoice.className = 'choice-item';
    newChoice.innerHTML = `
        <input type="text" name="choices[${choiceCount}][text]" placeholder="Texte du choix" required>
        <label>
            <input type="radio" name="correct_choice" value="${choiceCount}"> Correct
        </label>
    `;
    container.appendChild(newChoice);
    choiceCount++;
}
</script>
<?php endif; ?>

<?php require_once '../app/views/partials/footer.php'; ?>