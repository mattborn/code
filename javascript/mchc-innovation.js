(function(window,undefined){

	var History = window.History,
		$ = window.jQuery;
	
	if (!History.enabled){ return false; }
	
	// Add easeOutExpo easing
	$.extend($.easing, { def: 'easeOutExpo',
		easeOutExpo: function(x, t, b, c, d){
			return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
		}
	});
	
	MCHC = {
	
		// Fires once on initial load
		init: function(){
		
			MCHC.util();
			
			// Clone header elements
			$('#header h1, #header h2, #header h3, #header .utility').clone().appendTo('#cover');
			$('#header .share').clone().appendTo('#footer');
			
			// Create navigation buttons
			$('<a />', {'href': '#', 'id': 'back-to-top', 'text': 'Back to Top'}).appendTo('body');
			$('<a />', {'href': '#', 'id': 'prev', 'text': 'Prev'}).appendTo('body');
			$('<a />', {'href': '#', 'id': 'next', 'text': 'Next'}).appendTo('body');
			
			// Create nav dropdown from cover nav
			$('<select />', {'id': 'nav'}).appendTo('body');
			$('<option />', {'selected': 'selected', 'value': 'cover', 'text': 'Cover'}).appendTo('#nav');
			$('#cover .nav a').each(function(){
				var item = $(this);
				$('<option />', {'value': item.attr('href'), 'text': item.text()}).appendTo('#nav');
			});
		
		},
		
		util: function(){
		
			if ($(window).scrollTop() < 300 && $(window).width() > 480){
				$('body').addClass('top');
			} else {
				$('body').removeAttr('class');
			}
			
			if ($(window).width() > 990){
				$('#nav').css('bottom','').css('top',10);
			} else {
				$('#nav').css('top','').css('bottom',10);
			}
			
			if (MCHC.loaded){
				if ($('#appendices').offset().top !== ($('#arrows li.appendices').offset().top-75)){
					console.log('fire');
					$('.section').each(function(){
						id = $(this).attr('id');
						$('#arrows li[class='+id+']').css('top', $(this).offset().top+75);
					});
				}
			}
		
		},
		
		loaded: false,
		
		current: 'cover',
		
		update: function(section, trigger){
		
			console.log('current: '+MCHC.current+', new:'+section);
			
			if (MCHC.current !== section){
				
				MCHC.current = section;
				$('#nav').val('/'+section);
				
				if (trigger) {
					History.pushState(null, null, '/'+section);
				}
			
			}
		
		},
		
		scroll: function(){
		
			if (!$('html, body').is(':animated')){
			
				if ($(window).scrollTop() < $('#letter').offset().top && MCHC.current !== 'cover'){
					MCHC.update('cover');
				} else {
					$('.section').each(function(){
						
						var next = $(this).next('.section').offset(),
							nextOffset = (next) ? next.top : $(window.document).height();
						
						if ($(this).offset().top <= $(window).scrollTop() && $(window).scrollTop() < nextOffset && MCHC.current !== $(this).attr('id')){
							MCHC.update($(this).attr('id'));
						}
					});
				}
			
			}
		
		}
	
	}
	
	$(function(){
	
		MCHC.init();
		
		var rootUrl = History.getRootUrl();
		
		$(window).bind('statechange', function(){
		
			var State = History.getState(),
				url = State.url,
				relativeUrl = url.replace(rootUrl, '');
			
			if (relativeUrl == ''){ relativeUrl = 'cover' }
			
			$('html, body').stop(true).animate({scrollTop: $('#'+relativeUrl).offset().top}, 1000, 'easeOutExpo');
			
			if (MCHC.current !== relativeUrl){
				MCHC.update(relativeUrl);
			}
		
		});
		
		History.Adapter.trigger(window, 'statechange');
		
		$('#cover .nav a').click(function(e){
			e.preventDefault();
			History.pushState(null, $(this).text(), this.href);
			var target = '/'+$(this).parent().attr('class');
			$('#nav').val(target);
		});
		
		$('#nav').change(function(){
			History.pushState(null, $(this).find('option:selected').text(), $(this).find('option:selected').val());
		});
		
		$('#prev').click(function(e){
			e.preventDefault();
			var prev = $('#'+MCHC.current).prev('.section').attr('id');
			if (prev !== undefined){
				MCHC.update(prev, true);
			} else if (MCHC.current !== 'cover'){
				MCHC.update('cover', true);
			}
		});
		
		$('#next').click(function(e){
			e.preventDefault();
			var next = $('#'+MCHC.current).next('.section').attr('id');
			if (MCHC.current == 'cover'){
				MCHC.update('letter', true);
			} else if (next !== undefined){
				MCHC.update(next, true);
			}
		});
		
		$('#back-to-top').click(function(e){
			e.preventDefault();
			MCHC.update('cover', true);
		});
		
		$('.share').click(function(e){
			e.preventDefault();
			$('#share').show();
		});
		
		$('#share .close').click(function(e){
			e.preventDefault();
			$('#share').hide();
		});
		
		$(window).load(function(){
			// Create side arrows MOVE TO MCHC ARROWS + FIRE ON LOAD/RESIZE use index selector offset top on sections
			$('<ol />', {'id': 'arrows'}).appendTo('body');
			$('.section').each(function(){
				$('<li />', {'class': $(this).attr('id')}).css('top', $(this).offset().top+75).appendTo($('#arrows'));
			});
			MCHC.loaded = true;
		});
		
		$(window).resize(function(){
			MCHC.util();
		});
		
		$(window).scroll(function(){
			MCHC.util();
			MCHC.scroll();
		});
	
	});

})(window);