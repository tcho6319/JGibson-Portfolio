<?php
include("includes/init.php");

// Search
const SEARCH_FIELDS = [
  "All" => "By All",
  "Available" => "By Available",
  "Outdoor" => "By Ourdoor",
  "Portrait" => "By Portrait",
  "Illustration" => "By Illustration",
  "Personal" => "By Personal",
];

if (isset($_GET['search']) && isset($_GET['category']) ) {

  $do_search = TRUE;
  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);

  if (in_array($category, array_keys (SEARCH_FIELDS) )){
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

?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
  <?php $gallery="current_page"; ?>

  <?php include("includes/header.php");?>
  <div id="content-wrap">

  <?php
 foreach($messages as $message){
   echo "<strong>" . htmlspecialchars($message) . "</strong>\n";
 }
 ?>


  <form id="search_form" action="gallery.php" method="get" style="text-align:center">
      <select name="category">
        <option value="" selected disabled>Search By</option>
        <option value="All">All</option>
        <option value="Available">Available</option>
        <option value="Outdoor">Outdoor</option>
        <option value="Portrait">Portrait</option>
        <option value="Illustration">Illustration</option>
        <option value="Personal">Personal</option>
      </select>
      <input type="text" name="search"/>
      <button type="submit">Search</button>
    </form>


    <?php
  if ($do_search) {
    ?>
    <h3>Search Results</h3>
    <?php

    $sql = "SELECT * FROM gallery WHERE " . $search_field . " LIKE '%' || :search || '%'";
    $params = array(
      ':search' => $search );
  }else{
    ?>
    <h3>All Arts</h3>
    <?php

//  code
}
?>

  <h3 class="subtitle2">━━━━━ Edit Gallery ━━━━━</h3>

  <form action="gallery.php" method="post">
  <input class="center" type="submit" name="submit" value="Delete Painting">
  </form>

  <?php
  if ( !check_admin_log_in() ) {
    echo "<h3>Sign in to edit gallery.</h3>";
  }
  else {
    echo "<p>Add a new painting:</p>

    <form id=\"uploadFile\" action=\"gallery.php\" method=\"post\" enctype=\"multipart/form-data\">
      <ul id=\"upload_form\">
        <li>
          <!-- declare max file size before uploading an image -->
          <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"<?php echo MAX_FILE_SIZE; ?>\" />
          <label for=\"new_image\">Upload Image:</label>
          <input id=\"new_image\" type=\"file\" name=\"new_image\">
        </div>
        </li>
        <li>
          <label for=\"upload_title\">Title:</label>
          <input id=\"upload_title\" type=\"text\" name=\"upload_title\" />
        </li>
          <button name=\"submit_upload\" type=\"submit\">Upload Image</button>
        </li>
      </ul>
    </form>";
  }
  ?>

  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
