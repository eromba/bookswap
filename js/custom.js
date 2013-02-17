function deactivate_post(id){
	var jqxhr = $.post("myposts/deactivate", { post_id: id})
	.done(function(data) { 
		if(data!="Error"){
			$("#postid_"+data).fadeOut("normal", function() {
	        			$(this).remove();
	        		});
		}
	})
	.fail(function() { alert("error"); })
}
$(".post-deactivate-btn").click(function(event) {
	idstring = $(this).parent().attr("id");
	idstring = idstring.substring(7);
});
$("#deactivate-confirm-button").click(function(event) {
	deactivate_post(idstring);
});
