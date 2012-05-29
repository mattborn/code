$(document).ready(function() {

/* ============================== Interactive =============================== */

    $(build());
    
    $('#interactive').hover(function(){ $(this).css('z-index',1000); }, function(){
    	$(this).css('z-index', 1);
    	$('#interactive .cities li a div').animate({opacity: 'hide', bottom: 0}, 250, 'easeOutExpo');
    });
    
    // Opens landmark popup.
    $('#landmark').click(function(e){
        e.preventDefault();
        if($('div',this).is(':hidden')){
            $('#interactive .cities li a div').animate({opacity: 'hide', bottom: 0}, 250, 'easeOutExpo');
            $('#interactive .industries li').removeClass('selected');
            $('#interactive .industries li a div').animate({opacity: 'hide', bottom: 25}, 250, 'easeOutExpo');
            reset();
            $('div',this).animate({opacity: 'show', bottom: 25}, 500, 'easeOutExpo');
        }
    });
    
    // Closes landmark popup.
    $('#landmark div').click(function(e){
        e.preventDefault();
        $(this).animate({opacity: 'hide', bottom: 0}, 500, 'easeOutExpo');
    });
    
    // Draws lane and opens city popup.
    $('#interactive .cities li a').live('click',function(e){
        e.preventDefault();
        if($('div',this).is(':hidden')){
            $('#landmark div').animate({opacity: 'hide', bottom: 0}, 500, 'easeOutExpo');
            $('#interactive .cities li a div').animate({opacity: 'hide', bottom: 0}, 250, 'easeOutExpo');
            $('#interactive .industries li').removeClass('selected');
            $('#interactive .industries li a div').animate({opacity: 'hide', bottom: 25}, 250, 'easeOutExpo');
            $('div',this).animate({opacity: 'show', bottom: 10}, 500, 'easeOutExpo');
            var i = $('#interactive .cities li a').index(this);
            lane([i]);
        }
    });
    
    // Closes city popup.
    $('#interactive .cities li a div').live('click',function(e){
        e.preventDefault();
        $(this).animate({opacity: 'hide', bottom: 0}, 250, 'easeOutExpo');
    });
    
    // Opens industry popup.
    $('#interactive .industries li a').live('click',function(e){
        e.preventDefault();
        if($('div',this).is(':hidden')){
            $('#interactive .industries li').removeClass('selected');
            $('#landmark div').animate({opacity: 'hide', bottom: 0}, 500, 'easeOutExpo');
            $('#interactive .cities li a div').animate({opacity: 'hide', bottom: 0}, 250, 'easeOutExpo');
            $(this).parent().addClass('selected');
            $('#interactive .industries li a div').animate({opacity: 'hide', bottom: 25}, 250, 'easeOutExpo');
            $('div',this).animate({opacity: 'show', bottom: 50}, 500, 'easeOutExpo');
        }
    });
    
    // Closes industry popup.
    $('#interactive .industries li a div').live('click',function(e){
        e.preventDefault();
        $('#interactive .industries li').removeClass('selected');
        $(this).animate({opacity: 'hide', bottom: 25}, 250, 'easeOutExpo');
    });
    
    // Prevents navigation title overlap.
/*
    $('#interactive .industries li').live('mouseenter',function(){
        $(this).prev('.selected').find('span').hide();
        $(this).next('.selected').find('span').hide();
    }).live('mouseleave',function(){
        $('#interactive .industries li span:hidden').removeAttr('style');
    });
*/

}); // End jQuery.ready()

/* "[City Name],[City x,y],[Lane width,height],[Lane background x,y],[Lane x,y]" */
var cities = [
    "Abu Dhabi,434,147,263,82,1,121,181,73",
    "Amsterdam,338,73,165,36,1,204,184,63",
    "Barcelona,322,108,149,42,167,204,184,75",
    "Beijing,548,99,372,68,1,246,184,38",
    "Brussels,334,86,161,31,265,121,184,68",
    "Buenos Aires,195,284,78,210,381,188,184,84",
    "Frankfurt,350,88,175,31,460,121,184,68",
    "Geneva,341,98,167,34,265,153,184,72",
    "Hong Kong,551,150,379,104,1,315,183,54",
    "Johannesburg,375,267,200,196,460,153,184,77",
    "London,314,82,142,30,661,129,184,71",
    "Madrid,307,109,134,26,661,160,182,90",
    "Manchester,309,70,136,35,661,187,182,64",
    "Melbourne,612,296,436,257,460,350,187,48",
    "Milan,354,103,180,41,661,253,184,70",
    "Moscow,395,69,220,54,661,295,184,44",
    "New Delhi,484,129,310,79,1,420,184,59",
    "Paris,321,93,148,29,661,223,184,72",
    "Sao Paulo,215,261,84,189,897,418,186,77",
    "Tokyo,601,120,431,91,1,500,179,36"
];

/* var queue = [11,0,8,19]; */

function clog(msg){if(window.console) console.log(msg);}

function build() {
    clog('Build initiated.');
    
    $('#destinations div').each(function(index){
        var c = cities[index].split(',');
        $('#interactive .cities').append('<li style="left: '+ c[1] +'px; top: '+ c[2] +'px;"><a href="#"></a></li>');
        $(this).appendTo($('#interactive .cities li a:eq('+ index +')'));
    });
    
    $('#interactive .cities li').each(function(index){
        var c = cities[index].split(',');
        $('#interactive .lanes').append('<li style="background-position: -'+ c[5] +'px -'+ c[6] +'px; left: '+ c[7] +'px; top: '+ c[8] +'px;"></li>')
    });
    
    $('#industries div').each(function(index){
        $('#interactive .industries').append('<li><a class="industry-'+ index +'" href="#"><span>'+ $('h3',this).html() +'</span></a></li>');
        $(this).appendTo($('#interactive .industries li a:eq('+ index +')'));
    });
    
    lane([11,0,8,19]);

}

var loop = false;

function lane(queue) {
    if(loop == true){
        if(queue.length == 1){
            loop = false;
            draw(queue[0]);
        } else {
            draw(queue[0]);
            queue.splice(0,1);
            lane(queue);
        }
    } else {
        $('#interactive .cities li a').css({backgroundPosition: 'right -325px'});
        reset();
        if(queue.length > 1){
            draw(queue[0]);
            queue.splice(0,1);
            loop = true;
            lane(queue);
        } else {
            draw(queue[0]);
        }
    }
}

function draw(index) {

   $('#interactive .cities li a:eq('+ index +')').css({backgroundPosition: 'right -310px'});
   var c = cities[index].split(',');
    if(index == 5 || index == 18){
        $('#interactive .lanes li:eq('+ index +')').show().animate({height: c[4]}, 1000, 'easeOutExpo');
    } else {
        $('#interactive .lanes li:eq('+ index +')').show().animate({width: c[3]}, 1000, 'easeOutExpo');
    }
    clog('Drawing lane '+ index +'.');

}

function reset() {
    $('#interactive .lanes li').each(function(index){
        $(this).hide();
        var c = cities[index].split(',');
        if(index == 5 || index == 18){
            $(this).css({width: c[3], height: 0});
        } else {
            $(this).css({width: 0, height: c[4]});
        }
    });
    clog('Lanes reset.');
}