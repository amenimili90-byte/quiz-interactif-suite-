// Base de données des quiz par thèmes
const QUIZ_DATABASE = {
  informatique: {
    title: "💻 Quiz Informatique",
    description: "Testez vos connaissances en informatique et technologies",
    icon: "fi-rr-laptop",
    questions: [
      {
        id: 1,
        q: "Que signifie l'acronyme CPU ?",
        choices: ["Central Processing Unit", "Computer Personal Unit", "Central Program Utility", "Core Processing Unit"],
        answer: 0,
        type: "qcm",
        timeLimit: 30,
        explanation: "CPU signifie Central Processing Unit, le processeur principal d'un ordinateur."
      },
      {
        id: 2,
        q: "Linux est un système d'exploitation open source.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Linux est effectivement un système d'exploitation open source très populaire."
      },
      {
        id: 3,
        q: "Quelle est l'unité de base de stockage en informatique ?",
        choices: ["Bit", "Byte", "Megabyte", "Kilobyte"],
        answer: 0,
        type: "qcm",
        timeLimit: 25,
        explanation: "Le bit (binary digit) est l'unité fondamentale en informatique."
      },
      {
        id: 4,
        q: "Le protocole HTTPS est sécurisé.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "HTTPS utilise le chiffrement SSL/TLS pour sécuriser les communications."
      },
      {
        id: 5,
        q: "Qu'est-ce que la RAM ?",
        choices: ["Mémoire de stockage permanente", "Mémoire vive temporaire", "Un processeur", "Un système d'exploitation"],
        answer: 1,
        type: "qcm",
        timeLimit: 30,
        explanation: "RAM = Random Access Memory, mémoire volatile pour les données en cours d'utilisation."
      },
      {
        id: 6,
        q: "JavaScript est le langage principal utilisé pour l'IA.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Python est le langage principal pour l'IA, JavaScript est pour le web."
      },
      {
        id: 7,
        q: "Que signifie DNS ?",
        choices: ["Data Network System", "Domain Name System", "Digital Network Service", "Domain Network Security"],
        answer: 1,
        type: "qcm",
        timeLimit: 25,
        explanation: "DNS convertit les noms de domaine en adresses IP."
      },
      {
        id: 8,
        q: "Un octet contient toujours 8 bits.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Un octet est par définition composé de 8 bits."
      },
      {
        id: 9,
        q: "Quel type de cyberattaque utilise des emails frauduleux ?",
        choices: ["Malware", "DDoS", "Phishing", "Ransomware"],
        answer: 2,
        type: "qcm",
        timeLimit: 30,
        explanation: "Le phishing utilise des emails frauduleux pour voler des informations."
      },
      {
        id: 10,
        q: "Le Cloud Computing nécessite un disque dur local.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Le cloud computing utilise des serveurs distants, pas de stockage local obligatoire."
      },
      {
        id: 11,
        q: "Quel langage est utilisé pour styliser les pages web ?",
        choices: ["HTML", "CSS", "JavaScript", "Python"],
        answer: 1,
        type: "qcm",
        timeLimit: 25,
        explanation: "CSS (Cascading Style Sheets) est utilisé pour le style des pages web."
      },
      {
        id: 12,
        q: "HTML est un langage de programmation.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 15,
        explanation: "HTML est un langage de balisage, pas un langage de programmation."
      },
      {
        id: 13,
        q: "Qu'est-ce qu'un firewall ?",
        choices: ["Un antivirus", "Un système de sécurité réseau", "Un type de virus", "Un programme de bureautique"],
        answer: 1,
        type: "qcm",
        timeLimit: 30,
        explanation: "Un firewall contrôle le trafic réseau entrant et sortant."
      },
      {
        id: 14,
        q: "Python est un langage compilé.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Python est un langage interprété, pas compilé."
      },
      {
        id: 15,
        q: "Quelle société a développé le système d'exploitation Windows ?",
        choices: ["Apple", "Microsoft", "Google", "IBM"],
        answer: 1,
        type: "qcm",
        timeLimit: 20,
        explanation: "Windows est développé par Microsoft."
      }
    ]
  },

  scientifique: {
    title: "🔬 Quiz Scientifique",
    description: "Découvrez les mystères de la science",
    icon: "fi-rr-flask",
    questions: [
      {
        id: 1,
        q: "Quelle est la planète la plus proche du Soleil ?",
        choices: ["Vénus", "Mars", "Mercure", "Jupiter"],
        answer: 2,
        type: "qcm",
        timeLimit: 25,
        explanation: "Mercure est la planète la plus proche du Soleil."
      },
      {
        id: 2,
        q: "L'eau bout à 100°C à pression atmosphérique normale.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Oui, l'eau bout à 100°C au niveau de la mer."
      },
      {
        id: 3,
        q: "Quel est l'élément chimique le plus abondant dans l'univers ?",
        choices: ["Oxygène", "Carbone", "Hydrogène", "Hélium"],
        answer: 2,
        type: "qcm",
        timeLimit: 30,
        explanation: "L'hydrogène représente environ 75% de la masse de l'univers."
      },
      {
        id: 4,
        q: "La lumière voyage plus vite que le son.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "La lumière voyage à 300,000 km/s, le son à environ 340 m/s."
      },
      {
        id: 5,
        q: "Combien de os compte le corps humain adulte ?",
        choices: ["206", "300", "150", "250"],
        answer: 0,
        type: "qcm",
        timeLimit: 25,
        explanation: "Le squelette humain adulte compte 206 os."
      },
      {
        id: 6,
        q: "Les plantes respirent du CO2 la nuit.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 20,
        explanation: "Les plantes respirent du CO2 la nuit (respiration cellulaire)."
      },
      {
        id: 7,
        q: "Qui a découvert la pénicilline ?",
        choices: ["Marie Curie", "Alexander Fleming", "Louis Pasteur", "Albert Einstein"],
        answer: 1,
        type: "qcm",
        timeLimit: 25,
        explanation: "Alexander Fleming a découvert la pénicilline en 1928."
      },
      {
        id: 8,
        q: "L'ADN signifie Acide Désoxyribonucléique.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Exact, ADN = Acide Désoxyribonucléique."
      },
      {
        id: 9,
        q: "Quelle est la vitesse de la lumière dans le vide ?",
        choices: ["300,000 km/s", "150,000 km/s", "500,000 km/s", "1,000,000 km/s"],
        answer: 0,
        type: "qcm",
        timeLimit: 25,
        explanation: "La lumière voyage à 299,792 km/s dans le vide."
      },
      {
        id: 10,
        q: "L'homme descend du singe.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 20,
        explanation: "L'homme et les singes ont un ancêtre commun, mais l'homme ne descend pas du singe."
      },
      {
        id: 11,
        q: "Quel est le plus grand organe du corps humain ?",
        choices: ["Le foie", "Le cerveau", "La peau", "Les poumons"],
        answer: 2,
        type: "qcm",
        timeLimit: 25,
        explanation: "La peau est le plus grand organe du corps humain."
      },
      {
        id: 12,
        q: "L'atome est divisible.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "L'atome est composé de protons, neutrons et électrons."
      }
    ]
  },

  histoire: {
    title: "🏛️ Quiz Histoire",
    description: "Voyagez à travers les grandes périodes historiques",
    icon: "fi-rr-landmark",
    questions: [
      {
        id: 1,
        q: "En quelle année a eu lieu la Révolution Française ?",
        choices: ["1789", "1776", "1799", "1815"],
        answer: 0,
        type: "qcm",
        timeLimit: 25,
        explanation: "La Révolution Française a commencé en 1789 avec la prise de la Bastille."
      },
      {
        id: 2,
        q: "Jules César a été le premier empereur romain.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 20,
        explanation: "Auguste fut le premier empereur romain, Jules César était dictateur."
      },
      {
        id: 3,
        q: "Qui a découvert l'Amérique en 1492 ?",
        choices: ["Christophe Colomb", "Marco Polo", "Vasco de Gama", "Magellan"],
        answer: 0,
        type: "qcm",
        timeLimit: 20,
        explanation: "Christophe Colomb a découvert l'Amérique en 1492."
      },
      {
        id: 4,
        q: "La Première Guerre mondiale s'est terminée en 1918.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "L'armistice a été signé le 11 novembre 1918."
      },
      {
        id: 5,
        q: "Quelle civilisation a construit les pyramides de Gizeh ?",
        choices: ["Les Romains", "Les Grecs", "Les Égyptiens", "Les Mayas"],
        answer: 2,
        type: "qcm",
        timeLimit: 25,
        explanation: "Les pyramides de Gizeh ont été construites par les Égyptiens de l'Antiquité."
      },
      {
        id: 6,
        q: "Louis XIV était surnommé le Roi Soleil.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Oui, Louis XIV était bien surnommé le Roi Soleil."
      },
      {
        id: 7,
        q: "Quand a eu lieu la chute du mur de Berlin ?",
        choices: ["1987", "1989", "1991", "1985"],
        answer: 1,
        type: "qcm",
        timeLimit: 25,
        explanation: "Le mur de Berlin est tombé le 9 novembre 1989."
      },
      {
        id: 8,
        q: "Napoléon Bonaparte est mort en exil à Sainte-Hélène.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Napoléon est mort en exil sur l'île de Sainte-Hélène en 1821."
      },
      {
        id: 9,
        q: "Qui a été le premier président des États-Unis ?",
        choices: ["Thomas Jefferson", "George Washington", "Abraham Lincoln", "John Adams"],
        answer: 1,
        type: "qcm",
        timeLimit: 20,
        explanation: "George Washington fut le premier président des États-Unis (1789-1797)."
      },
      {
        id: 10,
        q: "La Renaissance a commencé en Italie.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "La Renaissance est effectivement née en Italie au XIVe siècle."
      }
    ]
  },

  culture: {
    title: "🎭 Quiz Culture Générale",
    description: "Testez votre culture générale dans tous les domaines",
    icon: "fi-rr-brain",
    questions: [
      {
        id: 1,
        q: "Qui a peint la Joconde ?",
        choices: ["Michel-Ange", "Léonard de Vinci", "Raphaël", "Van Gogh"],
        answer: 1,
        type: "qcm",
        timeLimit: 25,
        explanation: "La Joconde a été peinte par Léonard de Vinci au XVIe siècle."
      },
      {
        id: 2,
        q: "Shakespeare a écrit Roméo et Juliette.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Roméo et Juliette est bien une tragédie de Shakespeare."
      },
      {
        id: 3,
        q: "Quel est l'océan le plus grand du monde ?",
        choices: ["Atlantique", "Indien", "Pacifique", "Arctique"],
        answer: 2,
        type: "qcm",
        timeLimit: 20,
        explanation: "L'océan Pacifique est le plus grand, couvrant environ 1/3 de la surface terrestre."
      },
      {
        id: 4,
        q: "Le français est la langue officielle du Brésil.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Le Brésil parle portugais, pas français."
      },
      {
        id: 5,
        q: "Quelle est la capitale de l'Australie ?",
        choices: ["Sydney", "Melbourne", "Canberra", "Perth"],
        answer: 2,
        type: "qcm",
        timeLimit: 25,
        explanation: "Canberra est la capitale de l'Australie, choisie comme compromis entre Sydney et Melbourne."
      },
      {
        id: 6,
        q: "Mozart était un compositeur allemand.",
        choices: ["Vrai", "Faux"],
        answer: 1,
        type: "truefalse",
        timeLimit: 20,
        explanation: "Mozart était autrichien, né à Salzbourg."
      },
      {
        id: 7,
        q: "Combien de continents y a-t-il sur Terre ?",
        choices: ["5", "6", "7", "8"],
        answer: 2,
        type: "qcm",
        timeLimit: 25,
        explanation: "Il y a 7 continents : Afrique, Amérique du Nord, Amérique du Sud, Antarctique, Asie, Europe, Océanie."
      },
      {
        id: 8,
        q: "Le mont Everest est la plus haute montagne du monde.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Le mont Everest culmine à 8,849 mètres d'altitude."
      },
      {
        id: 9,
        q: "Quel animal est le symbole des États-Unis ?",
        choices: ["L'aigle", "Le bison", "Le lion", "L'ours"],
        answer: 0,
        type: "qcm",
        timeLimit: 20,
        explanation: "Le pygargue à tête blanche (aigle) est le symbole des États-Unis."
      },
      {
        id: 10,
        q: "Victor Hugo a écrit 'Les Misérables'.",
        choices: ["Vrai", "Faux"],
        answer: 0,
        type: "truefalse",
        timeLimit: 15,
        explanation: "Victor Hugo est bien l'auteur des Misérables, publié en 1862."
      }
    ]
  }
};

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

