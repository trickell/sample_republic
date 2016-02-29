
$(function(){

  // checkout table
  var cotable = $('#co_table');

  // Button Animation
  $("#buttons .btn-block").click(function(e){
    e.preventDefault();
    $(".boxPanel").fadeOut(100);

    var title = $(this).prop("title");
    $('#'+title, "#contentForm").delay(200).fadeIn(300)
  });

  // When Check Out Gets Clicked
  $("form#form-checkout").submit(function(e){
    e.preventDefault();

    var loc = '',
        data = {
            'post':$(this).serialize(),
            'ajax':true,
            'method':'checkout'
        };

    console.log(data);
    $.post(loc, data)
      .success(function(res){
        var result = jQuery.parseJSON(res);
        if(result.error == false){
            alert(result.msg);
            cotable.find('tbody').append(result.row);
        } else {
            alert(result.msg);
        }
      });
  });

  // When Check In Gets Clicked
  $("form#form-checkin").submit(function(e){
    e.preventDefault();

    var loc = '',
        data = {
            'post':$(this).serialize(),
            'ajax':true,
            'method':'checkin'
        };

    console.log(data);
    $.post(loc, data)
      .success(function(res){
        var result = jQuery.parseJSON(res);
        if(result.error == false){
            alert(result.msg);

            // Removes from table and hides the book
            if(result.bid != undefined){
              $(this).find('input[name="book"]').find('option[value="'+result.bid+'"]').hide();
              cotable.find('tr#'+result.bid).hide();
            }
        } else {
            alert(result.msg);
        }
      });
  });

});
