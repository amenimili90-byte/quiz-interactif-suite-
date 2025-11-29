    </main>

    <footer class="site-footer">
        <small>
            Quiz Interactif Multi-Thèmes • Version PHP avec Base de Données • 
            Responsive & Accessible • 
            <a href="/quiz_interactif_suite/public/admin" style="color: inherit;">
                Administration
            </a>
        </small>
    </footer>

    <script src="/quiz_interactif_suite/public/js/script-php.js"></script>
    
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
    <script>
        // Supprimer les messages flash après 5 secondes
        setTimeout(() => {
            const messages = document.querySelectorAll('.auth-message');
            messages.forEach(msg => msg.style.display = 'none');
        }, 5000);
    </script>
    <?php endif; ?>
</body>
</html>