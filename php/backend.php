<?php include("settings.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>SMURFS Backend</title>
    <meta charset=utf-8>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Load Roboto font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <!-- Load css styles -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="../css/backend.css" />
</head>
<body>
<?php
session_start();
if (isset($_POST['submit']) && ($_POST['submit'] == "Login")) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (($username == $adminUserName) && ($password == "$adminPassword")) {
        $_SESSION['login_user'] = $username;
        unset($username);
        unset($password);
    }
}
?>
<div class="container">
    <div class="row">
<?php if (!isset($_SESSION['login_user'])): ?>
    <form method="post" action="backend.php">
        <p>Login backend</p>
        <table>
            <tr>
                <td><label>UserName :</label></td>
                <td><input id="name" name="username" placeholder="username" type="text"></td>
            </tr>
            <tr>
                <td><label>Password :</label></td>
                <td><input id="password" name="password" placeholder="**********" type="password"></td>
            </tr>
            <tr>
                <td colspan="2"><input name="submit" type="submit" value="Login"></td>
            </tr>
        </table>
    </form>
<?php else: ?>

    <?php
    if (isset($_REQUEST["action"]) && ($_REQUEST["action"]=="logout")){
        unset($_SESSION['login_user']);
        header("Location:backend.php");
    }

    $connection = mysql_connect("$databaseHostName", "$databaseUserName", "$databasePassword");
    $db = mysql_select_db("$databaseName", $connection);

    //insert data
    if (isset($_POST['submit']) && (($_POST['submit'] == "Insert Account") || ($_POST['submit'] == "Edit Account"))) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $username = stripslashes($username);
        $password = stripslashes($password);
        $username = mysql_real_escape_string($username);
        $password = mysql_real_escape_string($password);
        $password = empty($password) ? "" : $password;

        $categoryid = $_POST['categoryid'];
        $categoryid = stripslashes($categoryid);
        $categoryid = mysql_real_escape_string($categoryid);

        $insertedDate = date('Y-m-d H:i:s');

        if (!empty($username) && !empty($categoryid) && ($_POST['submit'] == "Insert Account")) {
            $sqlInsert = "INSERT INTO account(username, password, categoryid, inserted_date) VALUES ('$username', '$password', $categoryid, '$insertedDate')";
            mysql_query($sqlInsert, $connection);
            mysql_close($connection);
            header("Location:backend.php");
        }

        if (!empty($username) && !empty($categoryid) && ($_POST['submit'] == "Edit Account")) {
            $accountId = (int)$_REQUEST["accountid"];
            $sqlUpdate = "UPDATE account set username='$username', password = '$password', categoryid=$categoryid where id=$accountId";
            mysql_query($sqlUpdate, $connection);
            mysql_close($connection);
            header("Location:backend.php");
        }
    }

    //delete data
    if (!empty($_REQUEST["action"]) && ($_REQUEST["action"] == "delete") && is_numeric($_REQUEST["accountid"])) {
        $accountId = (int)$_REQUEST["accountid"];
        $sqlDelete = "delete from account where id=$accountId";
        mysql_query($sqlDelete, $connection);
        mysql_close($connection);
        header("Location:backend.php");
    }

    //edit data
    if (!empty($_REQUEST["action"]) && ($_REQUEST["action"] == "edit") && is_numeric($_REQUEST["accountid"])) {
        $accountId = (int)$_REQUEST["accountid"];
        $sqlEdit = "SELECT * FROM account where id=$accountId";
        $editResult = mysql_query($sqlEdit, $connection);
        $editRow = mysql_fetch_assoc($editResult);
        $username = $editRow["username"];
        $password = $editRow["password"];
        $categoryid = $editRow["categoryid"];
        $isEdit = true;
    }

    //select data
    $sqlSelectCount = "SELECT count(id) as total from account";
    $countResult = mysql_query($sqlSelectCount);
    $countRow = mysql_fetch_assoc($countResult);

    if (!isset($rowPerPage)){
        $rowPerPage = 10;
    }

    if( isset($_GET{'page'} ) )
    {
        $page = $_GET{'page'} + 1;
        $offset = $rowPerPage * $page ;
    }
    else
    {
        $page = 0;
        $offset = 0;
    }
    $left_rec = $countRow["total"] - ($page * $rowPerPage);
    $select = "SELECT *
               FROM account
               LIMIT $offset, $rowPerPage
    ";
    $result = mysql_query($select);
    ?>
    <br/>
    <form method="post" action="backend.php">
        <table>
            <tr>
                <td colspan="2">Hello <?php echo  $_SESSION['login_user'];?> <a href="backend.php?action=logout">Logout</a></td>
            </tr>
            <tr>
                <td><label>Username :</label></td>
                <td><input id="name" value="<?php echo isset($username) ? $username : "" ?>" name="username"
                           placeholder="username" type="text"></td>
            </tr>
            <tr>
                <td><label>Password :</label></td>
                <td><input id="password" value="<?php echo isset($password) ? $password : "" ?>" name="password"
                           placeholder="**********" type="text"></td>
            </tr>
            <tr>
                <td><label>Account type :</label></td>
                <td>
                    <select name="categoryid">
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo isset($categoryid) ? "selected" : "" ?>>
                                    <?php
                                    if (isset($value["category_name"])) {
                                        echo $value["category_name"];
                                    }
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="hidden" name="action" value="insert"</td>
            </tr>
            <tr>
                <td colspan="2">
                    <input name="submit" type="submit" value="<?php echo isset($isEdit) ? "Edit" : "Insert" ?> Account">
                    <?php if (isset($isEdit) && isset($accountId)): ?>
                        <input type="hidden" name="accountid" value="<?php echo $accountId; ?>">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <br/><br/>
        <table border="1" style="width: 96%">
            <tr>
                <td colspan="7">
                    Total rows: <?php echo $countRow["total"]; ?>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <?php
                        if( $page > 0 )
                        {
                            $last = $page - 2;
                            echo "<a href=\"backend.php?page=$last\">Last 10 Records</a> | ";
                            echo "<a href=\"backend.php?page=$page\">Next 10 Records</a>";
                        }
                        else if( $page == 0 )
                        {
                            echo "<a href=\"backend.php?page=$page\">Next 10 Records</a>";
                        }
                        else if( $left_rec < $rowPerPage)
                        {
                            $last = $page - 2;
                            echo "<a href=\"backend.php?page=$last\">Last 10 Records</a>";
                        }
                    ?>
                </td>
            </tr>
            <tr class="text-center">
                <td>account id</td>
                <td>username</td>
                <td>password</td>
                <td>category name</td>
                <td>sent</td>
                <td>inserted date</td>
                <td>action</td>
            </tr>
            <?php if (!$result): ?>
                <tr  class="text-center">
                    <td colspan="7">Empty data</td>
                </tr>
            <?php else: ?>
                <?php while ($row = mysql_fetch_assoc($result)) { ?>
                    <tr class="text-center">
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["username"] ?></td>
                        <td><?php echo $row["password"] ?></td>
                        <td>
                            <?php
                            if (isset($categories) && isset($categories[$row["categoryid"]])) {
                                $cat = $categories[$row["categoryid"]];
                                echo $cat["category_name"];
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($row["sent"]) || $row["sent"] == 0) {
                                echo "Not sent";
                            } else {
                                echo "Sent";
                            }
                            ?>
                        </td>
                        <td><?php echo $row["inserted_date"] ?></td>
                        <td><a href="backend.php?action=delete&accountid=<?php echo $row["id"]; ?>">Delete</a> | <a
                                href="backend.php?action=edit&accountid=<?php echo $row["id"]; ?>">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php endif; ?>
        </table>
    </form>
    <?php mysql_close($connection); ?>
<?php endif; ?>
    </div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.mixitup.js"></script>
<script type="text/javascript" src="../js/bootstrap.js"></script>
<script type="text/javascript" src="../js/modernizr.custom.js"></script>
</body>
</html>