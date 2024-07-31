<?php
session_start();

include("bootstrap.php");
include("h3util.php");


$op = $_GET['op'];
$uid = $_GET['uid'];
$mid = $_GET['mid'];

if ($op == "login") {

    if (getName($db, $_POST['uid']) != "") {

        $_SESSION["uid"] = $_POST['uid'];
        $op = "";
    } else {
        print('<P> WRONG LOGIN INFO, PLEASE TRY AGAIN </P>');
        $op = "";
    }
} else if ($op == "logout") {
    unset($_SESSION['uid']);
    $op = "";
}
?>

<!DOCTYPE html>
<HTML>

<HEAD>
    <TITLE> Dashboard </TITLE>



    <LINK type="text/css" rel="stylesheet" href="style.css" />

</HEAD>

<BODY>

    <DIV class="container">

        <DIV class="row mainHeading">
            <DIV class="col-md-10">
                <H2>
                    <?php
                    if (isset($_SESSION['uid'])) {
                        printf("<p class='navBoxStyle' style='background-color:#D7EDFA'> Welcome %s! </p>", getName($db, $_SESSION['uid']));
                    } else {
                        print("<p class='navBoxStyle' style='background-color:#D7EDFA'> Messaging Website  </p>");
                    }
                    ?>

                </H2>
            </DIV>


            <DIV class="col-md-2">

                <?php
                // display login form
                if (!isset($_SESSION['uid'])) {
                    printf("<FORM class = 'navBoxStyle' name='fmLogin' method='POST' action='?op=login'>\n");
                    printf("<INPUT type='text' name='uid' size='5' placeholder='user id' />\n");
                    printf("<INPUT type='submit' value='login' />\n");
                    printf("</FORM>\n");

                } else { // display logout form
                    printf("<FORM class = 'navBoxStyle' name='fmLogout' method='POST' action='?op=logout'>\n");
                    printf("<INPUT type='submit' value='logout' />\n");
                    printf("</FORM>\n");
                }
                ?>

            </DIV>
        </DIV>

        <ul class="nav justify-content-center" style="color: black ">
            <li class="nav-item ">

                <a class="nav-link active" href="?op=inbox">
                    <DIV class="navBoxStyle" style='color:black; background-color:#D7BEFA;'>
                        Inbox
                    </DIV>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?op=sentMessages">
                    <DIV class="navBox navBoxStyle" style='color:black; background-color:#D7BEFA;'>
                        Sent Messages
                    </DIV>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?op=compose">
                    <DIV class="navBox navBoxStyle" style='color:black; background-color:#D7BEFA;'">
                        Compose Message
                    </DIV>
                </a>
            </li>
            <li class=" nav-item">
                        <a class="nav-link" href="?op=history">
                            <DIV class="navBox navBoxStyle" style='color:black; background-color:#D7BEFA ;'>
                                History
                            </DIV>
                        </a>
            </li>
        </ul>
    </DIV>

    <BR>
    <BR>

    <!-- DEBUGGING -->
    <DIV class="container">
        <?php
        switch ($op) {
            case "inbox":
                if (isset($_SESSION['uid'])) {
                    viewInbox($db, $_SESSION['uid']);
                } else {
                    logInError($db);
                }

                break;

            case "sentMessages":
                if (isset($_SESSION['uid'])) {
                    viewSent($db, $_SESSION['uid'], getName($db, $_SESSION['uid']));
                } else {
                    logInError($db);
                }

                break;

            case "compose":
                if (isset($_SESSION['uid'])) {
                    showMsgForm($db, $_SESSION['uid']);
                } else {
                    logInError($db);
                }

                break;

            case "history":
                if (isset($_SESSION['uid'])) {
                    showThreadForm($db, $_SESSION['uid']);
                } else {
                    logInError($db);
                }
                break;

            case "historyAndForm":
                if (isset($_SESSION['uid'])) {
                    showThreadForm($db, $_SESSION['uid']);

                    $rid = $_POST['fReceiver'];
                    showThread($db, $uid, $rid);
                } else {
                    logInError($db);
                }
                break;

            case "view":
                if (isset($_SESSION['uid'])) {
                    viewMsg($db, $mid, $_SESSION['uid']);
                } else {
                    logInError($db);
                }
                break;

            case "markUnread":
                if (isset($_SESSION['uid'])) {

                    $read = $_POST['read'];


                    markAsUnread($db, $_SESSION['uid'], $read);
                } else {
                    logInError($db);
                }
                break;

            case "mail":
                if (isset($_SESSION['uid'])) {
                    sendMsg($db, $_SESSION['uid'], $_POST);
                } else {
                    logInError($db);
                }
                break;



            default:
                logInError($db);
        }
        ?>
    </DIV>


</BODY>

</HTML>