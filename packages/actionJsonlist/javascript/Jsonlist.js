/* javascript to be included on the view */

function showPost(id){

    var elements = document.getElementsByClassName('article_image')
    var scrollDuration = 100;

        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'none';
        }

    var elements = document.getElementsByClassName('titlebar')

    for (var i = 0; i < elements.length; i++){
        elements[i].style.display = 'none';
    }

    document.getElementById('article_' + id).style.display = "block";
    document.getElementById('back').style.display = "block";

    var scrollStep = -window.scrollY / (scrollDuration / 15),
        scrollInterval = setInterval(function(){
            if ( window.scrollY != 0 ) {
                window.scrollBy( 0, scrollStep );
            }
            else clearInterval(scrollInterval);
        },15);


}

function backToList(){
    var elements = document.getElementsByClassName('article_image')
    var scrollDuration = 100;

    for (var i = 0; i < elements.length; i++){
        elements[i].style.display = 'block';
    }

    var elements = document.getElementsByClassName('titlebar')

    for (var i = 0; i < elements.length; i++){
        elements[i].style.display = 'block';
    }

    var elements = document.getElementsByClassName('articlecontent')

    for (var i = 0; i < elements.length; i++){
        elements[i].style.display = 'none';
    }

    document.getElementById('back').style.display = "none";

    var scrollStep = -window.scrollY / (scrollDuration / 15),
        scrollInterval = setInterval(function(){
            if ( window.scrollY != 0 ) {
                window.scrollBy( 0, scrollStep );
            }
            else clearInterval(scrollInterval);
        },15);


}