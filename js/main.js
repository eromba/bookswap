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

$(function() {
  // When the page loads, focus the search bar
  // and set the insertion pointer to the end of the search query.
  $searchInput = $('#search-input').focus();
  var searchValue = $searchInput.val();
  $searchInput.val('').val(searchValue);

  // Prevent empty search queries.
  $('#search-button').click(function(event) {
    if ($searchInput.val() === '') {
      event.preventDefault();
    }
  });

  $('#login-modal').on('shown', function() {
    $('#netid-field').focus();
  });

  $('.login.btn').click(function(event) {
    $('#login-modal').modal('show');
    event.preventDefault();
  });
});
