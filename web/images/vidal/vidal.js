$(document).ready(function(){
	$('#main_menu > li').mouseenter(function(){
		$('.sub_menu_item').hide(100);
		$('#sub'+$(this).attr('id')).slideDown(200);
	})

	$('#sub_menu').mouseleave(function(){
		$('.sub_menu_item').slideUp(100);
	})
	
	$('.texttip-up, .texttip-down').mouseenter(function(){
				$(this).prev().children('.tooltip').fadeIn(100);
			}).mouseleave(function(){
				$(this).prev().children('.tooltip').fadeOut(100);
			})

	$('#sr-list-left > a').mouseenter(function(){
				$('.sr-details[data-id='+$(this).data('id')+']').css('width', ($('#wrapper').width()-leftMapMargin-leftMapMargin-100)+'px');
				$('.sr-details[data-id='+$(this).data('id')+']').css('height', $('#wrapper').height());
				$('.sr-details[data-id='+$(this).data('id')+'] > img').attr('height', $('#wrapper').height());
				if($('.sr-details[data-id='+$(this).data('id')+'] > img').attr('width') < $('.sr-details[data-id='+$(this).data('id')+']').width()) {$('.sr-details[data-id='+$(this).data('id')+'] > img').attr('width', $('.sr-details[data-id='+$(this).data('id')+']').width());}
				$('.sr-details[data-id='+$(this).data('id')+']').css('visibility', 'visible');
				$('.sr-details[data-id='+$(this).data('id')+']').fadeIn(200);
			}).mouseleave(function(){
				$('.sr-details[data-id='+$(this).data('id')+']').fadeOut(200);
			})
});