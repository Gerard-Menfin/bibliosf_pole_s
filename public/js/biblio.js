$(function(){   // équivalent de window.addEventListener('load')
    $("a.reserver").on("click", function(event){
        event.preventDefault();
        $.ajax({
            url: event.target.href,
            dataType: "json",
            method: 'put',
            success: function(reponse) {
                console.log(reponse);
                $("#nbReservations").html(reponse.nb);
            }
        });
    });
})