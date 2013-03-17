(function(window, $, undefined) {

/**
 * CodeIgniter's CSRF cookie must be sent with each AJAX request.
 */
var settings = { 'data': {} };
settings['data'][BOOKSWAP.csrf_token_name] = $.cookie(BOOKSWAP.csrf_cookie_name);
$.ajaxSetup(settings);

/**
 * Updates a post via AJAX.
 *
 * @param pid The ID of the post to update.
 * @param data The data attributes to save to the database.
 */
function updatePost(pid, data) {
  return $.ajax({
      'url': BOOKSWAP.base_url + 'api/v1/posts/' + pid,
      'method': 'POST',
      'data': data
    })
    .fail(function() {
      alert('There was a problem updating your post.');
    });
}

/**
 * Deactivates the post associated with the specified post element.
 *
 * @param post A jQuery selector
 */
function deactivatePost(post) {
  var $post = $(post),
    pid = $post.data('pid');
  updatePost(pid, { 'active': false })
    .done(function(data) {
      // Create a hidden copy of the post in the "Deactivated Posts" section.
      var $deactivatedPost = $post.clone()
        .css({
          'height': 0,
          'margin-top': 0,
          'margin-bottom': 0,
          'opacity': 0,
          'padding-top': 0,
          'padding-bottom': 0
        })
        .find('.post-actions')
          .remove()
          .end()
        .prependTo('#deactivated-posts');
      // Save the post's vertical dimensions.
      var cssProps = $post.css(['margin-top', 'margin-bottom', 'padding-top', 'padding-bottom']);
      // The deactivated post does not have action buttons,
      // so the buttons' height must be subtracted from the post's height.
      cssProps['height'] = ($post.height() - $post.find('.post-actions').outerHeight(true));
      // Animate in 3 steps:
      // 1. Fade out the active post.
      // 2. "Slide up" the active post while "sliding down" the deactivated post.
      // 3. Fade in the deactivated post.
      $('#deactivated-posts').removeClass('no-posts');
      $post.fadeTo(500, 0, function() {
        $deactivatedPost.animate(cssProps, 500, 'swing');
        $post.slideUp(500, 'swing');
        $.when($post.promise(), $deactivatedPost.promise()).done(function() {
          $activePosts = $('#active-posts');
          $activePosts.toggleClass('no-posts', ($activePosts.children().length == 2));
          $deactivatedPost.fadeTo(500, 1);
          $post.remove();
        });
      });
    })
}

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

  $('.deactivate-post').click(function() {
    BOOKSWAP.postToDeactivate = $(this).parents('.post');
  });

  $('#confirm-post-deactivation').click(function(event) {
    deactivatePost(BOOKSWAP.postToDeactivate);
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

})(window, jQuery);
