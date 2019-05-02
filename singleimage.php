<?php
include("includes/init.php");

//start session to save array of imgs in gallery that can be accessed in singleimage.php
session_start();

$image_list = $_SESSION["image_list"];
// var_dump($image_list);


//find image that corresponds to id
if (isset($_GET['id'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  $sql = "SELECT * FROM images WHERE id = :id;";
  $params = array(
    ':id' => $id
  );
  $records = exec_sql_query($db, $sql, $params)->fetchAll();
  $single_img = $records[0];
  $single_img_id_isset = True;
}
$single_img_id = $single_img["id"];

//function to process title from filename
function process_filename($single_img_filename){
  //replace - with space
  $single_img_filename = str_replace("-", " ", $single_img_filename);

  //capitalize first word
  $single_img_filename = ucwords($single_img_filename);

  return $single_img_filename;
}

//function to process title from filename input
function process_title($single_img_filename){
  //replace - with space
  $single_img_filename = str_replace(" ", "-", $single_img_filename);

  //capitalize first word
  $single_img_filename = strtolower($single_img_filename);

  return $single_img_filename;
}

//function to process description paragraph
function process_description($single_img_description){
  $single_img_description = ucfirst($single_img_description);
  return $single_img_description;
}

//function to return a string of tags
function print_single_img_tags($single_img_id){
  //get all tags for the single_img
  $sql = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id WHERE :img_id = image_id;";
  $params = array(
    ':img_id' => $single_img_id
  );
  $records_img_tags = exec_sql_query($db, $sql, $params)->fetchAll();

  //print tags
  $records_img_tags_length = count($records_img_tags);
  $count = 0;
  $tags_to_print = "";
  if ($records_img_tags_length != 0){ //only print tags if they exist for the image
    foreach ($records_img_tags as $record){
      if ($count == 0){
        $tags_to_print = "Tags: ";
      }
      if ($count != $records_img_tags_length - 1){
        $tags_to_print = $tags_to_print . ucfirst(htmlspecialchars($record["tag"])) . ' ' . '&#9830' . ' ';
      }
      else{
        $tags_to_print = $tags_to_print . ucfirst(htmlspecialchars($record["tag"]));
      }
      $count = $count + 1;
    }
  }
  return $tags_to_print;
}




//Call to process filename of single image
$single_img_filename = process_filename($single_img["filename"]);

//call to process description of single image
$single_img_description = process_description($single_img["description"]);

?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
<?php include("includes/header.php");?>
  <div id="singleimgblock">
    <div id="single_img_title"><?php echo '"' . htmlspecialchars($single_img_filename) . '"' ?></div>
    <?php
    if ($single_img_id != 17){
      echo '<img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/>';
    }

    else{
      echo '<div id="mustaches"><img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/></div>';
    }


    ?>
    <div id="single_img_details">
      <div id="single_img_descrip"><?php echo "Description: " . htmlspecialchars($single_img_description) ?></div>

      <div id="single_img_tags"><?php echo $tags_to_print ?></div>

    </div>
    <div id="return_gallery_link"><a href="gallery.php">Return to All Images</a></div>
  </div>

  <div id="slideshow_button_div">
    <?php echo '<a href="singleimage.php?'.http_build_query(array('id' => $single_img_id)).'"'?><</p>
    <p>></p>
  </div>

 <!-- if logged in, show edit single image form -->

  <?php include("includes/footer.php");?>
</body>
</html>
