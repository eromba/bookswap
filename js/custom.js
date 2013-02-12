function delete_post(id){
	var jqxhr = $.post("myposts/remove", { post_id: id})
	.done(function(data) { 
		if(data!="Error"){
			$("#postid_"+data).fadeOut("normal", function() {
	        			$(this).remove();
	        		});
		}
	})
	.fail(function() { alert("error"); })
}
$(".post-del-btn").click(function(event) {
	idstring = $(this).parent().attr("id");
	idstring = idstring.substring(7);
});
$("#delete-confirm-button").click(function(event) {
	delete_post(idstring);
});