// Classe pour gérer les scores
class ScoreManager {
  constructor() {
    this.highScores = this.loadHighScores();
  }

  loadHighScores() {
    const saved = localStorage.getItem('quizHighScores');
    return saved ? JSON.parse(saved) : {};
  }

  saveHighScores() {
    localStorage.setItem('quizHighScores', JSON.stringify(this.highScores));
  }

  addScore(quizId, score, timeSpent, totalQuestions) {
    if (!this.highScores[quizId]) {
      this.highScores[quizId] = [];
    }
    
    const newScore = {
      score,
      timeSpent,
      totalQuestions,
      date: new Date().toLocaleDateString('fr-FR'),
      timestamp: Date.now(),
      percentage: Math.round((score / (totalQuestions * 10)) * 100)
    };
    
    this.highScores[quizId].push(newScore);
    // Garder seulement les 5 meilleurs scores par quiz
    this.highScores[quizId].sort((a, b) => b.percentage - a.percentage || a.timeSpent - b.timeSpent);
    this.highScores[quizId] = this.highScores[quizId].slice(0, 5);
    this.saveHighScores();
  }

  getBestScore(quizId) {
    return this.highScores[quizId] && this.highScores[quizId].length > 0 
      ? Math.max(...this.highScores[quizId].map(s => s.percentage)) 
      : 0;
  }

