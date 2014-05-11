<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DB 2014</title>
   <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">

    <!-- Optional theme -->
    <link rel="stylesheet" href="<?= base_url('css/bootstrap-theme.min.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?= base_url('css/codemirror.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('css/codemirror/base16-light.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('css/main.css') ?>">
    
    <script src="<?= base_url('js/jquery.min.js') ?>"></script>
</head>
<body>
    <div id="container" class="container-fluid">
        <?= $body ?>
    </div>
    <div class="container-fluid">
        <hr>
        Created by <a target="_blank" href="http://www.ensky.tw">EnskyLin</a>
    </div>
    <script src="<?= base_url('js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/codemirror.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/codemirror/sql.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/pjax.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/jquery-ui-1.10.4.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('js/jquery.doublescroll.js') ?>"></script>
    <script type="text/javascript">
    $(document).pjax('a', '#container');
    $(document).on('submit', 'form[data-pjax]', function(event) {
        $.pjax.submit(event, '#container')
    });
    </script>
</body>
</html>