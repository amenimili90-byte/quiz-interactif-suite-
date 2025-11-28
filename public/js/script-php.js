// Configuration de l'API
const API_BASE_URL = 'api/';

// STATE
let currentQuiz = null;
let currentQuizKey = null;
let currentIndex = 0;
let score = 0;
let answered = false;
let timer = null;
let totalTimeSpent = 0;
let currentUser = null;
let userAnswers = []; // Pour sauvegarder les réponses détaillées

// Classe pour gérer le chronomètre
class Timer {
  constructor(duration, onTick, onComplete) {
    this.duration = duration;
    this.remaining = duration;
    this.onTick = onTick;
    this.onComplete = onComplete;
    this.interval = null;
    this.isRunning = false;
  }

  start() {
    this.isRunning = true;
    this.interval = setInterval(() => {
      this.remaining--;
      this.onTick(this.remaining);
      
      if (this.remaining <= 0) {
        this.stop();
        this.onComplete();
      }
    }, 1000);
  }

  stop() {
    this.isRunning = false;
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }

  reset() {
    this.stop();
    this.remaining = this.duration;
  }

  getRemainingTime() {
    return this.remaining;
  }
}

// DOM Elements
const startBtn = document.getElementById('start-btn');
const viewInst = document.getElementById('view-instructions');
const welcome = document.getElementById('welcome');
const quiz = document.getElementById('quiz');
const result = document.getElementById('result');
const modal = document.getElementById('modal');
const closeModal = document.getElementById('close-modal');
const qIndexEl = document.getElementById('q-index');
const qTotalEl = document.getElementById('q-total');
const questionText = document.getElementById('question-text');
const choicesEl = document.getElementById('choices');
const nextBtn = document.getElementById('next-btn');
const quitBtn = document.getElementById('quit-btn');
const feedbackEl = document.getElementById('feedback');
const progressBar = document.getElementById('progress-bar');
const scoreEl = document.getElementById('score');
const finalScore = document.getElementById('final-score');
const finalTotal = document.getElementById('final-total');
const resultMessage = document.getElementById('result-message');
const retryBtn = document.getElementById('retry-btn');
const homeBtn = document.getElementById('home-btn');

// Créer les éléments nécessaires
const timerEl = document.createElement('div');
timerEl.className = 'timer';
timerEl.innerHTML = '<i class="fi fi-rr-clock"></i> <span id="time-remaining">0</span>s';

const authSection = document.createElement('section');
authSection.id = 'auth-section';
authSection.className = 'card hidden';
authSection.innerHTML = `
  <h2>Connexion / Inscription</h2>
  <div class="auth-tabs">
    <button class="tab-btn active" data-tab="login">Connexion</button>
    <button class="tab-btn" data-tab="register">Inscription</button>
  </div>
  
  <div id="login-form" class="auth-form">
    <input type="text" id="login-identifier" placeholder="Nom d'utilisateur ou email" required>
    <input type="password" id="login-password" placeholder="Mot de passe" required>
    <button class="btn primary" id="login-btn">
      <span><i class="fi fi-rr-sign-in-alt"></i> Se connecter</span>
    </button>
  </div>
  
  <div id="register-form" class="auth-form hidden">
    <input type="text" id="register-username" placeholder="Nom d'utilisateur" required>
    <input type="email" id="register-email" placeholder="Email" required>
    <input type="password" id="register-password" placeholder="Mot de passe (min 6 caractères)" required>
    <button class="btn primary" id="register-btn">
      <span><i class="fi fi-rr-user-add"></i> S'inscrire</span>
    </button>
  </div>
  
  <div id="auth-message" class="auth-message"></div>
`;

