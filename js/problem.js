$(function(){
    $(document).on('click', 'input#test-sql, input#submit-sql', function(){
	$(this).prop('disabled', true);
    });
});
