<footer>
    <div class="footer">
    <div class="login">
        <hr>

        <?php
        foreach($messages_login as $message_login){
            echo "<strong>" . htmlspecialchars($message_login) . "</strong>\n";}
        // this prints a message to admins

        if (!$current_admin) { ?>
            <form id="loginForm" action="index.php" method="POST">
            <p>Log-In<br> *Admins only*</p>

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
            echo "<br>You are logged in\n"; ?> <p>
            <form id="loginForm" action="index.php" method="POST">
            <button name="logout" type="submit"><strong>Log Out</strong></button>

        </form>
        <?php }?>


    </div>
    </div>


</footer>