  getScoresForQuiz(quizId) {
    return this.highScores[quizId] || [];
  }
}

// STATE
let currentQuiz = null;
let currentIndex = 0;
let score = 0;
let answered = false;
let timer = null;
let totalTimeSpent = 0;
let scoreManager = new ScoreManager();

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
const totalCountEl = document.getElementById('total-count');
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

// Nouveaux éléments DOM
const timerEl = document.createElement('div');
timerEl.className = 'timer';
timerEl.innerHTML = '<i class="fi fi-rr-clock"></i> <span id="time-remaining">0</span>s';

const highScoresSection = document.createElement('section');
highScoresSection.id = 'high-scores';
highScoresSection.className = 'card hidden';
highScoresSection.innerHTML = `
  <h2>Meilleurs Scores</h2>
  <div id="high-scores-list"></div>
  <button id="close-scores" class="btn primary">Retour</button>
`;

const quizSelection = document.createElement('section');
quizSelection.id = 'quiz-selection';
quizSelection.className = 'card';
quizSelection.innerHTML = `
  <h2 style="text-align: center; margin-bottom: 32px;">Choisissez un Quiz</h2>
  <div class="quiz-grid" id="quiz-grid"></div>
`;

// Ajout des nouveaux éléments au DOM
document.querySelector('.quiz-header .meta').after(timerEl);
document.querySelector('.container').appendChild(highScoresSection);
document.querySelector('.container').insertBefore(quizSelection, welcome);