const quizSelection = document.createElement('section');
quizSelection.id = 'quiz-selection';
quizSelection.className = 'card hidden';
quizSelection.innerHTML = `
  <div class="user-info" id="user-info">
    <span>Bienvenue, <strong id="username-display"></strong></span>
    <button class="btn" id="logout-btn">
      <span><i class="fi fi-rr-sign-out-alt"></i> Déconnexion</span>
    </button>
  </div>
  <h2 style="text-align: center; margin-bottom: 32px;">Choisissez un Quiz</h2>
  <div class="quiz-grid" id="quiz-grid"></div>
  <button class="btn" id="view-stats-btn" style="margin-top: 24px;">
    <span><i class="fi fi-rr-chart-line"></i> Voir mes statistiques</span>
  </button>
`;

const leaderboardSection = document.createElement('section');
leaderboardSection.id = 'leaderboard';
leaderboardSection.className = 'card hidden';
leaderboardSection.innerHTML = `
  <h2>🏆 Classement</h2>
  <div class="leaderboard-tabs">
    <button class="tab-btn active" data-quiz="">Classement Global</button>
    <button class="tab-btn" data-quiz="informatique">Informatique</button>
    <button class="tab-btn" data-quiz="scientifique">Scientifique</button>
    <button class="tab-btn" data-quiz="histoire">Histoire</button>
    <button class="tab-btn" data-quiz="culture">Culture</button>
  </div>
  <div id="leaderboard-content"></div>
  <button class="btn primary" id="close-leaderboard">Retour</button>
`;

const statsSection = document.createElement('section');
statsSection.id = 'stats-section';
statsSection.className = 'card hidden';
statsSection.innerHTML = `
  <h2>📊 Mes Statistiques</h2>
  <div id="stats-content"></div>
  <button class="btn primary" id="close-stats">Retour</button>
`;

// Ajouter les éléments au DOM
document.querySelector('.quiz-header .meta').after(timerEl);
document.querySelector('.container').appendChild(authSection);
document.querySelector('.container').appendChild(quizSelection);
document.querySelector('.container').appendChild(leaderboardSection);
document.querySelector('.container').appendChild(statsSection);

// ===== FONCTIONS API =====

async function apiCall(endpoint, options = {}) {
  try {
    const response = await fetch(API_BASE_URL + endpoint, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...options.headers
      }
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.error || 'Erreur réseau');
    }
    
    return data;
  } catch (error) {
    console.error('Erreur API:', error);
    showMessage(error.message || 'Erreur de connexion au serveur', 'error');
    throw error;
  }
}

async function checkAuth() {
  try {
    const data = await apiCall('auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'check' })
    });
    
    if (data.authenticated) {
      currentUser = data.user;
      updateUIForLoggedUser();
      return true;
    }
    return false;
  } catch (error) {
    return false;
  }
}

async function login(identifier, password) {
  const data = await apiCall('auth.php', {
    method: 'POST',
    body: JSON.stringify({
      action: 'login',
      identifier,
      password
    })
  });
  
  if (data.success) {
    currentUser = data.user;
    showMessage(data.message, 'success');
    updateUIForLoggedUser();
    hide(authSection);
    show(quizSelection);
    await loadQuizzes();
  }
}

async function register(username, email, password) {
  const data = await apiCall('auth.php', {
    method: 'POST',
    body: JSON.stringify({
      action: 'register',
      username,
      email,
      password
    })
  });
  
  if (data.success) {
    currentUser = data.user;
    showMessage(data.message, 'success');
    updateUIForLoggedUser();
    hide(authSection);
    show(quizSelection);
    await loadQuizzes();
  }
}

async function logout() {
  await apiCall('auth.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'logout' })
  });
  
  currentUser = null;
  hide(quizSelection);
  hide(statsSection);
  hide(leaderboardSection);
  show(welcome);
}

