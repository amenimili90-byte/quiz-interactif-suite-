<?php 
require_once __DIR__ . '/../partials/header.php'; 

if (!defined('BASE_URL')) {
    define('BASE_URL', '/quiz_interactif_suite/public/index.php');
}

// Debug des données reçues
error_log("=== PLAY.PHP DEBUG ===");
error_log("Quiz: " . print_r($quiz, true));
error_log("Nombre de questions: " . count($questions));
if (!empty($questions)) {
    error_log("Première question: " . print_r($questions[0], true));
}
?>

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
        <h3 id="question-text">Chargement...</h3>
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
const BASE_URL = '<?php echo BASE_URL; ?>';

// Données du quiz
const quizData = <?php echo json_encode([
    'key' => $quiz['quiz_key'],
    'title' => $quiz['title'],
    'questions' => $questions
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

let currentQuestionIndex = 0;
let score = 0;
let timer;
let timeLeft = 0;
let answers = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== QUIZ CHARGÉ ===');
    console.log('Quiz:', quizData);
    console.log('Nombre de questions:', quizData.questions.length);
    
    if (!quizData.questions || quizData.questions.length === 0) {
        alert('Aucune question disponible pour ce quiz.');
        window.location.href = BASE_URL + '?page=quiz';
        return;
    }
    
    // Vérifier les choix de chaque question
    quizData.questions.forEach((q, i) => {
        console.log(`Question ${i+1}:`, q.question_text);
        console.log(`  Choix:`, q.choices);
        if (!q.choices || q.choices.length === 0) {
            console.error(`❌ Question ${i+1} sans choix!`);
        }
    });
    
    initializeQuiz();
});

function initializeQuiz() {
    displayQuestion();
    
    document.getElementById('next-btn').addEventListener('click', nextQuestion);
    
    document.getElementById('quit-btn').addEventListener('click', function() {
        if (confirm('Voulez-vous vraiment quitter le quiz ?')) {
            window.location.href = BASE_URL + '?page=quiz';
        }
    });
}

function displayQuestion() {
    const question = quizData.questions[currentQuestionIndex];
    
    console.log('=== AFFICHAGE QUESTION ===');
    console.log('Index:', currentQuestionIndex);
    console.log('Question:', question);
    
    if (!question) {
        console.error('❌ Question non trouvée');
        finishQuiz();
        return;
    }
    
    // Mettre à jour l'index
    document.getElementById('q-index').textContent = currentQuestionIndex + 1;
    document.getElementById('question-text').textContent = question.question_text;
    
    // Mettre à jour la barre de progression
    const progress = ((currentQuestionIndex + 1) / quizData.questions.length) * 100;
    document.getElementById('progress-bar').style.width = progress + '%';
    
    // Afficher les choix
    displayChoices(question);
    
    // Désactiver le bouton suivant
    document.getElementById('next-btn').disabled = true;
    
    // Démarrer le timer
    startTimer(question.time_limit || 30);
}

function displayChoices(question) {
    const choicesContainer = document.getElementById('choices');
    choicesContainer.innerHTML = '';
    
    console.log('=== AFFICHAGE DES CHOIX ===');
    console.log('Question ID:', question.id);
    console.log('Choix disponibles:', question.choices);
    
    if (!question.choices || !Array.isArray(question.choices) || question.choices.length === 0) {
        console.error('❌ Aucun choix disponible!');
        choicesContainer.innerHTML = '<p style="color:#e94560; padding:20px;">❌ Aucun choix disponible pour cette question</p>';
        return;
    }
    
    question.choices.forEach((choice, index) => {
        console.log(`Choix ${index + 1}:`, choice);
        
        const button = document.createElement('button');
        button.className = 'choice-btn';
        button.textContent = choice.choice_text || choice.text || 'Choix sans texte';
        button.dataset.choiceId = choice.id;
        button.dataset.isCorrect = choice.is_correct;
        
        button.addEventListener('click', function() {
            selectChoice(this, question);
        });
        
        choicesContainer.appendChild(button);
    });
    
    console.log('✅ Choix affichés:', choicesContainer.children.length);
}

function selectChoice(selectedBtn, question) {
    const allChoices = document.querySelectorAll('.choice-btn');
    allChoices.forEach(btn => {
        btn.disabled = true;
        btn.classList.remove('selected');
    });
    
    selectedBtn.classList.add('selected');
    clearInterval(timer);
    
    const isCorrect = selectedBtn.dataset.isCorrect === '1';
    const questionPoints = parseInt(question.points) || 10;
    
    if (isCorrect) {
        selectedBtn.classList.add('correct');
        score += questionPoints;
        showFeedback('✅ Correct ! +' + questionPoints + ' points', 'success');
    } else {
        selectedBtn.classList.add('wrong');
        allChoices.forEach(btn => {
            if (btn.dataset.isCorrect === '1') {
                btn.classList.add('correct');
            }
        });
        showFeedback('❌ Incorrect. La bonne réponse est affichée en vert.', 'error');
    }
    
    answers.push({
        question_id: question.id,
        choice_id: selectedBtn.dataset.choiceId,
        is_correct: isCorrect ? 1 : 0,
        time_spent: (question.time_limit || 30) - timeLeft
    });
    
    document.getElementById('score').textContent = score;
    document.getElementById('next-btn').disabled = false;
}

function startTimer(duration) {
    timeLeft = duration;
    
    timer = setInterval(() => {
        timeLeft--;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            timeExpired();
        }
    }, 1000);
}

function timeExpired() {
    showFeedback('⏱️ Temps écoulé !', 'error');
    
    const allChoices = document.querySelectorAll('.choice-btn');
    allChoices.forEach(btn => {
        btn.disabled = true;
        if (btn.dataset.isCorrect === '1') {
            btn.classList.add('correct');
        }
    });
    
    const question = quizData.questions[currentQuestionIndex];
    answers.push({
        question_id: question.id,
        choice_id: null,
        is_correct: 0,
        time_spent: question.time_limit || 30
    });
    
    document.getElementById('next-btn').disabled = false;
}

function nextQuestion() {
    clearInterval(timer);
    currentQuestionIndex++;
    
    if (currentQuestionIndex < quizData.questions.length) {
        displayQuestion();
    } else {
        finishQuiz();
    }
}

function showFeedback(message, type) {
    const feedback = document.getElementById('feedback');
    feedback.textContent = message;
    feedback.className = 'feedback ' + type;
    feedback.style.display = 'block';
    
    setTimeout(() => {
        feedback.style.display = 'none';
    }, 3000);
}

function finishQuiz() {
    const totalTime = answers.reduce((sum, ans) => sum + ans.time_spent, 0);
    
    const params = new URLSearchParams({
        score: score,
        total: quizData.questions.length,
        quiz: quizData.key
    });
    
    window.location.href = BASE_URL + '?page=quiz&action=result&' + params.toString();
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>