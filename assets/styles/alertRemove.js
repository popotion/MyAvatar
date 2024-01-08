window.setTimeout(alertRemoveFadeOut, 3000);
alertRemoveClick();

function alertRemoveFadeOut()
{
    var alertList = document.getElementsByClassName('alert');

    if(alertList != null){
        for(let i=0; i<alertList.length; i++){
            var alertElement = alertList[i];
            var alertStyle = alertElement.style;

            alertStyle.opacity = 1;
            (function fade(){(alertStyle.opacity-=.1)<0?alertStyle.display="none":setTimeout(fade,60)})();
        }
    }
}

function alertRemoveClick()
{
    var alertList = document.getElementsByClassName('alert');

    Array.from(alertList).forEach(function(element) {
        element.addEventListener('click', function() {
            var alertStyle = element.style;

            alertStyle.opacity = 1;
            (function fade(){(alertStyle.opacity-=.1)<0?alertStyle.display="none":setTimeout(fade,60)})();
        });
    });
}
