<a href="<?= site_url('main') ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to the Problem list</a>

<button id="log-start" style="display: none" class="btn btn-success">Start</button>
<button id="log-pause" class="btn btn-default">Pause</button>

<div class="row">
    <div class="col-md-12" style="overflow-y: scroll; height: 800px;">
        <table id="log-table" class="table table-striped">
            <thead>
                <th>ID</th>
                <th>stdid</th>
                <th>log</th>
                <th>sql</th>
                <th>time</th>
                <th>score</th>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
$(function () {
    var $table = $('#log-table').DataTable({
            columns: [
                { data: 'id' },
                { data: 'stdid' },
                { data: 'log' },
                { data: 'sql' },
                { data: 'time' },
                { data: 'score' }
            ]
        }),
        stopped = false,
        maxid = 0,
        getLogs = function () {
            $.get("<?= site_url() ?>ta/get_log/" + maxid, {}, function (logs) {
                logs = JSON.parse(logs);
                logs.forEach(function (log, index) {
                    $table.row.add( log );
                    if (log.id > maxid) {
                        maxid = parseInt(log.id, 10);
                    }
                });
                $table.draw();
                $table.columns.adjust();
                if (!stopped)
                    getLogs();
            });
        };
    getLogs();

    var $pause = $('#log-pause'),
        $start = $('#log-start');
    $pause.click(function () {
        stopped = true;
        $(this).hide();
        $start.show();
    });
    $start.click(function () {
        stopped = false;
        $(this).hide();
        $pause.show();
        getLogs();
    });
});
</script>
