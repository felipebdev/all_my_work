$(document).ready(function(){

    getSectionConfig(
        function(config){
            console.log(config);
            const {qtd_per_page} = config.sectionConfig;
            $("#articles").paginate({'perPage':qtd_per_page});
        },
        function(response){
            console.log(response);
        }
    );

});