async function loadQuizzes() {
  const quizGrid = document.getElementById('quiz-grid');
  quizGrid.innerHTML = '<p style="text-align: center;">Chargement des quiz...</p>';
  
  try {
    // Charger les quiz disponibles depuis l'API
    const quizKeys = ['informatique', 'scientifique', 'histoire', 'culture'];
    const quizGrid = document.getElementById('quiz-grid');
    quizGrid.innerHTML = '';
    
    for (const quizKey of quizKeys) {
      const data = await apiCall(`get-questions.php?quiz=${quizKey}`);
      if (data.success) {
        const quiz = data.quiz;
        const quizCard = document.createElement('div');
        quizCard.className = 'quiz-card';
        quizCard.innerHTML = `
          <div class="quiz-card-icon">
            <i class="fi ${quiz.icon}"></i>
          </div>
          <h3>${quiz.title}</h3>
          <p>${quiz.description}</p>
          <div class="quiz-meta">
            <span><i class="fi fi-rr-list"></i> ${quiz.questions.length} questions</span>
          </div>
          <button class="btn primary select-quiz" data-quiz="${quiz.key}">
            <span>Commencer <i class="fi fi-rr-arrow-right"></i></span>
          </button>
        `;
        quizGrid.appendChild(quizCard);
      }
    }
  } catch (error) {
    quizGrid.innerHTML = '<p style="text-align: center; color: var(--danger);">Erreur lors du chargement des quiz</p>';
  }
}

async function loadQuizQuestions(quizKey) {
  try {
    const data = await apiCall(`get-questions.php?quiz=${quizKey}`);
    if (data.success) {
      return data.quiz;
    }
  } catch (error) {
    showMessage('Erreur lors du chargement du quiz', 'error');
    return null;
  }
}

async function verifyAnswer(questionId, choiceId, timeSpent) {
  try {
    const data = await apiCall('verify-answer.php', {
      method: 'POST',
      body: JSON.stringify({
        question_id: questionId,
        choice_id: choiceId,
        time_spent: timeSpent
      })
    });
    return data;
  } catch (error) {
    return null;
  }
}

async function saveScore() {
  try {
    const data = await apiCall('save-score.php', {
      method: 'POST',
      body: JSON.stringify({
        quiz_key: currentQuizKey,
        score: score,
        total_questions: currentQuiz.questions.length,
        time_spent: totalTimeSpent,
        answers: userAnswers
      })
    });
    
    if (data.success) {
      return data.data;
    }
  } catch (error) {
    console.error('Erreur sauvegarde score:', error);
  }
  return null;
}

async function loadLeaderboard(quizKey = '') {
  try {
    const endpoint = quizKey ? `get-leaderboard.php?quiz=${quizKey}&limit=10` : 'get-leaderboard.php?limit=10';
    const data = await apiCall(endpoint);
    
    if (data.success) {
      displayLeaderboard(data.leaderboard, data.user_position);
    }
  } catch (error) {
    console.error('Erreur chargement classement:', error);
  }
}

async function loadUserStats() {
  try {
    const data = await apiCall('get-stats.php');
    if (data.success) {
      displayStats(data.stats);
    }
  } catch (error) {
    console.error('Erreur chargement stats:', error);
  }
}

// ===== FONCTIONS UI =====

function show(element) {
  element.classList.remove('hidden');
}

function hide(element) {
  element.classList.add('hidden');
}

function showMessage(message, type = 'info') {
  const authMessage = document.getElementById('auth-message');
  if (authMessage) {
    authMessage.textContent = message;
    authMessage.className = `auth-message ${type}`;
    authMessage.style.display = 'block';
    setTimeout(() => {
      authMessage.style.display = 'none';
    }, 5000);
  }
}

function updateUIForLoggedUser() {
  if (currentUser) {
    document.getElementById('username-display').textContent = currentUser.username;
    document.getElementById('user-info').style.display = 'flex';
  }
}

async function selectQuiz(quizKey) {
  currentQuizKey = quizKey;
  currentQuiz = await loadQuizQuestions(quizKey);
  
  if (currentQuiz) {
    startQuiz();
  }
}

function startQuiz() {
  currentIndex = 0;
  score = 0;
  answered = false;
  totalTimeSpent = 0;
  userAnswers = [];
  scoreEl.textContent = score;
  
  hide(welcome);
  hide(quizSelection);
  hide(result);
  hide(authSection);
  show(quiz);
  
  qTotalEl.textContent = currentQuiz.questions.length;
  finalTotal.textContent = currentQuiz.questions.length;
  
  renderQuestion();
}

