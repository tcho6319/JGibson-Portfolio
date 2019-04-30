<?php
include("includes/init.php");

$tags_sql = "SELECT tags.id, tags.tag FROM tags";
$tags_params = array();
$tags_result = exec_sql_query($db, $tags_sql, $tags_params);
$tags = $tags_result->fetchAll();
$albums_sql = "SELECT albums.id, albums.album FROM albums";
$albums_params = array();
$albums_result = exec_sql_query($db, $albums_sql, $albums_params);
$albums = $albums_result->fetchAll();

$messages = array();

const MAX_FILE_SIZE = 1000000;

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

  $sql2 = "INSERT INTO tags (tag) VALUES (:tag)";
  $params2 = array(
    ':tag' => $upload_tag
  );

  $result2 = exec_sql_query($db, $sql2, $params2);

  //need to figure out how to get image id
  // $sql3 = "INSERT INTO image_albums (album_id) VALUES (:album_id)";
  // $params3 = array(
  //   ':album_id' => $upload_album
  // );

  // $result3 = exec_sql_query($db, $sql3, $params3);

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
        <?php echo '<a href="singleimage.php?'.http_build_query(array('id' => $fileid)).'"'?>><img src=<?php echo $fullpath;?> alt=<?php echo $filename;?>></a>
        <figcaption>Artwork created by Jennifer Gibson.</figcaption>
      </figure>
    </div>
<?php
}

function print_album_buttons($album) {
  $album_text = htmlspecialchars($album["album"]);
  ?>
    <button class="album-button"><?php echo ucfirst($album_text); ?></button>
<?php
  }

function print_tag_buttons($tag) {
  $tag_text = htmlspecialchars($tag["tag"]);
  ?>
    <button class="tag-button"><?php echo ucfirst($tag_text); ?></button>
<?php
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

    <div id="gallery-button-group">
      <span id="album-buttons">
        <?php
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
        if (isset($search_field)) { ?>
          <select name="category">
          <option value="" disabled>Search By</option>
          <?php
            foreach(SEARCH_FIELDS as $dbname => $label){
              ?>
              <option value="<?php echo $dbname;?>" <?php if (isset($search_field) && ($search_field == $dbname)) { echo selected; } ?>><?php echo $label;?></option>
              <?php
            }
          ?>
        </select>
        <?php
        }
        else { ?>
          <select name="category">
          <option value="" selected disabled>Search By</option>
          <?php
            foreach(SEARCH_FIELDS as $dbname => $label) {
              ?>
              <option value="<?php echo $dbname;?>"><?php echo $label;?></option>
              <?php
            }
        }
        ?>
        </select>
        <input type="text" name="search" value="<?php if (isset($search)) { echo $search; } ?>"/>
        <button type="submit">Search</button>
      </form>
    </div>

    <?php
    if ($do_search) {
      ?>
      <h1>Search Results</h1>
      <?php
        $sql = "SELECT * FROM images WHERE " . $search_field . " LIKE '%' || :search || '%';";
        $params = array(':search' => $search);
        $result = exec_sql_query($db, $sql, $params);
    }
    else {
      $sql = "SELECT images.id, images.filename, images.ext FROM images;";
      $params = array();
      $result = exec_sql_query($db, $sql, $params);
    }
    ?>

    <h3 class="disclaimer">Click images to see fullscreen view!</h3>

    <div id="images-container">
      <?php
        $images = $result->fetchAll();
        if (count($images)>0) {
          foreach ($images as $image) {
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

    <h3 class="subtitle2">━━━━━ Edit Gallery ━━━━━</h3>

    <form action="gallery.php" method="post">
    <input class="center" type="submit" name="submit" value="Delete Painting">
    </form>

    <?php
    // if ( !check_admin_log_in() ) {
    //   echo "<h3>Sign in to edit gallery.</h3>";
    // }
    // else {
    echo "
    <form id=\"uploadFile\" action=\"gallery.php\" method=\"post\" enctype=\"multipart/form-data\">
      <ul id=\"upload_form\">
        <li class=\"center\">
          <!-- declare max file size before uploading an image -->
          <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"<?php echo MAX_FILE_SIZE; ?>\" />
          <label for=\"new_image\">Add a new painting:</label>
          <input id=\"new_image\" type=\"file\" name=\"new_image\">
        </div>
        </li>
        <li class=\"center\">
        <select name=\"upload_album\">
          <option value=\"available\">Available</option>
          <option value=\"outdoor\">Outdoor</option>
          <option value=\"portrait\">Portrait</option>
          <option value=\"illustration\">Illustration</option>
          <option value=\"personal\">Personal</option>
        </select>
        <li class=\"center\">
          <label for=\"upload_title\">Title:</label>
          <input id=\"upload_title\" type=\"text\" name=\"upload_title\" />
        </li>
        <li class=\"center\">
        <p>Description:</p>
        <textarea id=\"upload_description\" name=\"upload_description\" rows=\"10\" cols=\"30\"/>
        </textarea>
        </li>
        <li>
        <li class=\"center\">
        <label for=\"upload_tag\">Tag:</label>
        <input id=\"upload_tag\" type=\"text\" name=\"upload_tag\" />
        </li>
        <li>
          <button class=\"center\" name=\"submit_upload\" type=\"submit\">Upload Image</button>
        </li>
      </ul>
    </form>";
// }
?>

  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
