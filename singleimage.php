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
$single_img_ext = $single_img["ext"];

$tags_sql = "SELECT tags.id, tags.tag FROM tags";
$tags_params = array();
$tags_result = exec_sql_query($db, $tags_sql, $tags_params);
$tags = $tags_result->fetchAll();

$messages = array();

//query for deleting an image
if ( isset($_POST["submit_delete"]) ) {
      $sql = "DELETE FROM images WHERE id = :id;";
      $params = array(
        ':id' => $single_img_id
      );
      $result = exec_sql_query($db, $sql, $params);
      $delete_image = 'uploads/images/' . $single_img_id . '.' . $single_img_ext;
      unlink($delete_image);
      if ($result) {
        $location = "gallery.php";
        header("Location: $location?delete_message=Image has been deleted from gallery.");
        echo("Image was deleted from gallery");
      } else {
        echo "Image could not be deleted.";
      }
  }


// query for adding a new tag
if ( isset($_POST["submit_new_tag"]) ) {
    $tagname = filter_input(INPUT_POST, 'upload_new_tag', FILTER_SANITIZE_STRING);
    $sql = "INSERT INTO tags (tag) VALUES (:tag)";
    $params = array(
      ':tag' => $tagname
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      //success
      //array message here
    } else {
      // array message here
    }
  }


// query for adding existing tag
if ( isset($_POST["submit_existing_tag"]) ) {
      $existing_tag = filter_input(INPUT_POST, 'upload_existing_tag', FILTER_SANITIZE_SPECIAL_CHARS);
      $sql = "INSERT INTO image_tags (tag_id, image_id) VALUES (:tag_id, :image_id)";
      $params = array (
        ':tag_id' => $existing_tag,
        ':image_id' => $single_img_id
      );
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        //success, tag added to image
        //array message here
      } else {
        array_push($messages, "Failed.");
      }
  } else {
    array_push($messages, "Failed.");
  }


// query for editing title

if ( isset($_POST["submit_edit_title"]) ) {
    $edit_title = filter_input(INPUT_POST, 'upload_edit_title', FILTER_SANITIZE_STRING);
    var_dump($edit_title);
    $new_title = str_replace(" ", "-", $edit_title);
    $new_title = strtolower($new_title);
    $sql = "UPDATE images SET (filename) = (:filename) WHERE id = :id;";
    $params = array(
      ':filename' => $new_title,
      ':id' => $single_img_id
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      //success
      //message here
    } else {
      //message
    }
  }


//   //function to process filename from title input
// function process_title($single_img_filename){
//   //replace - with space
//   $single_img_filename = str_replace(" ", "-", $single_img_filename);
//   //capitalize first word
//   $single_img_filename = strtolower($single_img_filename);
//   return $single_img_filename;
// }



// var_dump($single_img);
//function to search $image_list for index of current single image. Returns False if not found
function search_image_list($single_img, $image_list){
  $single_img_ind_image_list = 0;
  foreach ($image_list as $image){
    if ($image["id"] == $single_img["id"]){
      return $single_img_ind_image_list;
    }
    $single_img_ind_image_list = $single_img_ind_image_list + 1;
  }
  return FALSE;
}