// Functions
function show(element) {
  element.classList.remove('hidden');
}

function hide(element) {
  element.classList.add('hidden');
}

function renderQuizSelection() {
  const quizGrid = document.getElementById('quiz-grid');
  quizGrid.innerHTML = '';
  
  Object.entries(QUIZ_DATABASE).forEach(([quizId, quizData]) => {
    const bestScore = scoreManager.getBestScore(quizId);
    const quizCard = document.createElement('div');
    quizCard.className = 'quiz-card';
    quizCard.innerHTML = `
      <div class="quiz-card-icon">
        <i class="fi ${quizData.icon}"></i>
      </div>
      <h3>${quizData.title}</h3>
      <p>${quizData.description}</p>
      <div class="quiz-meta">
        <span><i class="fi fi-rr-list"></i> ${quizData.questions.length} questions</span>
        ${bestScore > 0 ? `<span class="best-score"><i class="fi fi-rr-trophy"></i> Meilleur: ${bestScore}%</span>` : ''}
      </div>
      <button class="btn primary select-quiz" data-quiz="${quizId}">
        <span>Commencer <i class="fi fi-rr-arrow-right"></i></span>
      </button>
    `;
    quizGrid.appendChild(quizCard);
  });
}

function selectQuiz(quizId) {
  currentQuiz = QUIZ_DATABASE[quizId];
  startQuiz();
}

