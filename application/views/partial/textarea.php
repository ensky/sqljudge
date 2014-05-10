<textarea id="sql-textarea" name="query" class="col-md-11"><?= $this->input->post('query') ? $this->input->post('query') : $answer->answer ?></textarea>
<script type="text/javascript">
$(function () {
    var editor = CodeMirror.fromTextArea(document.querySelector('#sql-textarea'), {
        mode: 'text/x-mariadb',
        indentWithTabs: true,
        smartIndent: true,
        lineNumbers: true,
        matchBrackets : true,
        autofocus: true 
    });
    var input = document.getElementById("selectTheme");
    function selectTheme() {
        var theme = input.options[input.selectedIndex].innerHTML;
        editor.setOption("theme", theme);
    }
});
</script>