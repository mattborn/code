$.noConflict();
jQuery(document).ready(function($){

	var currentTime = new Date()
 	var hours = 20;
 	//var hours = currentTime.getHours()
  	var minutes = currentTime.getMinutes()
  	
 	var suffix = "AM";
  	if (hours >= 12) {
  		suffix = "PM";
  	}
  	
  	if (minutes < 10){
 	 	minutes = "0" + minutes
	}
	
	if(hours>=6 && hours<10){
		$('body').addClass('dark');
		$('body').addClass('coffee');
	}else if(hours>=10 && hours <=19){
		$('body').addClass('light');
	}else {
		$('body').addClass('dark');
	}
	
    // External links
    $('a[rel*=external]').click(function(){
        window.open(this.href);
        return false;
    });
    
    // Featured slider
/*
    $('#featured .slides').cycle({
        fx: 'scrollHorz',
        easing: 'easeOutExpo',
        timeout: 5000,
        pause: 1,
        pager: '#featured .controls',
        pagerAnchorBuilder: function(idx, slide) {
            return '<li><a href="#" style="background: #'+ slide.href +'">Test '+ idx +'</a></li>';
        }
    });
*/


/* Clear 'dem Fields by Matthew Born */
$('input[type=text]').focus(function(){
  if(!$(this).attr('placeholder')){
    if(!$(this).val()){
      $(this).attr('placeholder', ' ');
      $(this).val('').addClass('active');
    } else {
      $(this).attr('placeholder', $(this).val());
      $(this).val('').addClass('active');
    }
  } else if($(this).val() === $(this).attr('placeholder')){
    $(this).val('').addClass('active');
  }
}).blur(function(){
  if(!$(this).val()){
    if($(this).attr('placeholder') === ' '){
      $(this).val('');
    } else {
      $(this).val($(this).attr('placeholder'));
    } $(this).removeAttr('placeholder').removeClass('active')
  }
});
    
    // Select filter
    $('#filter select').change(function(){
        option = $(this).val();
        $('#main').load(template_url +'/zack.html', function(){
            alert(template_url +'/loop.php?filter='+ option);
        });
    });
    
    // Featured slider
    $(window).bind("load", function() {
        $('div#home-slideshow').slideViewerPro({
    		thumbs: 8, 
    		autoslide: true, 
    		asTimer: 3500, 
    		typo: false,
    		thumbsPercentReduction: 10.5, 
    		galBorderWidth: 0,
    		thumbsBorderOpacity: 0, 
    		buttonsTextColor: "#707070",
    		buttonsWidth: 20,
    		thumbsRightMargin: 10, 
    		thumbsTopMargin: 0,
    		typoFullOpacity: 1, 
    		thumbsActiveBorderOpacity: 0.9,
    		thumbsActiveBorderColor: "#559fd4",
    		shuffle: false,
    		leftButtonInner: "<img src='/wp-content/themes/peaty5/images/larw.gif' />",
    		rightButtonInner: "<img src='/wp-content/themes/peaty5/images/rarw.gif' />"
    	});
    });
    
    // Solutions slider
	$('div#solutions ul.controls li a').click(function(e){
        e.preventDefault();
        $('div#solutions ul.controls li a').removeAttr('class');
        $(this).addClass('active');
        var i = $('div#solutions ul.controls li a').index(this);
        if ( i == 4 ) {
            if ( $('div#solutions div.mask').height() > 300 ) {
                $('div#solutions div.mask').animate({
                    height: '300'
                }, 500, 'easeOutExpo');
            } else {
                $('div#solutions div.mask div.slides').animate({
                    top: '0'
                }, 500, 'easeOutExpo');
                $('div#solutions div.mask').animate({
                    height: '1260'
                }, 500, 'easeOutExpo');
            }
        } else {
            if ( $('div#solutions div.mask').height() > 300 ) {
                $('div#solutions div.mask').animate({
                    height: '300'
                }, 500, 'easeOutExpo');
            }
            var y = '-'+i*320;
            $('div#solutions div.mask div.slides').animate({
                top: y
            }, 500, 'easeOutExpo');
        }
	});
	
	// Work filter
	$('div#filter a.toggle').click(function(e){
        e.preventDefault();
        $('div#filter ul').slideToggle(500, 'easeOutExpo');
	    $(this).toggleClass('active');
	});
	
	$('div#filter ul').css({ opacity: 0.9 });
	
	// Work thumb hover
	$('li.project').hover(function(){
        $(this).css('background', '#333');
	}, function(){
        $(this).css('background', '#272626');
	});
	
	// Tooltip
	$('div.tooltip a').hover(function(){
        var tip = $(this).attr('title');
        $(this).after('<div class="tooltip-active">'+ tip +'<div class="tooltip-nub"></div></div>');
        $(this).siblings('.tooltip-active').animate({
            opacity: '1',
            top: '-45'
        }, 500, 'easeOutExpo');
	}, function(){
        $(this).siblings('.tooltip-active').animate({
            top: '-45',
            opacity: '.1'
        }, 500, 'easeOutExpo');
        $(this).siblings('.tooltip-active').remove();
	});
	
	// Elevator slider
	$('div#elevator ul.slides li').each(function(index){
        $('div#elevator div.controls ul').append('<li><a href="#"></a></li>');
	});
	
	$('div#elevator div.controls ul li:first').addClass('active');
	
	var m = parseInt($('div#elevator ul.slides li').css('margin-bottom'), 10);
    var h = $('div#elevator div.mask').height() + m;
	
	$('div#elevator div.controls ul li a').click(function(e){
        e.preventDefault();
        $('div#elevator div.controls ul li').removeAttr('class');
        $(this).parent().addClass('active');
        var i = $('div#elevator div.controls ul li a').index(this);
        var y = '-'+i*h;
        /* alert('i: '+ i +', m: '+ m +', h: '+ h +', y: '+ y); */
        if ( $('div#elevator div.mask').height() > h ){
            $('div#elevator div.mask').animate({height: h-m}, 500, 'easeOutExpo');
        }
        $('div#elevator ul.slides').animate({top: y}, 500, 'easeOutExpo');
	});
	
	$('div#elevator a.expand').click(function(e){
        e.preventDefault();
        var n = $('div#elevator ul.slides li').length;
        if ( $('div#elevator div.mask').height() > h ){
            $('em', this).html('Expand');
            $('div#elevator div.controls ul li').removeAttr('class');
            $('div#elevator div.controls ul li:first').addClass('active');
            $('div#elevator div.mask').animate({height: h-m}, 500, 'easeOutExpo');
        } else {
            $('em', this).html('Retract');
            $('div#elevator div.controls ul li').addClass('active');
            $('div#elevator ul.slides').animate({top: '0'}, 500, 'easeOutExpo');
            $('div#elevator div.mask').animate({height: h*n-m}, 500, 'easeOutExpo');
        }
	    $(this).toggleClass('active');
	});
	
	
    $(function() {
        $('.gallery a').lightBox();
    });

});