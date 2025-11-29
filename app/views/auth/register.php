<?php require_once '../app/views/partials/header.php'; ?>

<section class="card center" style="max-width: 400px; margin: 0 auto;">
    <h2>Inscription</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="auth-message error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>?page=register" class="auth-form">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required 
               pattern="[a-zA-Z0-9_]{3,20}" title="3-20 caractères alphanumériques">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe (min 6 caractères)" 
               required minlength="6">
        <button type="submit" class="btn primary">
            <i class="fi fi-rr-user-add"></i> S'inscrire
        </button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Déjà un compte ? 
        <a href="/quiz_interactif_suite/public/login">Se connecter</a>
    </p>
</section>

<?php require_once '../app/views/partials/footer.php'; ?>