<?php
include("includes/init.php");
//start session to save array of imgs in gallery that can be accessed in singleimage.php
session_start();
$_SESSION["image_list"] = []; //list of images. either all or after search query
$tags_sql = "SELECT tags.id, tags.tag FROM tags";
$tags_params = array();
$tags_result = exec_sql_query($db, $tags_sql, $tags_params);
$tags = $tags_result->fetchAll();
$albums_sql = "SELECT albums.id, albums.album FROM albums";
$albums_params = array();
$albums_result = exec_sql_query($db, $albums_sql, $albums_params);
$albums = $albums_result->fetchAll();

$messages = array();
$upload_messages = array();

const MAX_FILE_SIZE = 1000000;
//query for image upload
// user needs to be logged in
if ( isset($_POST["submit_upload"]) ) {
  $valid_upload = TRUE;

  $upload_info = $_FILES["new_image"];
  $upload_description = filter_input(INPUT_POST, 'upload_description', FILTER_SANITIZE_STRING);
  $upload_tag = filter_input(INPUT_POST, 'upload_tag', FILTER_SANITIZE_STRING);
  $upload_album = filter_input(INPUT_POST, 'upload_album', FILTER_SANITIZE_SPECIAL_CHARS);

  // var_dump($upload_description);

  // make sure user selects album
  if ( isset($upload_album) == FALSE) {
    $valid_upload = FALSE;
    array_push($upload_messages, "Please select an album for the upload.");
  }

  // tags can be added and edited later, so not required upon submission

  // make sure user writes description
  if (trim($upload_description) == '') {
    $valid_upload = FALSE;
    array_push($upload_messages, "Please write a description for the upload.");
  }

  if ( isset($valid_upload) && $valid_upload ) {
    if ( $upload_info['error'] == UPLOAD_ERR_OK ) {
      $upload_name = basename($upload_info["name"]);
      $upload_filename = strtolower( pathinfo($upload_name, PATHINFO_FILENAME));
      $upload_ext = strtolower( pathinfo($upload_name, PATHINFO_EXTENSION) );

      $sql1 = "INSERT INTO images (filename, ext, description, admin_id) VALUES (:filename, :ext, :description, :admin_id)";
      $params1 = array(
        ':filename' => $upload_filename,
        ':ext' => $upload_ext,
        ':description' => $upload_description,
        ':admin_id' => $current_admin
      );
      $result = exec_sql_query($db, $sql1, $params1);

      if ($result && $upload_info['error'] == UPLOAD_ERR_OK) {
        //image was added to db
        //need to move image to images folder
        $file_id = $db->lastInsertId("id");
        $id_filename = 'uploads/images/' . $file_id . '.' . $upload_ext;
        if ( move_uploaded_file($upload_info["tmp_name"], $id_filename) ) {
          // image was moved to folder
        } else {
            array_push($upload_messages, "Image could not be uploaded.");
        }

      } else {
          array_push($upload_messages, "Image could not be uploaded. Make sure you selected a file.");
      }

    if ($upload_tag && $upload_tag != null && result && $upload_info['error'] == UPLOAD_ERR_OK) {
      $sql2 = "INSERT INTO tags (tag) VALUES (:tag)";
      $params2 = array(
        ':tag' => $upload_tag
      );
      $result2 = exec_sql_query($db, $sql2, $params2);

      $newtagid = $db->lastInsertId("id");
      // var_dump($newtagid, $file_id);
      $sql_tag = "INSERT INTO image_tags (tag_id, image_id) VALUES (:tag_id, :image_id);";
      $params_tag = array(
        ':tag_id'=>$newtagid,
        ':image_id'=>$file_id
      );
      $result_tag = exec_sql_query($db, $sql_tag, $params_tag);
    }

    $newimageid = $db->lastInsertId();
    $sql_album = "SELECT id FROM albums WHERE album = :album";
    $params = array(
        ':album' => $upload_album
      );
    $result_album = exec_sql_query($db, $sql_album, $params)->fetchAll();
    $single_album = $result_album[0];
    $album_needed = $single_album[0];
      $sql4 = "INSERT INTO image_albums (album_id, image_id) VALUES (:album_id, :image_id)";
      $params3 = array(
        ':album_id' => $album_needed,
        ':image_id' => $newimageid
      );
      $result4 = exec_sql_query($db, $sql4, $params3);
  }
  if ($upload_info['error'] != UPLOAD_ERR_OK){
    array_push($upload_messages, "Image could not be uploaded. Make sure you selected a file.");
  }
  $location="gallery.php";
  if ($result && $result2 && $result_tag && $result4) {
    header("Location: $location?upload_message=Image has been uploaded to the gallery.");
  }
}
}