//slidshow buttons
$single_img_ind_image_list = search_image_list($single_img, $image_list);   //index of current single image in image_list
// var_dump($single_img_ind_image_list);
// var_dump(count($image_list)-1);
if ($single_img_ind_image_list == (count($image_list) - 1)){ //current image is last image in image_list
  $next_img_ind_image_list = 0;
  $back_img_ind_image_list = $single_img_ind_image_list - 1;
  // echo "In correct if block";
}
elseif ($single_img_ind_image_list == 0){ //current image is first image in image_list
  $back_img_ind_image_list = count($image_list) - 1;
  $next_img_ind_image_list = $single_img_ind_image_list + 1;
  // echo "In single_img_ind_image_list == 0 block";
}
else { //in middle of slideshow
  $back_img_ind_image_list = $single_img_ind_image_list - 1;
  $next_img_ind_image_list = $single_img_ind_image_list + 1;
  // echo "In else block";
}
$back_img = $image_list[$back_img_ind_image_list];
$next_img = $image_list[$next_img_ind_image_list];
// var_dump($back_img_ind_image_list);
// var_dump($next_img_ind_image_list);
$back_img_id = $back_img["id"];
$next_img_id = $next_img["id"];
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
  global $db;
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
        $tags_to_print = $tags_to_print . ucfirst(htmlspecialchars($record["tag"])) . ' ' . '&#9830 ' . ' ';
      }
      else{
        $tags_to_print = ' ' . $tags_to_print . ucfirst(htmlspecialchars($record["tag"]));
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
//Call to print single image tags
$tags_to_print = print_single_img_tags($single_img_id);
?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>


<body>
<?php include("includes/header.php");?>

  <div id="singleimgblock">
    <div id="single_img_title"><?php echo '"' . htmlspecialchars($single_img_filename) . '"' ?></div>

    <div id="slideshowdiv">
    <?php if (count($image_list) > 1) { ?>
    <div id="back_button"><?php echo '<a href="singleimage.php?'.http_build_query(array('id' => $back_img_id)).'"'?>>&lt</a></div>
    <?php } ?>
    <?php
    if ($single_img_id != 17){
      echo '<img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/>';
    }
    else{ //accounting for very wide mustache piece
      echo '<div id="mustaches"><img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/></div>';
    }
    ?>

    <?php if (count($image_list) > 1) { ?>
    <div id="next_button"><?php echo '<a href="singleimage.php?'.http_build_query(array('id' => $next_img_id)).'"'?>>&gt</a></div>
    <?php } ?>
  </div>

    <div id="single_img_details">
      <div id="single_img_descrip"><?php echo "Description: " . htmlspecialchars($single_img_description) ?></div>

      <div id="single_img_tags"><?php echo $tags_to_print ?></div>

    </div>

  <?php if (check_admin_log_in() != NULL) { ?>
    <h3 class="subtitle2">━━━━━ Edit this Image ━━━━━</h3>


   <!-- will uncomment when sessions work -->

    <!-- if ( !check_admin_log_in() ) {
     echo "<h3>Sign in to edit gallery.</h3>";
     }
    // else { -->

    <div id="uploading">


    <!-- delete image button - CURRENTLY NOT FUNCTIONAL DOWN HERE -->
    <form id="submit_delete" action="" method="post" enctype="multipart/form-data">
      <input class="center" type="submit" name="submit_delete" value="Delete Painting">
    </form>

<!-- add a tag form NOT FUNCTIONAL DOWN HERE-->

    <form id="newtag" action="" method="post" enctype="multipart/form-data">
    <li class="center">
    <input id="upload_new_tag" type="text" name="upload_new_tag" />
    <button name="submit_new_tag" type="submit">Add a tag</button>
    </li>
    </form>


    <!-- add an existing tag form NOT FUNCTIONAL DOWN HERE-->

    <form id="existingtag" action="" method="post" enctype="multipart/form-data">
    <li class="center">
    <select name="upload_existing_tag">";

    <?php
    foreach ($tags as $tag) {
      $tag_text = htmlspecialchars($tag["tag"]);
      echo "<option value=\"" . $tag_text . "\">" . $tag_text . "</option>";
    }
    ?>

    </select>
    <button name="submit_existing_tag" type="submit">Add existing tag</button>
    </li>
  </form>


   <!-- edit title form -->

   <form id="edittitle" action="" method="post" enctype="multipart/form-data">
    <li class="center">
    <input id="upload_edit_title" type="text" name="upload_edit_title" />
    <button name="submit_edit_title" type="submit">Edit title</button>
    </li>
  </form>

  </div>
  <?php } ?>

    <div id="return_gallery_link"><a href="gallery.php">Return to All Images</a></div>
  </div>


 <!-- if logged in, show edit single image form -->

  <?php include("includes/footer.php");?>
</body>
</html>
