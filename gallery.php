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

// //upload form

// if ( issset( $_POST["image_upload"]) ) {

//   // get information about image
//   $upload_info = $_FILES["new_image"];
//   $upload_title = filter_input(INPUT_POST, 'upload_title', FILTER_SANITIZE_STRING);
//   $upload_tag = filter_input(INPUT_POST, 'upload_tag', FILTER_SANITIZE_STRING);

//   if ( $upload_info['error'] == UPLOAD_ERR_OK ) {
//     // upload successful
//     // get name
//     $upload_name = basename($upload_info["name"]);
//     // get file extension
//     $upload_ext = strtolower( pathinfo($upload_name, PATHINFO_EXTENSION));
//   }
// }

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
    ?>
      <h1>Search Results</h1>
    <?php
    $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_albums ON images.id = image_albums.image_id INNER JOIN albums ON albums.id = image_albums.album_id WHERE albums.album LIKE '%'||:album||'%' AND || :search_field || LIKE '%' || :search || '%';";
    $params = array(':album' => $user_album, ':search_field' => $search_field, ':search' => $search);
    $result = exec_sql_query($db, $sql, $params);
  }
  else {
    array_push($messages, "Album doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params);
  }
}
else if ($do_search && isset($_GET['by_tag'])) {
  $user_tag = filter_input(INPUT_GET, 'by_tag', FILTER_SANITIZE_STRING);
  if (is_valid_tag($user_tag)) {
    ?>
      <h1>Search Results</h1>
    <?php
      $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON tags.id = image_tags.tag_id WHERE tags.tag LIKE '%'||:tag||'%' AND || :search_field || LIKE '%' || :search || '%';";
      $params = array(':tag' => $user_tag, ':search_field' => $search_field, ':search' => $search);
      $result = exec_sql_query($db, $sql, $params);
  }
  else {
    array_push($messages, "Tag doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params);
  }
}
else if ($do_search) {
  ?>
    <h1>Search Results</h1>
  <?php
    $sql = "SELECT * FROM images WHERE " . $search_field . " LIKE '%' || :search || '%';";
    $params = array(':search' => $search);
    $result = exec_sql_query($db, $sql, $params);
}
else if (!$do_search && isset($_GET['by_album'])) {
  $user_album = filter_input(INPUT_GET, 'by_album', FILTER_SANITIZE_STRING);
  if (is_valid_album($user_album)) {
    $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_albums ON images.id = image_albums.image_id INNER JOIN albums ON albums.id = image_albums.album_id WHERE albums.album LIKE '%'||:album||'%';";
    $params = array(':album' => $user_album);
    $result = exec_sql_query($db, $sql, $params);
  }
  else {
    array_push($messages, "Album doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params);
  }
}
else if (!$do_search && isset($_GET['by_tag'])) {
  $user_tag = filter_input(INPUT_GET, 'by_tag', FILTER_SANITIZE_STRING);
  if (is_valid_tag($user_tag)) {
    $sql = "SELECT DISTINCT images.id, images.filename, images.ext, images.admin_id FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON tags.id = image_tags.tag_id WHERE tags.tag LIKE '%'||:tag||'%';";
    $params = array(':tag' => $user_tag);
    $result = exec_sql_query($db, $sql, $params);
  }
  else {
    array_push($messages, "Tag doesn't exist.");
    $sql = "SELECT * FROM images;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params);
  }
}
else {
  $sql = "SELECT * FROM images;";
  $params = array();
  $result = exec_sql_query($db, $sql, $params);
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
        <a href="gallery.php" class="album-button">All</a>
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
          <label for=\"upload_title\">Title:</label>
          <input id=\"upload_title\" type=\"text\" name=\"upload_title\" />
        </li>
        <li class=\"center\">
        <label for=\"upload_tag\">Tag:</label>
        <input id=\"upload_tag\" type=\"text\" name=\"upload_tag\" />
        </li>
        <li>
          <button class=\"center\" name=\"image_upload\" type=\"submit\">Upload Image</button>
        </li>
      </ul>
    </form>";
// }
?>

  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
