var pane = { current : 0 }
var search = false;

$(document).ready(function(){
    
    /* $.ajaxSetup({cache:false}); */
    
    $(build());
    $(move());
    $('nav#primary ul li a:eq(0)').removeClass('selected');
    
    $('#prev').click(function(e){
        e.preventDefault();
        if ( pane.current > 0 ) {
            pane.current--;
        } else {
            pane.current = ($('.pane').length - 1);
        }
        move();
    });
    
    $('#next').click(function(e){
        e.preventDefault();
        if ( pane.current < ($('.pane').length - 1) ) {
            pane.current++;
        } else {
            pane.current = 0;
        }
        move();
    });
    
    $('nav#primary ul li a').click(function(e,s){
        e.preventDefault();
        pane.current = $('nav#primary ul li a').index(this);
        if ( $('#splash').css('opacity') > 0 ) {
            $('#splash').fadeTo(1000,0,function(){
                $(this).remove();
            });
        }
        if ( $('#search').css('opacity') > 0 ) {
            $('#search').fadeTo(500,0,function(){
                $(this).hide();
            });
        }
        move();
    });
    
    $(window).resize(function(){build();});
    
    // Tooltips.
    $('nav#primary ul li a').hover(function(){
        i = $('nav#primary ul li a').index(this);
        $(this).append('<div class="tool">'+ $('.pane:eq('+ i +') article header h1').html() +'<div class="tip"></div></div><!-- .tooltip -->');
        $('.tool', this).animate({'bottom':100,'opacity':1},250,'easeOutExpo');
    }, function(){
        $('.tool', this).animate({'bottom':120,'opacity':0},250,'easeOutExpo',function(){
            $(this).remove();
        });
    });
    
    // Slide toggle.
    $('.toggle').live('click',function(){
        $(this).toggleClass('open');
        $(this).next().slideToggle(750,'easeOutExpo');
    });
    
    // Search.
    $('.search').submit(function(){
        $('#search').fadeTo(250,1);
        $('article').removeHighlight();
        search = $('input.text').val();
        $.post(template +'/ajax-search.php', { s: $('input.text',this).val() }, function(data){
            $('#search .results').html(data);
        });
        return false;
    });
    
    $('#search .results ol li a').live('click',function(e){
        e.preventDefault();
        $('ul li a[href*="'+ $(this).attr('href') +'"]').trigger('click');
    });

});

function build(){
    $('#splash').width($(window).width()).height($(document).height());
    $('#search').width($(window).width()).height($(document).height());
    var paneWidth = $('.pane').width() + parseInt($('.pane').css('marginRight'));
    $('#move').width($('.pane').length * paneWidth);
}

function move(){
    var w = $('.pane').width() + parseInt($('.pane').css('marginRight'));
    var l = -w * pane.current;
    $('nav#primary ul li a').removeClass('selected');
    $('nav#primary ul li:eq('+ pane.current +') a').addClass('selected');
    $('#move').animate({ 'left': l }, 750, 'easeInOutExpo', function(){
        $('html, body').animate({scrollTop:0}, 500, 'easeOutExpo');
    });
    var selected = '#move div.pane:eq('+ pane.current +') article .hidden';
    if ( ! $(selected).hasClass('loaded') ) {
        $('#loading').show();
        $.post(template +'/ajax.php', { name: $('li a:eq('+ pane.current +')').attr('href') }, function(data){
            $(selected).append(data).addClass('loaded');
            searchHighlight();
            fit(selected);
            $(selected).fadeTo(1000,1,function(){
                if (jQuery.browser.msie)
                this.style.removeAttribute("filter");
            });
            $('#loading').fadeOut(250);
        });
    } else {
        searchHighlight();
        adjust(selected);
    }
}

function searchHighlight(){
    if ( search !== false ) {
        $('article').highlight(search);
        search = false;
    }
}

function fit(selected){
    $('#main').height($('#move').height());
    $(selected).attr('height', $('#move').height());
    $(selected).find('.toggle').addClass('open');
    $(selected).find('.toggle').not(':eq(0)').trigger('click');
}

function adjust(selected){
    $('#main').height($(selected).attr('height'));
}