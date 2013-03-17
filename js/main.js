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

  $('td.more-info-button a').click(function(event) {
    $(this).find('i').toggleClass('icon-plus icon-minus');
    event.preventDefault();
  });

  // Toggle Bootstrap form-validation classes when form fields are validated.
  $('#post-form')
    .h5Validate()
    .on('validated', function(event, v) {
      $(v.element)
        .parents('.control-group')
          .toggleClass('error', ( ! v.valid))
          .toggleClass('success', v.valid);
    });
});
