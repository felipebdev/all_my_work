$(document).ready(
    function (){
        $('.has-arrow').click(
            function (){
                 $(".left-sidebar").animate({ scrollTop: $(".left-sidebar").height() }, 2000);
            }
        )
    }
)