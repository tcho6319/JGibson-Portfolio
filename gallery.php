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

if (isset($_GET['search']) && isset($_GET['category']) ) {

  $do_search = TRUE;
  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);

  if (in_array($category, array_keys(SEARCH_FIELDS) )){
    $search_field = $category;

  } else {
    $do_search = FALSE;
    array_push($messages, "Error in Selecting Search Category");
  }

  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
  $search = trim($search);


} else {
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
      echo "<strong>" . htmlspecialchars($message) . "</strong>\n";
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
        <select name="category">
          <option value="" selected disabled>Search By</option>
          <option value="By Artwork Name">By Artwork Name</option>
          <option value="By Description">By Description</option>
        </select>
        <input type="text" name="search"/>
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
    }
    else {
      $sql = "SELECT images.id, images.filename, images.ext FROM images;";
      $params = array();
      $result = exec_sql_query($db, $sql, $params);
      ?>

      <h3 class="disclaimer">Click images to see fullscreen view!</h3>

      <div id="images-container">
        <?php
          $images = $result->fetchAll();
          foreach ($images as $image) {
            print_image($image);
          }
        ?>
      </div>
      <?php

        //  code
    }
      ?>

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
