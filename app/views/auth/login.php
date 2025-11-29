<?php require_once '../app/views/partials/header.php'; ?>

<section class="card center" style="max-width: 400px; margin: 0 auto;">
    <h2>Connexion</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="auth-message error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="auth-message success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>?page=login" class="auth-form">
        <input type="text" name="identifier" placeholder="Nom d'utilisateur ou email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" class="btn primary">
            <i class="fi fi-rr-sign-in-alt"></i> Se connecter
        </button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Pas de compte ? 
        <a href="/quiz_interactif_suite/public/register">S'inscrire</a>
    </p>
</section>

<?php require_once '../app/views/partials/footer.php'; ?>