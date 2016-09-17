<!DOCTYPE html>
<html lang="<?= Gundi()->config->getParam('core.default_lang_code'); ?>">
<head>
    <meta charset="utf-8">
</head>
<body>
<div class="container">
    <?= $this->getContent(); ?>
</div>
</body>
</html>