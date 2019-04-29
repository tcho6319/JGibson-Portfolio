<header>
    <h1 id="title">Jennifer Gibson</h1>

    <?php
        $pages = [['index.php', 'Home'],['gallery.php', 'Gallery'],['about.php', 'About'],['contact.php', 'Contact']];
        $current_file = basename($_SERVER['PHP_SELF']);
    ?>
    <h2 id="nav_bar">
        <ul>
            <?php foreach($pages as $page) {
                if ($page[0] == $current_file) {
                    echo "<li id=\"current_page\"><a href=\"".$page[0]."\">".$page[1]."</a></li>";
                }
                else {
                    echo "<li><a href=\"".$page[0]."\">".$page[1]."</a></li>";
                }
            } ?>
        </ul>
    </h2>
</header>
