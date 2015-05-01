<textarea id="<?= $input_name ?>" name="<?= $input_name?>" class="col-md-11"><?= $query ?></textarea>
<script type="text/javascript">
$(function () {
	var editor = CodeMirror.fromTextArea(document.querySelector('#<?= $input_name ?>'), {
        mode: 'text/x-mariadb',
        indentWithTabs: true,
        smartIndent: true,
        lineNumbers: true,
        matchBrackets : true,
        autofocus: true,
        lineWrapping: true
    });
});
</script>
