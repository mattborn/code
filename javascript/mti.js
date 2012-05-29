(function($){

	// Extend easing options.
	$.extend( $.easing, { def: 'easeOutExpo',
		easeOutExpo: function (x, t, b, c, d) {
			return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
		},
		easeInOutExpo: function (x, t, b, c, d) {
			if (t==0) return b;
			if (t==d) return b+c;
			if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
			return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
		}
	});
	
	CORE = {
		init : function(){
			CORE.w = $(window).width();
			CORE.h = $(window).height();
		}
	}
	
	FULL = {
		init : function(){
			CORE.init();
			FULL.size();
		},
		current : 0,
		offset : 0,
		size : function(){
			CORE.init();
			$('#full .gallery img').not(':eq('+FULL.current+')').css({left:CORE.w});
			$('#full').width(CORE.w).height(CORE.h);
			if ( CORE.w / CORE.h < 1.6 ) {
				$('#full .gallery img').css({width:'auto'}).height(CORE.h);
				if ( $('#full .gallery img:eq('+FULL.current+')').width() > CORE.w ) {
					FULL.offset = ($('#full .gallery img:eq('+FULL.current+')').width() - CORE.w) / 2;
					$('#full .gallery img:eq('+FULL.current+')').css({left:-FULL.offset});
				}
			} else { $('#full .gallery img').css({width:'100%',height:'auto'}); }
		},
		slide : function(index){
			$('#controls li').removeClass('active');
			$('#controls li:eq('+index+')').addClass('active');
			$('#full .gallery img:eq('+index+')').css({zIndex:2}).stop(true).animate({left:-FULL.offset},1000,'easeOutExpo',function(){
				$(this).css({zIndex:1});
				$('#full .gallery img').not(':eq('+index+')').css({left:CORE.w});
			});
		},
		interval : setInterval(function(){
			if ( FULL.current < $('#full .gallery img').length - 1 ) { FULL.current++; }
			else { FULL.current = 0; }
			FULL.slide(FULL.current);
		},5000)
	}
	
	MTI = {
		init : function(){
			this.build();
			$('#load').animate({opacity:0},500,function(){
				$(this).hide();
				$('header.ui').animate({opacity:1,top:68},750,'easeInOutExpo',function(){
					$('#social').animate({opacity:1,bottom:50},750,'easeInOutExpo');
				});
			});
		},
		build : function(){
			var screenWidth = $(window).width();
			var screenHeight = $(window).height();
			
			var pageWidth = $('header.ui .align').width();
			var wingWidth = Math.ceil((screenWidth - pageWidth) / 2);
			$('.west').css('left',-wingWidth+10);
			$('.east').css('left',pageWidth+10);
			if ( screenWidth < 1280 ) { $('.wing').hide(); }
			else { $('.wing').show().css('width',wingWidth); }
			
			var ajaxMax = screenHeight - 160;
			var mainMax = ajaxMax - 40;
			var sideWidth = ( $('#ajax').hasClass('tax-types') ) ? 320 : 360;
			var mainWidth = pageWidth - parseInt($('#main').css('marginLeft')) - sideWidth;
			$('#ajax, #side, #project').css({maxHeight:ajaxMax});
			$('#main').css({width:mainWidth,maxHeight:mainMax});
			
			if ( $('.gallery').length > 1 ) { $('#full').empty(); }
			var backgrounds = $('.gallery').detach();
			$('#full').html(backgrounds);
			if ( $('#full .gallery img').length > 0 ) {
				$('body').css('background','#111');
			} else { $('body').css('background','#111 url(/wp-content/uploads/01-PRACEGPD-01.jpg) no-repeat top'); }
		}
	}
	
	$(document).ready(function(){
	
		if ( location.pathname == '/' || location.pathname == '/wp-login.php' || location.pathname.indexOf( '#!' ) > 0 ) { $(window).hashchange(); MTI.init(); }
		
		else { window.location = location.protocol+'//'+location.host+'/#!'+location.pathname; }
		
		console.log(location.pathname.indexOf( '#!' ) > 0);
		
/*
		$('#navigation li a').hover(function(){
			$(this).next('.sub-menu').hide().slideDown();
		},function(){
			$(this).next('.sub-menu').slideUp();
		});
*/
		
		$('#ajax').addClass('group');
		
		$('#related h2 a').live('click',function(e){
			e.preventDefault();
			$(this).parent().siblings('.toggle').slideToggle();
		});
		
		$('.project-title a, .project-info').live('click',function(e){
			e.preventDefault();
			$(this).parents('header').siblings('.toggle').slideToggle();
		});
		
		$('#navigation a, #side a, .archive a, #type a, #related li a, article a').live('click',function(e){
			e.preventDefault();
			var hash = this.href.replace(location.protocol+'//'+location.host+'/','');
			if ( $(this).hasClass('print') ) { window.print(); }
			if ( hash !== '#' ) {
				if ( $(this).hasClass('external') || $(this).parent().hasClass('external') ) { window.open(this.href); }
				else { setHash(hash); }
			}
		});
		
		$('#social a').click(function(e){
			e.preventDefault();
			window.open(this.href);
		});
		
		$('.search').submit(function(){
			search = $('input').val();
			setHash('?s='+search);
			return false;
		});
		
		// Initiate primary function.
		FULL.init();
		
	});
	
	// Recalculate once images are loaded.
	$(window).load(function(){FULL.size();});
	
	// Recalculate on browser resize.
	$(window).resize(function(){ MTI.build(); FULL.size(); });
	
	$(window).hashchange(function(){
		var hash = getHash();
		$('body').css('overflow','hidden');
		$('#load').show().animate({opacity:.5},250);
		$('#content').stop(true).animate({opacity:0,bottom:-500},250,'easeInOutExpo',function(){
			$(this).loadShiv(location.protocol+'//'+location.host+'/'+hash+' #ajax',function(response,status,xhr){
				MTI.build();
				$(this).stop(true).animate({opacity:1,bottom:40},750,'easeInOutExpo',function(){
					$('#page').removeAttr('style');
				});
				$('#load').animate({opacity:0},500,function(){ $(this).hide(); });
			});
		});
		//console.log(location.protocol+'//'+location.host+'/'+hash+' #ajax');
		$('#full').empty();
		setTimeout(function(){
			$('#controls').empty();
			$('#full .gallery img').each(function(index){
				$('#controls').append('<li class="item-'+index+'"><a href="#"></a></li>');
			});
			$('#controls li a').live('click',function(e){
				e.preventDefault();
				clearInterval(FULL.interval);
				FULL.current = $('#controls li a').index(this);
				FULL.slide(FULL.current);
			});
			$('#controls li:eq('+FULL.current+')').addClass('active');
		},3000)
		var sizesize = setInterval(function(){FULL.size();},500);
	});

})(jQuery);

function getHash(){ return window.location.hash.substring(3); }
function setHash(str){ window.location.hash = '!/' + str; }