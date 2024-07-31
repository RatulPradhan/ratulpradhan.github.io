<?php

include_once("db_connect.php");

print_r($_POST);

$op = $_GET['op'];

if ($op == 'add') {
    addTitan($db, $_POST);
}
else if ($op == 'delete') {
    deleteTitan($db, $_POST);
}
else if ($op == 'mail') {
    sendMail($db, $_POST);
}

// add a new titan specified in $data ($_POST)
function addTitan($db, $data) {

    $id     = $data['tfID'];
    $name   = $data['tfName'];
    $planet = $data['tfPlanet'];
    $power  = $data['tfPower'];

    $str1 = "INSERT INTO titan1 VALUE($id, '$name')";
    $str2 = "INSERT INTO titan2 VALUE($id, '$planet', '$power')";

    $res1 = $db->query($str1);
    $res2 = $db->query($str2);

    if ($res1 != FALSE && $res2 != FALSE) {
        header("refresh:5;url=titans.php");
        //header("Location: titans.php");
        printf("<P>Successfully added $name as new titan</P>\n");
    }
    else {
        printf("<P>Failed to add $name as new titan</P>\n");
        printf("<P>$str1</P>\n");
        printf("<P>$str2</P>\n");
    }

}

// $data has cbTitans: array of IDs
function deleteTitan($db, $data) {
    $ids = $data['cbTitans'];

    foreach ($ids AS $id) {
        $str1 = "DELETE FROM titan1 WHERE id=$id";
        $str2 = "DELETE FROM titan2 WHERE id=$id";

        $res1 = $db->query($str1);
        $res2 = $db->query($str2);

        if ($res1 != FALSE && $res2 != FALSE) {
            printf("<P>Successfully deleted $id</P>\n");
        }
        else {
            printf("<P>Failed to delete $id </P>\n");
            printf("<P>$str1</P>\n");
            printf("<P>$str2</P>\n");
        }
    }
}

// sendMail
function sendMail($db, $data) {
    $sid = $data['ddlSender'];
    $rid = $data['ddlReceiver'];
    $subject = $data['tfSubject'];
    $content = $data['taContent'];
    $sent = date("Y-m-d H:m:s");

    // no quotes around numeric types: $sid, $rid
    $str = "INSERT INTO message VALUE($sid, $rid, '$sent', '$subject', '$content')";

    //printf("<P>sendMail: $str</P>\n");

    $res = $db->query($str);

    if ($res != FALSE) {
        header("refresh:2;url=titans.php");
        printf("<P>Message sent successfully.</P>\n");
    }
    else {
        header("refresh:2;url=titans.php");
        printf("<P>Failed to sent your message.</P>\n");
    }
}


?>