function startQuiz() {
  currentIndex = 0;
  score = 0;
  answered = false;
  totalTimeSpent = 0;
  scoreEl.textContent = score;
  hide(welcome);
  hide(quizSelection);
  hide(result);
  hide(modal);
  hide(highScoresSection);
  show(quiz);
  
  // Mettre à jour les totaux
  qTotalEl.textContent = currentQuiz.questions.length;
  finalTotal.textContent = currentQuiz.questions.length;
  
  renderQuestion();
  document.getElementById('question-area').focus?.();
}

function renderQuestion() {
  const q = currentQuiz.questions[currentIndex];
  qIndexEl.textContent = currentIndex + 1;
  questionText.textContent = q.q;
  
  // Ajouter l'indicateur de type
  questionText.innerHTML = q.q;
  const typeIndicator = document.createElement('span');
  typeIndicator.className = 'question-type';
  typeIndicator.textContent = q.type === 'truefalse' ? 'Vrai/Faux' : 'QCM';
  questionText.appendChild(typeIndicator);
  
  choicesEl.innerHTML = '';
  feedbackEl.textContent = '';
  feedbackEl.innerHTML = ''; // Clear previous content
  nextBtn.disabled = true;
  answered = false;

  // Démarrer le chronomètre
  if (timer) timer.stop();
  timer = new Timer(q.timeLimit, updateTimerDisplay, onTimeUp);
  timer.start();

  q.choices.forEach((choiceText, idx) => {
    const btn = document.createElement('button');
    btn.className = 'choice';
    btn.setAttribute('role', 'listitem');
    btn.setAttribute('data-index', idx);
    btn.setAttribute('tabindex', 0);
    btn.innerHTML = `<span class="choice-label">${choiceText}</span>`;
    
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        btn.click();
      }
    });
    
    btn.addEventListener('click', () => onSelectChoice(btn, idx));
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
      timerEl.style.animation = 'pulse 0.5s infinite';
    } else if (remaining <= 10) {
      timerEl.style.color = 'var(--accent-2)';
      timerEl.style.animation = 'pulse 1s infinite';
    } else {
      timerEl.style.color = 'var(--text)';
      timerEl.style.animation = 'none';
    }
  }
}

function onTimeUp() {
  if (answered) return;
  answered = true;
  
  const q = currentQuiz.questions[currentIndex];
  const correctEl = choicesEl.querySelector(`[data-index="${q.answer}"]`);
  if (correctEl) correctEl.setAttribute('data-state', 'correct');
  
  feedbackEl.innerHTML = `
    <div class="feedback-content">
      <i class="fi fi-rr-clock"></i>
      <div>
        <strong>Temps écoulé !</strong>
        <p>${q.explanation}</p>
      </div>
    </div>
  `;
  feedbackEl.style.background = 'rgba(248, 113, 113, 0.1)';
  feedbackEl.style.color = 'var(--danger)';
  feedbackEl.style.border = '1px solid rgba(248, 113, 113, 0.3)';
  
  feedbackEl.setAttribute('role', 'status');
  nextBtn.disabled = false;
  nextBtn.focus();
}

