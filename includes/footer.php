<footer>
    <div class="footer">
    <div class="login">
        <hr>
        <p>Log-In <br> *Admins only*


        <?php
        foreach($messages as $message){
            echo "<strong>" . htmlspecialchars($message) . "</strong>\n";}
        // this prints a message to admins

        if (!$current_admin) { ?>
        <form id="loginForm" action="index.php" method="POST">
                <ul style="list-style-type:none;">
                <li>
                    <label>Admin ID: </label>
                    <input type="text" name="admin_id" placeholder="Enter Admin ID" required/>
                </li>
                <li>
                    <label>Password: </label>
                    <input type="password" name="password" placeholder="Enter Password" required/>
                </li>
                <li>
                    <button name="login" type="submit"><strong>Log In</strong></button>
                </li>
                </ul>
        </form>
        <?php
        } else {
            echo "<br><strong>You are logged in</strong>\n";}?>
        </p>
    </div>
    </div>


</footer>
