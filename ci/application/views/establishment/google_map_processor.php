<?php
    if (!empty($establishment_id) && !empty($address))
    {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAUkbxy1rBZ7YdIl2C-ckxvBRDXtlp_hZRoOI_0qCoAppPOPmpYBQOjy3rCPVQVc0rb3qBBtMKhm25Bw"></script>
</head>
<body>

<div id="container">

</div>

<script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': '<?php echo $address; ?>'}, function(result, status)
    {
        var lat = false;
        var lng = false;
        
        if (status == 'OK')
        {
            lat = result[0].geometry.location.lat();
            lng = result[0].geometry.location.lng();  
        }
        
        $.ajax({
            url: 'http://www.twobeerdudes.com/establishment/ajax_google_map_lat_lng',
            cache: false,
            type: 'POST',
            data: 
            {
                id: <?php echo $establishment_id; ?>, 
                latitude: lat,
                longitude: lng
            }
        });
    });
    
    
</script>

</body>
</html>
<?php
    }
?>