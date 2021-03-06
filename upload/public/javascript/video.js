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

var video_element;

function video(title, ogg, mp4) {

    // Init
    video_element = $('#video');

    // Apply new changes
    $('#videoOGG').attr('src', ogg);
    $('#videoMP4').attr('src', mp4);
    $('#video #modal-title').html(title);

    video_element.load();
    video_element[0].play();
}