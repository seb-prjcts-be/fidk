<?php

$tags = get_meta_tags('https://vimeo.com/194264590');

 foreach($tags as $key => $val) 
 {
     if( is_array( $key ) ) {
         foreach( $key as $thing) {
         }
     } else {
		  ${$key}=$val;
			 echo $key."=".${$key};
			 echo "<br>";
     }
 }
 
 echo "<br>Description: ".$tags["description"];
 echo "<br>Keywords: ".$tags["keywords"];
// $t="L'amoureuse";
// echo str_replace("'","&#39;",$t);

// $t="SebShinesOn1012";
// echo md5($t);


// if (mysqli_num_rows($result) > 0) {
  //output data of each row
  // while($row = mysqli_fetch_assoc($result)) 
  // {
    // echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
  // }
// } else {
  // echo "0 results";
// }
?>
