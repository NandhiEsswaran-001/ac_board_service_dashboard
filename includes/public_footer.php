<?php
$jsFile = __DIR__ . '/../js/app.js';
$jsVer = file_exists($jsFile) ? filemtime($jsFile) : time();
?>
</main>

<footer class="landing-footer">
    <div class="container footer-inner">
        <p><?= APP_NAME ?> &copy; <?= date('Y') ?>. Clean service. Trusted support. Practical solutions.</p>
    </div>
</footer>

<script src="js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>
