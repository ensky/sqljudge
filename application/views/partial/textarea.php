<textarea id="<?= $input_name ?>" name="<?= $input_name?>" class="col-md-11"><?= $query ?></textarea>
<script type="text/javascript">
$(function () {
	var editor = CodeMirror.fromTextArea(document.querySelector('#<?= $input_name ?>'), {
		mode: 'text/x-mysql',
		theme: 'sqljudge',
		hint: CodeMirror.hint.sql,
        indentWithTabs: true,
        smartIndent: true,
        lineNumbers: true,
        matchBrackets : true,
		autofocus: true,
		hintOptions: {
			completeSingle: false
		},
		lineWrapping: true
	});
	editor.on('inputRead', function(instance){
		if (instance.state.completionActive)
			return;
		var cur = instance.getCursor();
		var str = instance.getTokenAt(cur).string;
		if(str.length > 0 && str.match(/^[.`\w@]\w*$/)){
			CodeMirror.commands.autocomplete(instance);
		}
	});
});
</script>
