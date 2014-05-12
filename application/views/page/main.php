<?php $this->load->view('partial/main-header', ['score' => $score]); ?>

<?php if ($this->isTA): ?>
<a class="npjax btn btn-success" href="<?= site_url('ta/problem_edit/') ?>"><span class="glyphicon glyphicon-plus"></span> New problem</a>
<?php endif; ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Problem</th>
            <th>Score</th>
            <th>Result</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($problems as $problem): ?>
        <tr>
            <td class="col-md-8">
                <a href="<?= site_url('main/problem/'.$problem->id) ?>#title" style="font-weight: bold"><?= $problem->title ?></a>
            </td>
            <td><?= $problem->score?> %</td>
            <td>
                <?php $correct_stat = !isset($answers[$problem->id]) ? NULL : ($answers[$problem->id] === '1' ? True : False);
                    $ok_css = $correct_stat === True ? 'success' : 'default';
                    $nok_css = $correct_stat === False ? 'danger' : 'default';
                ?>
                <span class="label label-<?= $ok_css ?>"><span class="glyphicon glyphicon-ok"></span> Correct</span>
                <span class="label label-<?= $nok_css ?>"><span class="glyphicon glyphicon-remove"></span> Wrong answer</span>

                <?php if ($this->isTA): ?>
                    <a href="<?= site_url('ta/problem_edit/'.$problem->id) ?>" class="npjax btn btn-primary"><span class="glyphicon glyphicon-pencil"></span> Edit problem</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>