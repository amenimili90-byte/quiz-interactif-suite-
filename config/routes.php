<?php
/**
 * Configuration des routes
 */

$routes = [
    'GET' => [
        '/' => 'QuizController@index',
        '/quiz' => 'QuizController@index',
        '/quiz/play' => 'QuizController@play',
        '/quiz/result' => 'QuizController@result',
        '/leaderboard' => 'ScoreController@leaderboard',
        '/stats' => 'ScoreController@stats',
        '/admin' => 'AdminController@dashboard',
        '/admin/quizzes' => 'AdminController@quizzes',
        '/admin/questions' => 'AdminController@questions',
        '/login' => 'AuthController@loginForm',
        '/register' => 'AuthController@registerForm',
        '/score/leaderboard' => 'ScoreController@leaderboard',
        '/score/stats' => 'ScoreController@stats',
        '/admin/dashboard' => 'AdminController@dashboard',
        '/admin/quizzes' => 'AdminController@quizzes',
        '/admin/questions' => 'AdminController@questions',
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/register' => 'AuthController@register',
        '/logout' => 'AuthController@logout',
        '/admin/quiz/create' => 'AdminController@createQuiz',
        '/admin/question/create' => 'AdminController@createQuestion',
         '/score/save' => 'ScoreController@save',
        '/admin/quiz/create' => 'AdminController@createQuiz',
        '/admin/question/create' => 'AdminController@createQuestion',
    ]
];