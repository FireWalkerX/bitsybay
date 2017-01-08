/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (https://github.com/bitsybay)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

var audio_element;
var audio_track;
var audio_play_class;
var audio_stop_class;
var audio_status;
var audio_row;

function audio(row, ogg, mp3) {

    // Init
    audio_status     = $('.audio i');
    audio_element    = $('#audio');
    audio_track      = $('#audio' + row + ' i');
    
    audio_stop_class = 'glyphicon glyphicon-music';
    audio_play_class = 'glyphicon glyphicon-play';

    // Apply new changes
    $('#audioOGG').attr('src', ogg);
    $('#audioMP3').attr('src', mp3);

    if (audio_row != row) {
        audioStop();
    }

    if (audio_element[0].paused) {
        audioStart();
    } else {
        audioStop();
    }

    audio_row = row;
}

// Stop active audios
function audioStop() {
    audio_element[0].pause();
    audio_status.attr('class', audio_stop_class);
}

// Stop active audios
function audioStart() {
    // Reload
    audio_element.load();

    // Play current track
    audio_element[0].play();
    audio_track.attr('class', audio_play_class);

    // Stop on end
    audio_element.bind('ended', function(){
        audio_track.attr('class', audio_stop_class);
    });
}