function renderQuestion() {
  const q = currentQuiz.questions[currentIndex];
  qIndexEl.textContent = currentIndex + 1;
  questionText.textContent = q.question_text;
  
  const typeIndicator = document.createElement('span');
  typeIndicator.className = 'question-type';
  typeIndicator.textContent = q.question_type === 'truefalse' ? 'Vrai/Faux' : 'QCM';
  questionText.appendChild(typeIndicator);
  
  choicesEl.innerHTML = '';
  feedbackEl.innerHTML = '';
  nextBtn.disabled = true;
  answered = false;

  if (timer) timer.stop();
  timer = new Timer(q.time_limit, updateTimerDisplay, onTimeUp);
  timer.start();

  q.choices.forEach((choice, idx) => {
    const btn = document.createElement('button');
    btn.className = 'choice';
    btn.setAttribute('data-choice-id', choice.id);
    btn.innerHTML = `<span class="choice-label">${choice.text}</span>`;
    btn.addEventListener('click', () => onSelectChoice(btn, choice.id));
    choicesEl.appendChild(btn);
  });

  const pct = Math.round((currentIndex / currentQuiz.questions.length) * 100);
  progressBar.style.width = pct + '%';
}

function updateTimerDisplay(remaining) {
  const timeRemainingEl = document.getElementById('time-remaining');
  if (timeRemainingEl) {
    timeRemainingEl.textContent = remaining;
    
    if (remaining <= 5) {
      timerEl.style.color = 'var(--danger)';
    } else if (remaining <= 10) {
      timerEl.style.color = 'var(--accent-2)';
    } else {
      timerEl.style.color = 'var(--text)';
    }
  }
}

async function onTimeUp() {
  if (answered) return;
  answered = true;
  
  feedbackEl.innerHTML = `
    <div class="feedback-content">
      <i class="fi fi-rr-clock"></i>
      <div>
        <strong>Temps écoulé !</strong>
        <p>Vous n'avez pas répondu à temps.</p>
      </div>
    </div>
  `;
  feedbackEl.style.background = 'rgba(248, 113, 113, 0.1)';
  feedbackEl.style.color = 'var(--danger)';
  
  nextBtn.disabled = false;
}

async function onSelectChoice(btn, choiceId) {
  if (answered) return;
  answered = true;
  
  const q = currentQuiz.questions[currentIndex];
  const timeSpentOnQuestion = q.time_limit - timer.getRemainingTime();
  totalTimeSpent += timeSpentOnQuestion;
  timer.stop();

  // Vérifier la réponse via l'API
  const result = await verifyAnswer(q.id, choiceId, timeSpentOnQuestion);
  
  if (result) {
    const isCorrect = result.is_correct;
    const pointsEarned = result.points_earned;
    
    // Sauvegarder la réponse pour l'enregistrement final
    userAnswers.push({
      question_id: q.id,
      choice_id: choiceId,
      is_correct: isCorrect ? 1 : 0,
      time_spent: timeSpentOnQuestion
    });
    
    if (isCorrect) {
      btn.setAttribute('data-state', 'correct');
      feedbackEl.innerHTML = `
        <div class="feedback-content">
          <i class="fi fi-rr-check-circle"></i>
          <div>
            <strong>Excellente réponse ! +${pointsEarned} points</strong>
            <p>${result.explanation}</p>
          </div>
        </div>
      `;
      feedbackEl.style.background = 'rgba(52, 211, 153, 0.1)';
      feedbackEl.style.color = 'var(--success)';
      score += pointsEarned;
    } else {
      btn.setAttribute('data-state', 'wrong');
      const correctEl = choicesEl.querySelector(`[data-choice-id="${result.correct_choice.id}"]`);
      if (correctEl) correctEl.setAttribute('data-state', 'correct');
      
      feedbackEl.innerHTML = `
        <div class="feedback-content">
          <i class="fi fi-rr-cross-circle"></i>
          <div>
            <strong>Incorrect</strong>
            <p>${result.explanation}</p>
          </div>
        </div>
      `;
      feedbackEl.style.background = 'rgba(248, 113, 113, 0.1)';
      feedbackEl.style.color = 'var(--danger)';
    }
    
    scoreEl.textContent = score;
  }
  
  nextBtn.disabled = false;
  nextBtn.focus();
}

