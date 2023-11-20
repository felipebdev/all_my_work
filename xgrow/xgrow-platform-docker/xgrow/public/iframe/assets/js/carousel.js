$(document).ready(function(){
    getContentsFromTheFeatureOrder(1,3, setSliders);
});

const setSliders = contents => {
    if(contents){
        var i = 0;
        contents.map(content => {
            
            $(".carousel-indicators").append(`
                <li data-target="#carouselExampleIndicators" id="featured_${i}" data-slide-to=${i}></li>
            `);
            $(".carousel-inner").append(`
                <div class="carousel-item featured_article" id="featured_${i}"></div>
            `);
            i++;
        });
        $("#featured_0").addClass("active");
    }
    else{
        $('#main').append('Nenhum destaque inserido');
    }
}

