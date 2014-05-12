<div class="row">
    <div class="col-md-8"><h1>2014 Database Online Judge System</h1></div>
    <div class="col-md-4" style="padding-top: 20px">
        Time Remains: <span id="time-remains">00:00:00</span>
        <a href="<?= site_url('main/help') ?>" class="btn btn-default">Help</a>
        <a href="<?= site_url('auth/logout') ?>" class="btn btn-primary">Finish Test!</a>
    </div>
</div>
<h4>Student Number: <?= $this->session->userdata('stdid'); ?></h4>
<h4>Score: <?= $score ?></h4>

<hr>
<script type="text/javascript">
$(function () {
    var elTimeRemains = $('#time-remains');
    var expireTime = <?= (strtotime($this->config->item('end_time', 'sqljudge'))) ?>;
    var computeInterval = function () {
        var totalSec = expireTime - parseInt(new Date().getTime() / 1000, 10),
            hours = parseInt( totalSec / 3600 ) % 24,
            minutes = parseInt( totalSec / 60 ) % 60,
            seconds = totalSec % 60,
            result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
        if (totalSec > expireTime) {
            location.reload();
        } else {
            elTimeRemains.text(result);
        }
    };
    var interval = setInterval(computeInterval, 1000);
    computeInterval();
});
</script>