function goNext() {
  currentIndex++;
  if (currentIndex >= currentQuiz.questions.length) {
    finishQuiz();
    return;
  }
  renderQuestion();
}

async function finishQuiz() {
  hide(quiz);
  show(result);
  finalScore.textContent = score;
  
  const totalPossible = currentQuiz.questions.length * 10;
  const pct = Math.round((score / totalPossible) * 100);
  
  // Sauvegarder le score
  const scoreData = await saveScore();
  
  let message, icon, color;
  if (pct >= 90) {
    message = "Exceptionnel ! 🎉";
    icon = "fi-rr-crown";
    color = "var(--success)";
  } else if (pct >= 75) {
    message = "Très bien ! 👏";
    icon = "fi-rr-star";
    color = "var(--success)";
  } else if (pct >= 60) {
    message = "Bon travail ! 👍";
    icon = "fi-rr-thumbs-up";
    color = "var(--accent)";
  } else {
    message = "Continue tes efforts ! 📚";
    icon = "fi-rr-book";
    color = "var(--danger)";
  }
  
  let resultHTML = `
    <div style="text-align: center;">
      <p style="color:${color}; font-size: 1.8rem; margin-bottom: 16px;">
        <i class="fi ${icon}"></i> ${message}
      </p>
      <p style="font-size: 1.2rem; margin-bottom: 8px;">Score: ${pct}% (${score}/${totalPossible} points)</p>
      <p style="color: var(--text-muted);">Temps total: ${Math.floor(totalTimeSpent / 60)}min ${totalTimeSpent % 60}s</p>
  `;
  
  if (scoreData) {
    resultHTML += `
      <p style="color: var(--accent); margin-top: 16px;">
        <i class="fi fi-rr-trophy"></i> Classement: ${scoreData.rank}ème
      </p>
    `;
    if (scoreData.is_personal_best) {
      resultHTML += `<p style="color: var(--success);"><strong>🎊 Nouveau record personnel !</strong></p>`;
    }
  }
  
  resultHTML += `</div>`;
  resultMessage.innerHTML = resultHTML;
  progressBar.style.width = '100%';
}

function displayLeaderboard(leaderboard, userPosition) {
  const content = document.getElementById('leaderboard-content');
  
  if (!leaderboard || leaderboard.length === 0) {
    content.innerHTML = '<p style="text-align: center;">Aucune donnée disponible</p>';
    return;
  }
  
  let html = '<ol class="leaderboard-list">';
  
  leaderboard.forEach((entry, index) => {
    const rank = entry.rank || (index + 1);
    const username = entry.username;
    const percentage = entry.percentage || entry.avg_percentage;
    const timeFormatted = formatTime(entry.time_spent || entry.total_time_spent);
    
    html += `
      <li>
        <span class="rank">#${rank}</span>
        <span class="username">${username}</span>
        <span class="percentage">${percentage}%</span>
        <span class="time">${timeFormatted}</span>
      </li>
    `;
  });
  
  html += '</ol>';
  
  if (userPosition) {
    html += `<p style="text-align: center; margin-top: 20px; color: var(--accent);">
      <strong>Votre position: ${userPosition}ème</strong>
    </p>`;
  }
  
  content.innerHTML = html;
}

