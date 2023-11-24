<?php if (isset($errorList) && !empty($errorList)) : ?>
    <div class="alert alert-danger">
        <?php foreach ($errorList as $error) : ?>
            <p style="margin:0"><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>