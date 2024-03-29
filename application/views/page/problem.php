<?php $this->load->view('partial/main-header', ['score' => $score]); ?>

<a id="title" href="<?= site_url('main') ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to the Problem list</a>

<h2>
    <div><span class="label label-<?= $solved ? 'success' : 'primary' ?>"><?= $solved ? 'solved' : 'unsolved' ?></span> <?= $problem->order ?>. <?= $problem->title ?></div>
</h2>
<div class="row">
    <div class="col-md-6">
        <div id="description" class="block">
            <h2>Description <small>Problem description</small></h2>
            <?= $problem->description ?>
        </div>
        <div class="block">
            <h2>Tables <small>Tables used in the problem(test only, different from the table for judge)</small></h2>
            <div class="dbtables">
            <?php foreach ($test_tables as $tableName => $table): ?>
                <hr>
                <h3><?= $tableName ?> <button data-target="#table-<?= $tableName ?>" class="btn fold"><span class="glyphicon glyphicon-minus"></span></button></h3>
                <div id="table-<?= $tableName ?>" class="">
                    <?php $this->load->view('partial/table', ['table' => $table, 'schema' => $test_schema[$tableName]]); ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <div class="block">
            <h2>Answers <small>Answers according to the above test tables</small></h2>
            <?php $this->load->view('partial/table', ['table' => $test_result, 'schema' => false]); ?>
        </div>
        <script type="text/javascript">
        $(function() {
            var $el = $('.dbtables');
            if ($el.width() < $el.get(0).scrollWidth)
                $('.dbtables').doubleScroll({contentElement: $('.dbtables')});
            $('button.fold').click(function () {
                var el = $(this).attr('data-target'),
                    span = $(this).find('span');
                $(el).find('tbody').toggle(500);
                var css = span.hasClass('glyphicon-minus') ? 'glyphicon-plus' : 'glyphicon-minus';
                span.attr('class', 'glyphicon ' + css);
            });
            $('.folded tbody').hide();
        });
        </script>
    </div>
    <div class="col-md-6">
        <div id="sql" class="block">
            <h2>Console <small>Please write down the SQL and test it here</small></h2>
            <?= form_open('main/problem/' . $problem->id, ['id'=>'form-sql','method' => 'post', 'data-pjax' => 'true']) ?>
            <div class="row">
                <div class="col-md-12 form-group">
                    <?php $this->load->view('partial/textarea', ['query' => $query, 'input_name' => 'query', 'schema' => $test_schema]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                <div class="pull-right">
                    <input type="hidden" name="type">
                    <input id="test-sql" type="button" class="btn btn-default" value="Test">
                    <input id="submit-sql" type="button" class="btn btn-success" value="Submit">
                    <input id="submit-btn" type="submit" style="display:none">
                    <script type="text/javascript">
                        $(function () {
                            var hiddenField = function (type) {
                                return function () {
                                    $('#form-sql input[name="type"]').val(type);
                                    $('#submit-btn').click();
                                };
                            };
                            $('#test-sql').click(hiddenField('Test'));
                            $('#submit-sql').click(hiddenField('Submit'));
                        });
                    </script>
                </div>
                </div>
            </div>
            </form>
        </div>
        <div id="result" class="block">
            <h2>Result <small>Testing result will display here</small></h2>
			<?php if (isset($result->error)): ?>
                <?php if($result->error): ?>
                <div class="alert alert-danger">Database error: <?= $result->error ?></div>
                <?php endif; ?>
                <?php if($result->type == "judge"): ?>
                    <?php if($result->is_correct): ?>
                        <div class="alert alert-success">Your answer is correct! please answer the next question <a href="<?= site_url('main') ?>">Go Back</a>.</div>
                    <?php else: ?>
                        <div class="alert alert-danger">Your answer is wrong.</div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($result->type == 'test'): ?>
                    <?php ! $result->error && $this->load->view('partial/table', ['table' => $result->data, 'schema' => false]); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
