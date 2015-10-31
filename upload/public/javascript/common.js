/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (http://bitsybay.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */


// Common
$(document).ready(function() {

    // Top search
    $('#topSearchButton').click(function () {
        if ($('#topSearchQuery').val()) {
            window.location.href = $('#topSearchAction').val() + $('#topSearchQuery').val();
        } else {
            $('#topSearchForm').addClass('has-error');
        }
    });

    $('#topSearchForm').keypress(function(e){
        if (e.which == 13) {
            if ($('#topSearchQuery').val()) {
                window.location.href = $('#topSearchAction').val() + $('#topSearchQuery').val();
            } else {
                $('#topSearchForm').addClass('has-error');
            }
        }
     });


    // Bottom Search
    $('#footerSearchForm button').click(function () {
        if ($('#footerSearchForm input[name=query]').val()) {
            window.location.href = $('#footerSearchForm input[name=action]').val() + $('#footerSearchForm input[name=query]').val();
        } else {
            $('#footerSearchForm').addClass('has-error');
        }
    });

    $('#footerSearchForm').keypress(function(e){
        if (e.which == 13) {
            if ($('#footerSearchForm input[name=query]').val()) {
                window.location.href = $('#footerSearchForm input[name=action]').val() + $('#footerSearchForm input[name=query]').val();
            } else {
                $('#footerSearchForm').addClass('has-error');
            }
        }
     });


    // Submitting the report form
    $('#reportSubmit').click(function () {

        $.ajax({
            url:  'ajax/report',
            type: 'POST',
            data: { product_id: $('#reportProductId').val(), message: $('#reportMessage').val() },
            beforeSend: function () {

                // Disable send button & add the timer icon
                $('#reportSubmit').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i>');
            },
            success: function (e) {
              if (e['status'] == 200) {

                  // Hide the form
                  $('#productReport .modal-footer, #reportMessage').addClass('hide');

                  // Response output
                  $('#productReport h4').html(e['title']);
                  $('#productReport .modal-body p').html(e['message']);

              } else {
                  alert('Connection error. Please, try again later.');
              }
            },
            error: function (e) {
              alert('Internal server error. Please, try again later.');
            }
        });
    });
});

function lengthFilter(val, limit) {
    var len = val.value.replace(/\,\s/g, ',').length;
    if (len >= limit) {
      val.value = val.value.substring(0, limit);
    }
};

// Product favorite
function favorite(product_id, user_is_logged) {
    if (!user_is_logged) {
        $('#loginForm').modal('toggle');
    } else {
        $.ajax({
            url:  'ajax/favorite',
            type: 'POST',
            data: {product_id : product_id},
            success: function (e) {
              if (e['status'] == 200) {
                if (e['code']) $('#productFavoriteButton' + product_id + ' .glyphicon').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                else $('#productFavoriteButton' + product_id + ' .glyphicon').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                if (e['total']) $('#productFavoriteButton' + product_id + ' span').html(e['total']);
                else $('#productFavoriteButton' + product_id + ' span').html('')
              }
            },
            error: function (e) {
              alert('Session expired! Please login or try again later.');
            }
        });
    }
}

// Init report form
function report(product_id, product_title) {

    // Set misconfiguration
    $('#reportProductId').val(product_id);
    $('#productReport h4').html(product_title);

    // Reset previews response
    $('#productReport .modal-body p').html('');
    $('#reportSubmit').removeClass('disabled');
    $('#reportSubmit i').remove();

    // Show navigation
    $('#productReport .modal-footer, #reportMessage').removeClass('hide').val('');
}

// Zoom image
function zoomImage(url, title) {
    $('#zoomImage .modal-header h4').html(title);
    $('#zoomImage .modal-body img').attr('src', url);
}

// Timer
function timer(sec, block, direction) {

    var time    = sec;
    direction   = direction || false;

    var hour    = parseInt(time / 3600);
    if ( hour < 1 ) hour = 0;
    time = parseInt(time - hour * 3600);
    if ( hour < 10 ) hour = '0'+hour;

    var minutes = parseInt(time / 60);
    if ( minutes < 1 ) minutes = 0;
    time = parseInt(time - minutes * 60);
    if ( minutes < 10 ) minutes = '0'+minutes;

    var seconds = time;
    if ( seconds < 10 ) seconds = '0'+seconds;

    //block.innerHTML = hour+':'+minutes+':'+seconds;
    block.innerHTML = minutes+':'+seconds;

    if ( direction ) {
        sec++;

        setTimeout(function(){ timer(sec, block, direction); }, 1000);
    } else {
        sec--;

        if ( sec > 0 ) {
            setTimeout(function(){ timer(sec, block, direction); }, 1000);
        } else {
            location.reload();
        }
    }
}

// Time tools
function seconds2time(secs) {

    var hr  = Math.floor(secs / 3600);
    var min = Math.floor((secs - (hr * 3600))/60);
    var sec = Math.floor(secs - (hr * 3600) -  (min * 60));

    if (min < 10) min = '0' + min;
    if (sec < 10) sec = '0' + sec;

    return min + ':' + sec;
}

// Locale
function setLanguage(old_code, new_code) {
    window.location = window.location.protocol + '//' + window.location.host + '/' + new_code + window.location.pathname.replace(old_code + '/', '');
}
