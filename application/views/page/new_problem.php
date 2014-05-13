<small id="title"><a href="<?= site_url('main') ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to the Problem list</a></small>
<?= form_open('ta/problem_edit/' . $problem->id, ['method' => 'post']) ?>
<div class="form-group">
    <label for="order">Order</label>
    <input type="text" id="order" name="order" placeholder="題號" class="form-control" value="<?= $problem->order ?>">
</div>
<div class="form-group">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" class="form-control" value="<?= $problem->title ?>">
</div>
<div class="form-group">
    <label for="description">Description</label>
    <textarea class="tinymce" id="description" name="description"><?= $problem->description ?></textarea>
</div>
<div class="form-group">
    <label for="tables">Tables used(請先在 sqljudge_problem_test 和 sqljudge_problem_judge 內建立好需要的table和data, test是測試用, judge是正式改考卷的)</label>
    <input type="text" id="tables" name="tables" placeholder="逗點分隔，如: fruits,shopping_list" class="form-control" value="<?= $problem->tables ?>">
</div>
<div class="form-group">
    <label for="score">Score</label>
    <input type="text" id="score" name="score" placeholder="就是配分" class="form-control" value="<?= ($problem->score ? $problem->score : 5) ?>">
</div>
<div class="form-group">
    <label>Answer SQL</label>
    <?php $this->load->view('partial/textarea', ['answer' => $problem]); ?>
</div>
<input type="submit" class="btn btn-primary" value="Test and Save">
</form>

<hr>

<div class="row">
<div class="col-md-12">
    <h3>Testing database</h3>
    <div class="block">
        <h2>Result <small>Testing result will display here</small></h2>
        <?php if (isset($test->error)): ?>
            <?php if($test->error): ?>
            <div class="alert alert-danger">Database error: <?= $test->error ?></div>
            <?php endif; ?>

            <?php ! $test->error && $this->load->view('partial/table', ['table' => $test->data]); ?>
        <?php endif; ?>
    </div>
    <div class="block">
        <h2>Table info</h2>
        <?php foreach ($test->tables as $name => $table): ?>
            <h3><?= $name ?> <button data-target="#test-table-<?= $name ?>" class="btn fold"><span class="glyphicon glyphicon-plus"></span></button></h3>
            <div id="test-table-<?= $name ?>" class="folded">
            <?php $this->load->view('partial/table', ['table' => $table]); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php /*
<div class="col-md-6">
    <h3>Judging  database</h3>
    <div class="block">
        <h2>Result <small>Judging result will display here</small></h2>
        <?php if (isset($judge->error)): ?>
            <?php if($judge->error): ?>
            <div class="alert alert-danger">Database error: <?= $judge->error ?></div>
            <?php endif; ?>

            <?php ! $judge->error && $this->load->view('partial/table', ['table' => $judge->data]); ?>
        <?php endif; ?>
    </div>
    <div class="block">
        <h2>Table info</h2>
        <?php foreach ($judge->tables as $name => $table): ?>
            <h3><?= $name ?> <button data-target="#judge-table-<?= $name ?>" class="btn fold"><span class="glyphicon glyphicon-plus"></span></button></h3>
            <div id="judge-table-<?= $name ?>" class="folded">
            <?php $this->load->view('partial/table', ['table' => $table]); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>
*/ ?>

<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector:'.tinymce',
        plugins: [
            'contextmenu code image'
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