<?php
include("includes/init.php");

//start session to save array of imgs in gallery that can be accessed in singleimage.php
session_start();

$image_list = $_SESSION["image_list"];
var_dump($image_list);

$messages = array();

// query for deleting an image

if ( isset($_POST["submit_delete"]) ) {
      $selected_id = $_POST["selected_id"];
      $selected_ext = $_POST["selected_ext"];

      $sql = "DELETE FROM images WHERE id = '$selected_id'";
      $result = exec_sql_query($db, $sql);
      $delete_image = 'uploads/images/' . $selected_id . '.' . $selected_ext;
      unlink($delete_image);
      if ($result) {
         echo "Image was deleted from gallery.";
      } else {
        echo "Image could not be deleted.";
      }
}

// query for adding a new tag

if ( isset($_POST["submit_new_tag"]) ) {
  if ( isset($_POST["checkbox"]) ) {
    $tagname = filter_input(INPUT_POST, 'upload_new_tag', FILTER_SANITIZE_STRING);
    $sql = "INSERT INTO tags (tag) VALUES (:tag)";
    $params = array(
      ':tag' => $tagname
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      //success,  tag added to db and image
    }
  }
}

// query for adding existing tag

if ( isset($_POST["submit_existing_tag"]) ) {
  if ( isset($_POST["checkbox"]) ) {
      $existing_tag = filter_input(INPUT_POST, 'upload_existing_tag', FILTER_SANITIZE_SPECIAL_CHARS);
      $selected_id = $_POST["selected_id"];
      $sql = "INSERT INTO image_tags (tag_id, image_id) VALUES (:tag_id, :image_id)";
      $params = array (
        ':tag_id' => $existing_tag,
        ':image_id' => $selected_id
      );
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        //success, tag added to image
      } else {
        array_push($messages, "Failed.");
      }
  } else {
    array_push($messages, "Failed.");
  }
}

// query for editing title

// what is title? description?

// if ( isset($_POST["submit_edit_title"]) ) {
//   if ( isset($_POST["checkbox"]) ) {
//     $edit_title = filter_input(INPUT_POST, 'upload_edit_title', FILTER_SANITIZE_STRING);
//     $selected_id - $_POST["selected_id"];
//     $sql = "UPDATE images SET ";
//   }
// }

// query for uploading a new image

// user needs to be logged in
if ( isset($_POST["submit_upload"]) ) {
  $upload_info = $_FILES["new_image"];
  $upload_description = filter_input(INPUT_POST, 'upload_description', FILTER_SANITIZE_STRING);
  $upload_tag = filter_input(INPUT_POST, 'upload_tag', FILTER_SANITIZE_STRING);
  $upload_album = filter_input(INPUT_POST, 'upload_album', FILTER_SANITIZE_SPECIAL_CHARS);

  if ( $upload_info['error'] == UPLOAD_ERR_OK ) {
    $upload_name = basename($upload_info["name"]);
    $upload_ext = strtolower( pathinfo($upload_name, PATHINFO_EXTENSION) );

  $sql1 = "INSERT INTO images (filename, ext, description, admin_id) VALUES (:filename, :ext, :description, :admin_id)";
  $params1 = array(
    ':filename' => $upload_name,
    ':ext' => $upload_ext,
    ':description' => $upload_description,
    ':admin_id' => $current_admin
  );

  $result = exec_sql_query($db, $sql1, $params1);

  if ($result) {
    //image was added to db
    //need to move image to images folder
    $file_id = $db->lastInsertId("id");
    $id_filename = 'uploads/images/' . $file_id . '.' . $upload_ext;
    if ( move_uploaded_file($upload_info["tmp_name"], $id_filename) ) {
      // image was moved to folder
      } else {
        array_push($messages, "failed");
      }
    } else {
      array_push($messages, "failed");
    }
  } else {
      array_push($messages, "failed");
    }

  if ($upload_tag != null) {
    $sql2 = "INSERT INTO tags (tag) VALUES (:tag)";
    $params2 = array(
      ':tag' => $upload_tag
    );

    $result2 = exec_sql_query($db, $sql2, $params2);
  }

  $newimageid = $db->lastInsertId();
  $sql4 = "INSERT INTO image_albums (album_id, image_id) VALUES (:album_id, :image_id)";
  $params3 = array(
    ':album_id' => $upload_album,
    ':image_id' => $newimageid
  );

  $result4 = exec_sql_query($db, $sql4, $params3);


  header("Location: gallery.php", true, 303);
}

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
    <?php echo '<img alt="' . htmlspecialchars($single_img["description"]) . '" src="uploads/images/' . htmlspecialchars($single_img["id"]) . '.' . htmlspecialchars($single_img["ext"]) . '"/>' ?>
    <div id="single_img_details">
      <div id="single_img_descrip"><?php echo "Description: " . htmlspecialchars($single_img_description) ?></div>

      <div id="single_img_tags"><?php echo $tags_to_print ?></div>

    </div>


    <h3 class="subtitle2">━━━━━ Edit this Image ━━━━━</h3>

    <!-- will uncomment when sessions work -->

    <!-- if ( !check_admin_log_in() ) {
     echo "<h3>Sign in to edit gallery.</h3>";
     }
    // else { -->

    <div id="uploading">

    <!-- delete image button - CURRENTLY NOT FUNCTIONAL DOWN HERE -->


    <input class="center" type="submit" name="submit_delete" value="Delete Painting"></form>


    <!-- add a tag form NOT FUNCTIONAL DOWN HERE-->


    <form id="uploadFile" action="gallery.php" method="post" enctype="multipart/form-data">
    <li class="center">
    <input id="upload_new_tag" type="text" name="upload_new_tag" />
    <button name="submit_new_tag" type="submit">Add a tag</button>
    </li>
    </form>


    <!-- add an existing tag form NOT FUNCTIONAL DOWN HERE-->


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



    <!-- edit title form -->


    <li class="center">
    <input id="upload_edit_title" type="text" name="upload_edit_title" />
    <button name="submit_edit_title" type="submit">Edit title</button>
    </li>
    </div>

    <div id="return_gallery_link"><a href="gallery.php">Return to All Images</a></div>
  </div>

 <!-- if logged in, show edit single image form -->

  <?php include("includes/footer.php");?>
</body>
</html>
