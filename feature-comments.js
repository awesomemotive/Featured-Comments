/**
 * allow this to be called multiple times
 */
function featured_comments_click() {
	
	// unbind first
	jQuery('.feature-comments').unbind('click');
	
	// rebind
	jQuery('.feature-comments').click(function(){
		$this = jQuery(this);
		jQuery.post (
			featured_comments.ajax_url,
			{
				'action' : 'feature_comments',
				'do': $this.attr('data-do'),
				'comment_id': $this.attr('data-comment_id')
			},
			function ( response ) {
				var action = $this.attr('data-do'),
					comment_id = $this.attr('data-comment_id'),
					$comment = jQuery("#comment-" + comment_id + ", #li-comment-" + comment_id),
					$this_and_comment = $this.siblings('.feature-comments').add($comment).add($this);
				if ( action == 'feature' )
					$this_and_comment.addClass('featured');
				if ( action == 'unfeature' )
					$this_and_comment.removeClass('featured');
				if ( action == 'bury' )
					$this_and_comment.addClass('buried');
				if ( action == 'unbury' )
					$this_and_comment.removeClass('buried');
			}
		);
		return false;
	});
	
}

/**
 * allow this to be called multiple times
 */
jQuery(document).ready(function($){

	// init click handler
	featured_comments_click();
	
	/* Set classes on Edit Comments */
	$('.feature-comments.feature').each(function(){
		$this = $(this);
		$tr = $(this).parents('tr');
		if($this.hasClass('featured')) $tr.addClass('featured');
		if($this.hasClass('buried')) $tr.addClass('buried');
	});
	
});