<?php
include("includes/init.php");

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


//function to process description paragraph


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

?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
  <?php include("includes/header.php");?>
  <div id="singleimgblock">
    <a href="gallery.php">Return to All Images</a>
    <?php echo '<img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/>' ?>
    <div id="single_img_title"><?php echo '"' . htmlspecialchars($single_img["filename"]) . '"' ?></div>
    <div id="single_img_details">
      <div id="single_img_descrip"><?php echo htmlspecialchars($single_img["description"]) ?></div>

      <div id="single_img_tags"><?php echo $tags_to_print ?></div>

    </div>
  </div>


  <?php include("includes/footer.php");?>
</body>
</html>
