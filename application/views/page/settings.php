<a href="<?= site_url('main') ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to the Problem list</a>

<?= form_open('ta/setting', ['class' => 'form']) ?>
<?php foreach ($settings as $setting): ?>
<div class="form-group">
    <label for="setting-form-<?= $setting->key ?>"><?= $setting->key ?></label>
    <input type="text" id="setting-form-<?= $setting->key ?>" name="<?= $setting->key ?>" value="<?= $setting->value ?>">
</div>
<?php endforeach; ?>
<button class="btn btn-primary" type="submit">Save</button>
<?= form_close() ?>

<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector:'.tinymce',
        plugins: [
            'contextmenu code'
        ],
        contextmenu: "link image inserttable | cell row column deletetable"
    });

    $('button.fold').click(function () {
        var el = $(this).attr('data-target'),
            span = $(this).find('span');
        $(el).find('tbody').toggle(500);
        var css = span.hasClass('glyphicon-minus') ? 'glyphicon-plus' : 'glyphicon-minus';
        span.attr('class', 'glyphicon ' + css);
    });
    $('.folded tbody').hide();
</script>