function onSelectChoice(btn, idx) {
  if (answered) return;
  answered = true;
  
  const timeSpentOnQuestion = currentQuiz.questions[currentIndex].timeLimit - timer.getRemainingTime();
  totalTimeSpent += timeSpentOnQuestion;
  timer.stop();

  const q = currentQuiz.questions[currentIndex];
  const correct = idx === q.answer;

  btn.setAttribute('aria-pressed', 'true');
  
  // Calcul du score avec pénalité temporelle
  const basePoints = 10;
  const timePenalty = Math.floor(timeSpentOnQuestion / 3);
  const pointsEarned = correct ? Math.max(1, basePoints - timePenalty) : 0;
  
  if (correct) {
    btn.setAttribute('data-state', 'correct');
    feedbackEl.innerHTML = `
      <div class="feedback-content">
        <i class="fi fi-rr-check-circle"></i>
        <div>
          <strong>Excellente réponse ! +${pointsEarned} points</strong>
          <p>${q.explanation}</p>
        </div>
      </div>
    `;
    feedbackEl.style.background = 'rgba(52, 211, 153, 0.1)';
    feedbackEl.style.color = 'var(--success)';
    feedbackEl.style.border = '1px solid rgba(52, 211, 153, 0.3)';
    score += pointsEarned;
  } else {
    btn.setAttribute('data-state', 'wrong');
    const correctEl = choicesEl.querySelector(`[data-index="${q.answer}"]`);
    if (correctEl) correctEl.setAttribute('data-state', 'correct');
    feedbackEl.innerHTML = `
      <div class="feedback-content">
        <i class="fi fi-rr-cross-circle"></i>
        <div>
          <strong>Incorrect - Pénalité: -${timePenalty} points</strong>
          <p>${q.explanation}</p>
        </div>
      </div>
    `;
    feedbackEl.style.background = 'rgba(248, 113, 113, 0.1)';
    feedbackEl.style.color = 'var(--danger)';
    feedbackEl.style.border = '1px solid rgba(248, 113, 113, 0.3)';
  }
  
  scoreEl.textContent = score;
  feedbackEl.setAttribute('role', 'status');
  nextBtn.disabled = false;
  nextBtn.focus();
}

function goNext() {
  quiz.style.animation = 'slideOutLeft 0.4s ease';
  setTimeout(() => {
    currentIndex++;
    if (currentIndex >= currentQuiz.questions.length) {
      finishQuiz();
      return;
    }
    renderQuestion();
    quiz.style.animation = 'slideInRight 0.4s ease';
  }, 400);
}

function finishQuiz() {
  hide(quiz);
  show(result);
  finalScore.textContent = score;
  const totalPossible = currentQuiz.questions.length * 10;
  const pct = Math.round((score / totalPossible) * 100);
  
  // Sauvegarder le score
  scoreManager.addScore(Object.keys(QUIZ_DATABASE).find(key => QUIZ_DATABASE[key] === currentQuiz), 
                       score, totalTimeSpent, currentQuiz.questions.length);
  
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
  } else if (pct >= 50) {
    message = "Pas mal ! 💪";
    icon = "fi-rr-gym";
    color = "var(--accent-2)";
  } else {
    message = "Continue tes efforts ! 📚";
    icon = "fi-rr-book";
    color = "var(--danger)";
  }
  
  resultMessage.innerHTML = `
    <div style="text-align: center;">
      <p style="color:${color}; font-size: 1.8rem; margin-bottom: 16px;">
        <i class="fi ${icon}"></i> ${message}
      </p>
      <p style="font-size: 1.2rem; margin-bottom: 8px;">Score: ${pct}% (${score}/${totalPossible} points)</p>
      <p style="color: var(--text-muted);">Temps total: ${Math.floor(totalTimeSpent / 60)}min ${totalTimeSpent % 60}s</p>
    </div>
  `;
  
  progressBar.style.width = '100%';
  
  // Ajouter le bouton pour voir les meilleurs scores
  const viewHighScoresBtn = document.createElement('button');
  viewHighScoresBtn.className = 'btn';
  viewHighScoresBtn.innerHTML = '<span><i class="fi fi-rr-trophy"></i> Voir les meilleurs scores</span>';
  viewHighScoresBtn.addEventListener('click', showHighScores);
  result.querySelector('.controls').appendChild(viewHighScoresBtn);
}