function displayStats(stats) {
  const content = document.getElementById('stats-content');
  
  const global = stats.global;
  
  let html = `
    <div class="stats-grid">
      <div class="stat-card">
        <h3>${global.quizzes_completed || 0}</h3>
        <p>Quiz complétés</p>
      </div>
      <div class="stat-card">
        <h3>${global.total_attempts || 0}</h3>
        <p>Tentatives totales</p>
      </div>
      <div class="stat-card">
        <h3>${global.avg_percentage || 0}%</h3>
        <p>Moyenne générale</p>
      </div>
      <div class="stat-card">
        <h3>${global.total_points || 0}</h3>
        <p>Points totaux</p>
      </div>
    </div>
    
    <h3 style="margin-top: 32px; margin-bottom: 16px;">Performances par quiz</h3>
    <div class="quiz-stats">
  `;
  
  if (stats.by_quiz && stats.by_quiz.length > 0) {
    stats.by_quiz.forEach(quiz => {
      html += `
        <div class="quiz-stat-item">
          <h4>${quiz.title}</h4>
          <p>Meilleur score: ${quiz.best_percentage}%</p>
          <p>Tentatives: ${quiz.attempts}</p>
        </div>
      `;
    });
  } else {
    html += '<p>Aucune statistique disponible</p>';
  }
  
  html += '</div>';
  content.innerHTML = html;
}

function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}min ${secs}s`;
}

function quitQuiz() {
  if (timer) timer.stop();
  hide(quiz);
  show(quizSelection);
}

function goHome() {
  hide(result);
  show(quizSelection);
}

// ===== EVENT LISTENERS =====

startBtn.addEventListener('click', async () => {
  const isAuth = await checkAuth();
  if (isAuth) {
    hide(welcome);
    show(quizSelection);
    await loadQuizzes();
  } else {
    hide(welcome);
    show(authSection);
  }
});

viewInst.addEventListener('click', () => show(modal));
closeModal.addEventListener('click', () => hide(modal));
nextBtn.addEventListener('click', goNext);
quitBtn.addEventListener('click', quitQuiz);
retryBtn.addEventListener('click', () => {
  currentIndex = 0;
  startQuiz();
});
homeBtn.addEventListener('click', goHome);

// Auth tabs
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('tab-btn')) {
    const tab = e.target.dataset.tab;
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
    
    if (tab === 'login') {
      show(document.getElementById('login-form'));
      hide(document.getElementById('register-form'));
    } else {
      hide(document.getElementById('login-form'));
      show(document.getElementById('register-form'));
    }
  }
  
  if (e.target.closest('.select-quiz')) {
    const quizKey = e.target.closest('.select-quiz').dataset.quiz;
    selectQuiz(quizKey);
  }
  
  if (e.target.closest('[data-quiz]') && e.target.closest('.leaderboard-tabs')) {
    const quizKey = e.target.closest('[data-quiz]').dataset.quiz;
    document.querySelectorAll('.leaderboard-tabs .tab-btn').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
    loadLeaderboard(quizKey);
  }
});

document.getElementById('login-btn')?.addEventListener('click', async () => {
  const identifier = document.getElementById('login-identifier').value;
  const password = document.getElementById('login-password').value;
  await login(identifier, password);
});

document.getElementById('register-btn')?.addEventListener('click', async () => {
  const username = document.getElementById('register-username').value;
  const email = document.getElementById('register-email').value;
  const password = document.getElementById('register-password').value;
  await register(username, email, password);
});

document.getElementById('logout-btn')?.addEventListener('click', logout);

document.getElementById('view-stats-btn')?.addEventListener('click', async () => {
  hide(quizSelection);
  show(statsSection);
  await loadUserStats();
});

document.getElementById('close-stats')?.addEventListener('click', () => {
  hide(statsSection);
  show(quizSelection);
});

document.getElementById('close-leaderboard')?.addEventListener('click', () => {
  hide(leaderboardSection);
  show(result);
});

// Initialisation
window.addEventListener('load', async () => {
  const isAuth = await checkAuth();
  if (isAuth) {
    hide(welcome);
    show(quizSelection);
    await loadQuizzes();
  }
});