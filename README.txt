HOW TO USE:
first add the GMap.php file to your "application/libraries" folder in CodeIgniter
// Controller
  $this->load->library('GMap');
  $location = '1.234, 1.234';
  $location_sensor_flag = 'false'; // or 'true', this should be a string
  $draggable_marker_flag = 'true'; // or 'false', this should be a string
  $this->gmap->add_listener('map', $action='click', '// JS script to be executed after'); // check Google Maps API to see all avalable actions
  $data['map'] = $this->gmap->get_map($location, $location_sensor_flag, $draggable_marker_flag, "Marker Title");

// View
1- print the $map variable in the HTML header. it outputs <script> tags.
2- make sure your HTML <body> looks like this:
   <body onload="initialize()">
3- put this DIV in the place you want the map:
   <div id="map_canvas" style="width:100%; height:400px"></div>

you are done.