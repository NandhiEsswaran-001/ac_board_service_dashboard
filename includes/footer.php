    </div><!-- end content-area -->
</main><!-- end main-content -->

</div><!-- end app-wrapper -->

<?php
$root = $rootPath ?? '../';
$jsFile = __DIR__ . '/' . $root . 'js/app.js';
$jsVer = file_exists($jsFile) ? filemtime($jsFile) : time();
?>
<script src="<?= $root ?>js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>
