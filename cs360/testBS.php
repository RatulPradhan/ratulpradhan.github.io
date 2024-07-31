<?php
session_start();

$op = $_GET['op'];

if ($op == "login") {
    $_SESSION['uid'] = $_POST['uid']; // form
    $op = "";
}
else if ($op == "logout") {
    unset($_SESSION['uid']);
    $op = "";
}
?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>Bootstrap test</TITLE>

<?php include("bootstrap.php"); ?>

<STYLE>

.menuItem {
    border: 1px solid white;
    background-color: MidnightBlue;
    color: orange;
    text-align: center;
    padding-top: 10px;
    padding-bottom: 10px;
}

.menuItem:hover {
    background-color: orange;
    color: MidnightBlue;
}

</STYLE>

</HEAD>

<BODY>

<DIV class="container">

<!-- welcome banner -->
<DIV class="row" style="border: 3px solid blue; padding: 20px; margin-top: 10px;">

    <DIV class="col-md-10">
    <H2>
    <?php 
        if (isset($_SESSION['uid'])) {
            print("Welcome " . $_SESSION['uid']);
        }
        else {
            print("Welcome -- please login");
        }
    ?>
    
    </H2>
    </DIV>

    <!-- login/logout forms -->
    <DIV class="col-md-2">

    <?php
    // display login form
    if (!isset($_SESSION['uid'])) {
        printf("<FORM name='fmLogin' method='POST' action='?op=login'>\n");
        printf("<INPUT type='text' name='uid' size='5' placeholder='user id' />\n");
        printf("<INPUT type='submit' value='login' />\n");
        printf("</FORM>\n");
    }
    else { // display logout form
        printf("<FORM name='fmLogout' method='POST' action='?op=logout'>\n");
        printf("<INPUT type='submit' value='logout' />\n");
        printf("</FORM>\n");
    }
    ?>

    </DIV>


</DIV> <!-- closes banner row -->

<DIV class="row" style="padding-top: 10px;"><!-- menu -->

<DIV class="col-md-2 menuItem">
Menu 1
</DIV>

<DIV class="col-md-4 menuItem">
Menu 2
</DIV>

<DIV class="col-md-6 menuItem">
Menu 3
</DIV>


</DIV>

<DIV class="row"><! -- content -->


</DIV>

</DIV>

</BODY>
</HTML>

