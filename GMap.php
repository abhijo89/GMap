<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Project:     GoogleMapAPI V3: a CI library inteface to the Google Map API v3
 * File:        GMap.php
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * @copyright 2010 Mahmoud Zarroug
 * @author Mahmoud Zarroug <gnu.maxo@gmail.com>
 * @package GoogleMapAPI (version 3)
 * @version 3.0
*/
/*
For database caching, you will want to use this schema:

CREATE TABLE geocode_cache (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  lng double NOT NULL,
  lat double NOT NULL,
  query varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
);

*/

/**
 * PHP Google Maps API class
 * @package GoogleMapAPI
 * @version 3.0
 */
class Gmap {
	// detect user location
	var $_sensor = false;
	
	// list of marker listeners
	var $_listener = array();
	
	// position of the map
	var $_langlat = null;

	/*
	 * optinal constructor
	 * 
	 * @param: $langlat: 		map position
	 */
	function initialize($langlat='0') {
		if($langlat) {
			$_langlat = $langlat;
		}
	}

	/*
	 * add listener to the map
	 * 
	 * @param: $item: 		map or marker
	 * 		   $action:		click, drag, mouseover, .. , etc.
	 * 	       $script:		JS script for the listener.
	 * 
	 */
	function add_listener($item, $action="click", $script) {
		$this->_listener[] = 'google.maps.event.addListener('.$item.', \''.$action.'\', function() {'.$script.'});';
	}

	function get_map($latlang="0,0", $sensor='false', $draggable_marker='false', $marker_title='') {
		$output='<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor='.$sensor.'">
        </script>
        <script type="text/javascript">
            var map;
            var marker = null;
            var markersArray = [];
            var initialLocation;
            var infowindow;
            var siberia = new google.maps.LatLng(24.689129568192794, 46.720268225000005);
            var browserSupportFlag =  new Boolean();
            
            function initialize() {
                var latlng = new google.maps.LatLng('.$latlang.');
                var myOptions = {
                    zoom: 8,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.TERRAIN
                };
                map = new google.maps.Map(document.getElementById("map_canvas"),
                myOptions);
                setMarker(latlng);
                ';
				if($draggable_marker!='false') {
	                $output.='google.maps.event.addListener(map, \'click\', function(event) {
	                   setMarker(event.latLng);
	                });';
				}
                
				
					foreach($this->_listener as $listen) {
						$output.=$listen.'
				';
					}
                $output .='
                // Try W3C Geolocation (Preferred)
                if('.$sensor.') {
	                if(navigator.geolocation) {
					    browserSupportFlag = true;
					    navigator.geolocation.getCurrentPosition(function(position) {
					      initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					      map.setCenter(initialLocation);
					      setMarker(initialLocation);
					    }, function() {
					      handleNoGeolocation(browserSupportFlag);
					    });
					  // Try Google Gears Geolocation
					  } else if (google.gears) {
					    browserSupportFlag = true;
					    var geo = google.gears.factory.create(\'beta.geolocation\');
					    geo.getCurrentPosition(function(position) {
					      initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
					      map.setCenter(initialLocation);
					      setMarker(initialLocation);
					    }, function() {
					      handleNoGeoLocation(browserSupportFlag);
					    });
					  //Browser doesn\'t support Geolocation
					  } else {
					    browserSupportFlag = false;
					    handleNoGeolocation(browserSupportFlag);
					  }
					  function handleNoGeolocation(errorFlag) {
					    if (errorFlag == true) {
					      alert("Geolocation service failed.");
					      initialLocation = siberia;
					    } else {
					      alert("Your browser doesn\'t support geolocation. We\'ve placed you in Riyadh.");
					      initialLocation = siberia;
					    }
					    map.setCenter(initialLocation);
					    setMarker(initialLocation);
					  }
                }
			}
            function setMarker(location) {
                if(marker != null)
                {
                    marker.setMap(null);
                }
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: ';
                if($draggable_marker=='true')
                	$output.='true';
                else
                	$output.='false';
                
                $output.=',
                    title: "'.$marker_title.'"

                });
                marker.setMap(map);
            }

            function addMarker(location, info) {
              var mark = new google.maps.Marker({
                position: location,
                map: map
              });
              if(info!=null) {
                google.maps.event.addListener(mark, \'click\', function() {
                    infowindow = new google.maps.InfoWindow({
                        content: info
                    });
                    infowindow.open(map,mark);});
              }
              markersArray.push(mark);
              mark.setMap(map);
            }

			';

        $output.='</script>';
        return $output;
	}
}

?>