// Search
const SEARCH_FIELDS = [
  "filename" => "By Artwork Name",
  "description" => "By Description",
];
if (isset($_GET['search']) && isset($_GET['category'])) {
  $do_search = TRUE;
  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
  if (in_array($category, array_keys(SEARCH_FIELDS))) {
    $search_field = $category;
    $search_field_db_key = array_search($search_field, SEARCH_FIELDS);
  }
  else {
    $do_search = FALSE;
    array_push($messages, "Error in selecting search category.");
  }
  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
  $search = trim($search);
  if ($search == "") {
    $do_search = FALSE;
    array_push($messages, "Please enter a search term.");
  }
}
elseif(isset($_GET['search']) && !isset($_GET['category'])) {
  $do_search = FALSE;
  array_push($messages, "Please enter a valid search category.");
  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
  $search = trim($search);
}
else {
  $do_search = FALSE;
  $category = NULL;
  $search = NULL;
}
function print_image($image) {
  $fileid = htmlspecialchars($image["id"]);
  $filename = htmlspecialchars($image["filename"]);
  $fileext = htmlspecialchars($image["ext"]);
  $fullpath = "uploads/images/".$fileid.".".$fileext;
  ?>
    <div class="image-content">
      <figure>
        <!-- Artwork created by Jennifer Gibson. -->
        <?php echo '<a href="singleimage.php?'.http_build_query(array('id' => $fileid)). '#singleimgblock' . '"'?>><img src=<?php echo $fullpath;?> alt=<?php echo $filename;?>></a>
        <figcaption>Artwork created by Jennifer Gibson.</figcaption>
      </figure>
    </div>
<?php
}
function print_album_buttons($album) {
  $album_text = htmlspecialchars($album["album"]);
  if (isset($_GET['by_album'])) {
    if (filter_input(INPUT_GET, 'by_album', FILTER_SANITIZE_STRING) == $album_text) {
      echo '<a href="gallery.php?'.http_build_query(array('by_album' => $album_text)).'" id="selected">'.ucfirst($album_text).'</a>';
    }
    else {
      echo '<a href="gallery.php?'.http_build_query(array('by_album' => $album_text)).'" class="album-button">'.ucfirst($album_text).'</a>';
    }
  }
  else {
    echo '<a href="gallery.php?'.http_build_query(array('by_album' => $album_text)).'" class="album-button">'.ucfirst($album_text).'</a>';
  }
}
function print_tag_buttons($tag) {
  $tag_text = htmlspecialchars($tag["tag"]);
  if (isset($_GET['by_tag'])) {
    if (filter_input(INPUT_GET, 'by_tag', FILTER_SANITIZE_STRING) == $tag_text) {
      echo '<a href="gallery.php?'.http_build_query(array('by_tag' => $tag_text)).'" id="selected">'.ucfirst($tag_text).'</a>';
    }
    else {
      echo '<a href="gallery.php?'.http_build_query(array('by_tag' => $tag_text)).'" class="tag-button">'.ucfirst($tag_text).'</a>';
    }
  }
  else {
    echo '<a href="gallery.php?'.http_build_query(array('by_tag' => $tag_text)).'" class="tag-button">'.ucfirst($tag_text).'</a>';
  }
}
function is_valid_album($user_album) {
  global $albums;
  $valid_album = FALSE;
  foreach($albums as $album) {
    if ($user_album == $album["album"]) {
      $valid_album = TRUE;
    }
  }
  return $valid_album;
}
function is_valid_tag($user_tag) {
  global $tags;
  $valid_tag = FALSE;
  foreach($tags as $tag) {
    if ($user_tag == $tag["tag"]) {
      $valid_tag = TRUE;
    }
  }
  return $valid_tag;
}
if ($do_search && isset($_GET['by_album'])) {
  $user_album = filter_input(INPUT_GET, 'by_album', FILTER_SANITIZE_STRING);
  if (is_valid_album($user_album)) {
    array_push($messages, "Search Results");
    if ($user_album == "available") {
      array_push($messages, "All pieces currently being viewed can be purchased upon request. Please use the contact page to inquire about commissions or purchases.");
    }
    if ($search_field == 'filename') {
      $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_albums ON images.id = image_albums.image_id INNER JOIN albums ON albums.id = image_albums.album_id WHERE albums.album = :album AND filename LIKE '%'||:search||'%';";
    }
    else {
      $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_albums ON images.id = image_albums.image_id INNER JOIN albums ON albums.id = image_albums.album_id WHERE albums.album = :album AND description LIKE '%'||:search||'%';";
    }
    $params = array(':album' => $user_album, ':search' => $search);
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
  else {
    array_push($messages, "Album doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
}
else if ($do_search && isset($_GET['by_tag'])) {
  $user_tag = filter_input(INPUT_GET, 'by_tag', FILTER_SANITIZE_STRING);
  if (is_valid_tag($user_tag)) {
    array_push($messages, "Search Results");
    if ($search_field == 'filename') {
      $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON tags.id = image_tags.tag_id WHERE tags.tag = :tag AND filename LIKE '%'||:search||'%';";
    }
    else {
      $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON tags.id = image_tags.tag_id WHERE tags.tag = :tag AND description LIKE '%'||:search||'%';";
    }
    $params = array(':tag' => $user_tag, ':search' => $search);
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
  else {
    array_push($messages, "Tag doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
  }
}
else if ($do_search) {
  array_push($messages, "Search Results");
  if ($search_field == 'filename') {
    $sql = "SELECT * FROM images WHERE filename LIKE '%' ||:search|| '%';";
  }
  else {
    $sql = "SELECT * FROM images WHERE description LIKE '%' ||:search|| '%';";
  }
  $params = array(':search' => $search);
  $result = exec_sql_query($db, $sql, $params)->fetchAll();
  // var_dump($result->fetchAll());
}
else if (!$do_search && isset($_GET['by_album'])) {
  $user_album = filter_input(INPUT_GET, 'by_album', FILTER_SANITIZE_STRING);
  if (is_valid_album($user_album)) {
    if ($user_album == "available") {
      array_push($messages, "All pieces currently being viewed can be purchased upon request. Please use the contact page to inquire about commissions or purchases.");
    }
    $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_albums ON images.id = image_albums.image_id INNER JOIN albums ON albums.id = image_albums.album_id WHERE albums.album LIKE '%'||:album||'%';";
    $params = array(':album' => $user_album);
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
  else {
    array_push($messages, "Album doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
}
else if (!$do_search && isset($_GET['by_tag'])) {
  $user_tag = filter_input(INPUT_GET, 'by_tag', FILTER_SANITIZE_STRING);
  if (is_valid_tag($user_tag)) {
    $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON tags.id = image_tags.tag_id WHERE tags.tag LIKE '%'||:tag||'%';";
    $params = array(':tag' => $user_tag);
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
    // var_dump($result->fetchAll());
  }
  else {
    array_push($messages, "Tag doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params)->fetchAll();
  }
}
else {
  $sql = "SELECT * FROM images;";
  $params = array();
  $result = exec_sql_query($db, $sql, $params)->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
  <?php $gallery="current_page"; ?>

  <?php include("includes/header.php");?>

  <div id="gallery-content">

    <?php
    foreach($messages as $message){
      echo "<p class='disclaimer'>" . htmlspecialchars($message) . "</p>\n";
    }
    ?>
    <h1>Gallery</h1>

    <h2>Albums&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tags</h2>

    <div id="gallery-button-group">
      <span id="album-buttons">
        <?php
        echo '<a href="gallery.php" class="album-button">All</a>';
        foreach($albums as $album){
          print_album_buttons($album);
        }
        ?>
      </span>
      <span id="tag-buttons">
        <?php
        foreach($tags as $tag){
          print_tag_buttons($tag);
        }
        ?>
      </span>
    </div>

    <div>
      <form id="search_form" action="gallery.php" method="get">
        <?php
        if (isset($user_album)) { ?>
          <input type="hidden" name="<?php echo "by_album" ?>" value="<?php echo $user_album; ?>"/>
        <?php }
        if (isset($user_tag)) { ?>
          <input type="hidden" name="<?php echo "by_tag" ?>" value="<?php echo $user_tag; ?>"/>
        <?php }
        if (isset($search_field)) { ?>
          <select name="category">
            <option value="" disabled>Search By</option>
            <?php
            foreach(SEARCH_FIELDS as $dbname => $label) { ?>
              <option value="<?php echo $dbname;?>" <?php if (isset($search_field) && ($search_field == $dbname)) { echo 'selected="selected"'; } ?>><?php echo $label;?></option>
              <?php
            }
          ?>
        </select>
        <?php
        }
        else { ?>
          <select name="category">
          <option value="" selected="selected" disabled>Search By</option>
          <?php
            foreach(SEARCH_FIELDS as $dbname => $label) {
              ?>
              <option value="<?php echo $dbname;?>"><?php echo $label;?></option>
              <?php
            } ?>
          </select>
        <?php }
        ?>

        <input type="text" name="search" value="<?php if (isset($search)) { echo $search; } ?>"/>
        <button type="submit">Search</button>
      </form>
    </div>


    <h3 class="disclaimer">Click images to see fullscreen view!</h3>

    <?php if( $_GET['delete_message'] ) {
        $delete_message = $_GET['delete_message'];
        echo '<p class="center"><strong>' . $delete_message . '</strong></p>';
      }
      ?>

<?php if( $_GET['upload_message'] ) {
        $upload_message = $_GET['upload_message'];
        echo '<p class="center"><strong>' . $upload_message . '</strong></p>';
      }
      ?>

      <div id="images-container">
      <?php
        // var_dump($result);
        $_SESSION["image_list"] = $result;
        if (count($result)>0) {
          foreach ($result as $image) {
            print_image($image);
          }
        }
        else {
          ?>
          <h2>No Search Results Found. Please try another search term.</h2>
          <?php
        }
      ?>
    </div>

<?php if (check_admin_log_in() != NULL) { ?>
    <h3 class="subtitle2">━━━━━ Edit this Gallery ━━━━━</h3>

    <div id="uploading">

    <?php
    // var_dump($upload_messages);
    foreach($upload_messages as $message){
      echo "<p class='center'><strong>" . htmlspecialchars($message) . "</strong></p>\n";
    }
    ?>

    <p class="center">Please click on an image to delete it or edit its title/tags.</p>
    <!--  form for adding an image  -->

    <form id="uploadimg" action="gallery.php" method="post" enctype="multipart/form-data">
      <ul id="upload_form">
        <li class="center">
          <!-- declare max file size before uploading an image -->
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
          <p><strong>Add a new painting:</strong></p>
          <input id="new_image" type="file" name="new_image">
        </li>
        <li class="center">
        <p class="center">Album:</p>
        <select name="upload_album">
          <option disabled selected value>Pick an Album</option>
          <option <?php if($upload_album=="available") echo 'selected="selected"'; ?> value="available">Available</option>
          <option <?php if($upload_album=="outdoor") echo 'selected="selected"'; ?> value="outdoor">Outdoor</option>
          <option <?php if($upload_album=="portrait") echo 'selected="selected"'; ?> value="portrait">Portrait</option>
          <option <?php if($upload_album=="illustration") echo 'selected="selected"'; ?> value="illustration">Illustration</option>
          <option <?php if($upload_album=="personal") echo 'selected="selected"'; ?> value="personal">Personal</option>
        </select>
        </li>
        <li class="center">
        <p>New tag:</p>
        <input id="upload_tag" type="text" name="upload_tag" value="<?php echo $upload_tag; ?>"/>
        <p class = 'center'><em>(Existing tag can be added later)</em></p>
        </li>
        <li class="center">
        <p>Description:</p>
        <textarea id="upload_description" name="upload_description" rows="10" cols="30">
        <?php echo $upload_description; ?>
        </textarea>
        </li>
        <li>
          <button class="center" name="submit_upload" type="submit">Upload Image</button>
        </li>
      </ul>
    </form>
    </div>
<?php } ?>
  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
