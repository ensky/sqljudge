<a href="<?= site_url('main') ?>" class="npjax btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to the Problem list</a>

<?= form_open('ta/setting', ['class' => 'form']) ?>
<?php foreach ($settings as $setting): ?>
<div class="form-group">
    <label for="setting-form-<?= $setting->key ?>"><?= $setting->key ?></label>
    <?php if ($setting->key == 'announcement' || $setting->key == 'help'): ?>
    <textarea id="setting-form-<?= $setting->key ?>" name="<?= $setting->key ?>" class="tinymce"><?= $setting->value ?></textarea>
    <?php else: ?>
    <input type="text" id="setting-form-<?= $setting->key ?>" name="<?= $setting->key ?>" value="<?= $setting->value ?>">
    <?php endif; ?>
</div>
<?php endforeach; ?>
<button class="btn btn-primary" type="submit">Save</button>
<?= form_close() ?>

<script>
    $(function () {
        tinymce.init({
            selector:'.tinymce',
            plugins: [
                'contextmenu code'
            ],
            contextmenu: "link image inserttable | cell row column deletetable"
        });
    });
</script>