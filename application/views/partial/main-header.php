<div class="row">
    <div class="col-md-8"><h1>2014 Database Online Judge System</h1></div>
    <div class="col-md-4" style="padding-top: 20px">
        Time Remains: <span id="time-remains">00:00:00</span>
        <a href="<?= site_url('auth/logout') ?>" class="btn btn-primary">Finish Test!</a>
    </div>
</div>
<h4>Student Number: <?= $this->session->userdata('stdid'); ?></h4>
<h4>Score: <?= $score ?></h4>

<hr>
