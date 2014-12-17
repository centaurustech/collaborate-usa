$(document).ready(function() {

    $("#myTags").tagit({
        fieldName: 'voice_tags[]',
        tagLimit: 5,
        /*availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]*/

        autocomplete: {
            minLength: 3,
            delay: 300,
            search: function(event, ui){$('#load_x').css('display', 'inline-block');},
            source: function(request, response){

                $.ajax({
                    dataType: "json",
                    type : 'Get',
                    url: tags_url,
                    data:{term: request.term},

                }).always(function(){
                    $('.ui-autocomplete-input').removeClass('ui-autocomplete-loading');
                    $('#load_x').css('display', 'none');

                }).done(function(msg){
                    //console.log(msg);
                    if(typeof(msg['json'])!='undefined')
                    response(msg['json']);
                });
            }
        },
    });

});