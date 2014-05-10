<div class="header">
    <h1>Database 2014 online judging system</h1>
</div>

<?= form_open('auth/login', ['class'=>'form', 'method' => 'post']); ?>
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?= $errors ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="stdid">Student ID</label>
        <input class="form-control" id="stdid" name="stdid" autofocus placeholder="Enter your Student ID">
    </div>
    <button class="btn btn-primary">Login</button>
</form>