<?php

function showUsersTable($users){
    ?>
    <div class="d-flex justify-content-center mt-3">

        <table class="table w-50">
            <tr>
                <th>Username</th>
                <th>Last login</th>
                <th>Creation date</th>
                <th>Telegram id</th>
            </tr>
            <?php
        foreach($users as $user){
            echo '<tr>';
            echo "<td>$user->username</td>";
            echo "<td>$user->last_login</td>";
            echo "<td>$user->creation_date</td>";
            echo "<td>$user->telegram_id</td>";
            echo '</tr>';
        }
        ?>
        </table>
    </div>
    <?php
}