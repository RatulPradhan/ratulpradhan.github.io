<?php
session_start();

include("bootstrap.php");
include("util.php");


$op = $_GET['op'];
$sid = $_GET['sid'];
$scid = $_GET['scid'];
$password = $_GET['password'];
$eid = $_GET['eid'];


if ($op == "login") {

    if ((getNameStudent($db, $_POST['sid']) != "") && ($_POST['password'] == getPassStudent($db, $_POST['sid']))) {

        $_SESSION["sid"] = $_POST['sid'];
        $op = "";
    } else if ((getNameScheduler($db, $_POST['sid']) != "") && ($_POST['password'] == getPassScheduler($db, $_POST['sid']))) {

        $_SESSION["scid"] = $_POST['sid'];
        $op = "";

    } else {
        print('<P> WRONG LOGIN INFO, PLEASE TRY AGAIN </P>');
        $op = "";
    }
} else if ($op == "logout") {
    unset($_SESSION['sid']);
    unset($_SESSION['scid']);
    $op = "";
}
?>

<!DOCTYPE html>
<HTML>

<HEAD>
    <TITLE> Dashboard </TITLE>

    <link rel="preconnect" href=https://fonts.googleapis.com />

    <link href=https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap rel="stylesheet" />



    <LINK type="text/css" rel="stylesheet" href="styleScheduler.css" />



</HEAD>

<BODY>
    <DIV class="container">
        <DIV class="container text-center ">

            <DIV class="row mainHeading">
                <DIV class="col-md-12">
                    <H2>
                        <!-- <p class="navBoxStyle">
                        Space Scheduler
                    </p> -->

                        <?php
                        if (isset($_SESSION['sid'])) {
                            print("
                            
                            <img src='assets/logo.png' width='30%' />
                              ");
                            printf("<H2> Welcome %s! </H2>", getNameStudent($db, $_SESSION['sid']));
                        } else if (isset($_SESSION['scid'])) {
                            print("
                            
                            <img src='assets/logo.png' width='30%' />
                              ");
                            printf("<H2> Welcome %s! (SCHEDULER ACCESS)</H2>", getNameScheduler($db, $_SESSION['scid']));
                        } else {
                            print("
                            
                            <img src='assets/logo.png' width='30%' />
                              ");
                        }
                        ?>

                    </H2>
                </DIV>
            </DIV>
        </DIV>

        <DIV class="row">

            <DIV class="col align-self-center text-center">

                <?php
                // display login form
                if (!isset($_SESSION['sid']) && !isset($_SESSION['scid'])) {
                    printf("<FORM name='fmLogin' method='POST' action='?op=login'>\n");
                    printf("<INPUT class='navBoxStyle' style: type='text' name='sid' size='5' placeholder='user id' />\n");
                    printf("<INPUT class='navBoxStyle' type='password' name='password' size='7' placeholder='password' minlength='8' required />\n");
                    printf("<INPUT type='submit' value='login' />\n");
                    printf("</FORM>\n");
                } else { // display logout form
                    printf("<FORM name='fmLogout' method='POST' action='?op=logout'>\n");
                    printf("<INPUT class='logout' type='submit' value='logout' />\n");
                    printf("</FORM>\n");
                }
                ?>

            </DIV>
        </DIV>

        <ul class="nav justify-content-center" style="color: black ">
            <li class="nav-item ">

                <a class="nav-link active" href="?op=dashboard">
                    <DIV class="navBoxStyle" style='color:black; background-color:#D7BEFA;'>
                        Dashboard
                    </DIV>
                </a>



            <li class='nav-item'>
                <a class='nav-link' href='?op=addEventForm'>
                    <DIV class='navBoxStyle' style='color:black; background-color:#D7BEFA;'>
                        Schedule An Event
                    </DIV>
                </a>
            </li>


            </li>

             <?php

            if (isset($_SESSION['scid'])) {
                $str = "

            <li class='nav-item'>
                <a class='nav-link' href='?op=myEvents'>
                    <DIV class='navBoxStyle' style='color:black; background-color:#D7BEFA;'>
                        Check My Events
                    </DIV>
                </a>
            </li>";
            }

            

            if (isset($_SESSION['scid'])) {
                $str = "

                <li class='nav-item'>
                <a class='nav-link' href='?op=addDeleteLocations'>
                    <DIV class='navBoxStyle' style='color:black; background-color:#D7BEFA;'>
                        Add/Delete Locations
                    </DIV>
                </a>
                </li>

                <li class='nav-item'>
                <a class='nav-link' href='?op=flagged'>
                    <DIV class='navBoxStyle' style='color:black; background-color:#D7BEFA;'>
                        Flagged Events
                    </DIV>
                </a>
                </li>
                
                ";

                print($str);
            }

            ?>
        </ul>
    </DIV>




    <DIV class="container ">
        <?php

        switch ($op) {

            case "dashboard":
                dashboardForm($db);
                break;

            case "addEventForm":
                if (isset($_SESSION['sid'])) {
                    addEventForm();
                } else if (isset($_SESSION['scid'])) {
                    addEventFormScheduler();
                } else {
                    logInError($db);
                }
                break;


            case "addDeleteLocations":

                if (isset($_SESSION['scid'])) {
                    addDeleteLocationForm($db);
                } else {
                    logInError($db);
                }
                break;

            case "myEvents":

                if (isset($_SESSION['sid'])) {
                    checkEvents($db, $_SESSION['sid']);
                } else if (isset($_SESSION['scid'])) {
                    checkEvents($db, $_SESSION['scid']);
                } else {
                    logInError($db);
                }
                break;


            case "addLocation":

                if (isset($_SESSION['scid'])) {
                    addLocation($db, $_POST);
                } else {
                    logInError($db);
                }
                break;
            case "delLocation":

                if (isset($_SESSION['scid'])) {
                    delLocation($db, $_POST);
                } else {
                    logInError($db);
                }
                break;
            case "add":
                if (isset($_SESSION['sid'])) {
                    addEvent($db, $_POST, $_SESSION['sid']);
                } else if (isset($_SESSION['scid'])) {
                    addEvent($db, $_POST, $_SESSION['sid']);
                } else {
                    logInError($db);
                }
                break;

            case "deleteEventAction":
                if (isset($_SESSION['scid'])) {
                    deleteEventAction($db, $eid);
                } else {
                    logInError($db);
                }
                break;

            case "view":
                if (isset($_SESSION['sid']) || isset($_SESSION['scid'])) {
                    eventDetails($db, $eid);

                    if (isset($_SESSION['scid'])) {
                        deleteEventForm($db, $eid);
                    }

                } else {
                    logInError($db);
                }
                break;

            case "locationSearch":
                locationSearch($db);
                break;

            case "eventSearchForm":
                eventSearchForm($db);
                break;

            case "advEventSearchForm":
                eventSearchForm($db);
                printTable($db, $_POST, $op);

                break;

            case "locationSearchAndlocations":
                locationSearch($db);
                locationSearchResult($db, $_POST);
                break;

            case "flagged":
                if (isset($_SESSION['scid'])) {
                    flaggedEvents($db);
                } else {
                    logInError($db);
                }
                break;

            default:
                dashboardForm($db);

        }


        ?>
    </DIV>

</BODY>

</HTML>