function showHighScores() {
  hide(result);
  show(highScoresSection);
  
  const highScoresList = document.getElementById('high-scores-list');
  highScoresList.innerHTML = '';
  
  const quizId = Object.keys(QUIZ_DATABASE).find(key => QUIZ_DATABASE[key] === currentQuiz);
  const scores = scoreManager.getScoresForQuiz(quizId);
  
  if (scores.length === 0) {
    highScoresList.innerHTML = '<p style="text-align: center; color: var(--text-muted);">Aucun score enregistré pour ce quiz</p>';
    return;
  }
  
  const ol = document.createElement('ol');
  ol.className = 'high-scores-list';
  
  scores.forEach((scoreData, index) => {
    const li = document.createElement('li');
    li.innerHTML = `
      <span class="score-rank">#${index + 1}</span>
      <span class="score-value">${scoreData.percentage}%</span>
      <span class="score-details">${scoreData.score}/${scoreData.totalQuestions * 10} pts</span>
      <span class="score-time">${Math.floor(scoreData.timeSpent / 60)}min ${scoreData.timeSpent % 60}s</span>
      <span class="score-date">${scoreData.date}</span>
    `;
    ol.appendChild(li);
  });
  
  highScoresList.appendChild(ol);
}

function quitQuiz() {
  if (timer) timer.stop();
  hide(quiz);
  show(quizSelection);
  feedbackEl.textContent = '';
}

function goHome() {
  hide(result);
  hide(quiz);
  show(quizSelection);
}

// Events
startBtn.addEventListener('click', () => show(quizSelection));
viewInst.addEventListener('click', () => show(modal));
closeModal.addEventListener('click', () => hide(modal));
nextBtn.addEventListener('click', goNext);
quitBtn.addEventListener('click', quitQuiz);
retryBtn.addEventListener('click', startQuiz);
homeBtn.addEventListener('click', goHome);

// Gestion de la sélection des quiz
document.addEventListener('click', (e) => {
  if (e.target.closest('.select-quiz')) {
    const quizId = e.target.closest('.select-quiz').dataset.quiz;
    selectQuiz(quizId);
  }
});

document.getElementById('close-scores').addEventListener('click', () => {
  hide(highScoresSection);
  show(result);
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    if (!modal.classList.contains('hidden')) hide(modal);
    if (!highScoresSection.classList.contains('hidden')) {
      hide(highScoresSection);
      show(result);
    }
  }
});

// Modal focus trap
modal.addEventListener('keydown', (e) => {
  if (e.key === 'Tab') {
    const focusables = modal.querySelectorAll('button, [tabindex]:not([tabindex="-1"])');
    if (focusables.length === 0) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }
});

// Navigation au clavier
choicesEl.addEventListener('keydown', (e) => {
  const active = document.activeElement;
  if (!active.classList || !active.classList.contains('choice')) return;
  
  if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
    e.preventDefault();
    const next = active.nextElementSibling || choicesEl.firstElementChild;
    next.focus();
  } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
    e.preventDefault();
    const prev = active.previousElementSibling || choicesEl.lastElementChild;
    prev.focus();
  }
});

viewInst.addEventListener('click', () => {
  setTimeout(() => closeModal.focus(), 50);
});

// Initialisation
window.addEventListener('load', () => {
  renderQuizSelection();
  startBtn.focus();
});