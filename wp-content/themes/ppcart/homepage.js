if (typeof mts_ajax_loadposts !== 'undefined') {

var sort_links = document.querySelectorAll('.sort_list > li > a');
console.log(sort_links);
for (var i = 0; i < sort_links.length; i++) {
	sort_links[i].addEventListener('click', load_more_posts);
}
}

function load_more_posts() {
	var pageNum = parseInt(mts_ajax_loadposts.startPage, 10) + 1;
	var max = parseInt(mts_ajax_loadposts.maxPages, 10);
	var nextLink = mts_ajax_loadposts.nextLink;
	var autoLoad = mts_ajax_loadposts.autoLoad;
	var loadPostsLink = document.querySelector('#load-posts a');
	if (pageNum == max+1) {
		loadPostsLink.innerHTML = '<i class="fa fa-ban"></i>' + mts_ajax_loadposts.i18n_nomore;
		loadPostsLink.classList.add('disabled');
	}
	if(pageNum <= max && !loadPostsLink.classList.contains('loading')) {
		loadPostsLink.innerHTML = '<i class="fa fa-refresh fa-spin"></i>' + mts_ajax_loadposts.i18n_loading;
		loadPostsLink.classList.add('loading');
		if (mts_ajax_loadposts.portfolios == '1') {
			jQuery.get(nextLink, function(data) {
				var $items = jQuery(data).find('.portfolio-entry').css('opacity', 0);
				jQuery('#portfolio-grid-frame').append($items).waitForImages(function() {
					jQuery('#portfolio-grid-frame').isotope( 'appended', $items ).find('.portfolio-entry').css('opacity', 1);
					// Update page number and nextLink.
					pageNum++;
					var new_url = nextLink;
					nextLink = nextLink.replace(/(\/?)page(\/|d=)[0-9]+/, '$1page$2'+ pageNum);
					if(pageNum <= max) {
						loadPostsLink.innerHTML = '<i class="fa fa-refresh"></i>'+mts_ajax_loadposts.i18n_loadmore;
						loadPostsLink.classList.remove('loading');
					} else {
						loadPostsLink.innerHTML = '<i class="fa fa-ban"></i>'+mts_ajax_loadposts.i18n_nomore;
						loadPostsLink.classList.add('disabled');
						loadPostsLink.classList.remove('loading');
					}
				});
			});
		}
	}
}