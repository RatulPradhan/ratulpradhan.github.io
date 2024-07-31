<?php

include_once("db_connect.php");

function showLoginForm()
{
    printf("<FORM name='fmLogin' method='POST' action='?op=login'>\n");
    printf("<INPUT type='text' name='uid' size='5' placeholder='user id' />\n");
    printf("<INPUT type='submit' value='login' />\n");
    printf("</FORM>\n");
}

function showLogoutForm()
{
    printf("<FORM name='fmLogout' method='POST' action='?op=logout'>\n");
    printf("<INPUT type='submit' value='logout' />\n");
    printf("</FORM>\n");
}

function getName($db, $uid)
{

    $str = "SELECT name FROM titan1 WHERE id= $uid ";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['name'];
        return $name;
    } else {
        return "";
    }
}

//needs to be finished
function viewMsg($db, $msgID, $userID)
{

    $str = "SELECT sent, subject, content, t1.name AS sender, t2.name AS receiver"
        . " FROM message JOIN titan1 AS t1 ON t1.id=sid"
        . " JOIN titan1 AS t2 ON t2.id=rid"
        . " WHERE mid = $msgID AND (sid = $userID OR rid = $userID)";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $sent = $row['sent'];
        $subject = $row['subject'];
        $content = $row['content'];
        $sender = $row['sender'];
        $receiver = $row['receiver'];


        $msg =
            "<DIV class='row'>"
            . "<DIV class='col-md-3 navBoxStyle' style='background-color:#E8D5D3'>"
            . "<H3> From : $sender </H3>"
            . "<H3> To : $receiver </H3>"
            . "<H3> Date-Time : $sent </H3>"
            . "</DIV>"

            . "<DIV class='col-md-9 navBoxStyle' style='background-color:#FDF2CD'>"
            . "<H3> $subject </H3>"
            . "$content"
            . "</DIV>"
            . "</DIV>";

        print("$msg \n");
    } else {
        print("<P> Failed to View Message</P>");
    }
}

function viewInbox($db, $userID)
{

    $str = "SELECT t1.name, subject, sent, mid, seen"
        . " FROM message JOIN titan1 AS t1 ON t1.id = sid"
        . " WHERE rid = $userID"
        . " ORDER BY mid DESC";

    $res = $db->query($str);

    $msg = "
    <FORM name='fRead' method='POST' action='?op=markUnread&uid=$userID'>
    <TABLE class='table table-hover'>
        <THEAD class='thead-dark'>
        <TR class='emailStyle'>
            <th scope='col-md-2'>Sender</th>
            <th scope='col-md-6'>Subject</th>
            <th scope='col-md-3'>Sent</th>
            <th scope='col-md-1' style='text-align:center'><INPUT type='submit' value='Mark As Unread' /></th>

        </TR>
    </THEAD>
    <TBODY> 
    ";

    print("$msg \n");

    if ($res != FALSE) {

        while ($row = $res->fetch()) {

            $sender = $row['name'];
            $subject = $row['subject'];
            $sent = $row['sent'];
            $mid = $row['mid'];
            $seen = $row['seen'];

            

            $msg =
                " 
                <tr class='emailStyle'>
                        <td>$sender</td>
                        <td>
                        <a href='?op=view&mid=$mid'>
                        $subject
                        </a>
                        </td>
                        <td>$sent</td>
                        <td align='center'> <INPUT type='checkbox' name='read[]' value=$mid > </td>
                        
                    </tr>";
            print("$msg \n");
        }

        $msg = "</FORM>  
                </TBODY>
                </TABLE>";

    } else {
        print("<P> Failed to Show Inbox</P>");
    }
}

function markAsUnread($db, $uid, $checked)
{
    $i=0;
    foreach ($checked AS $ch) {
        $str = "UPDATE message
        SET seen=NULL
        WHERE mid = $ch AND rid = $uid";
        
        $res = $db->query($str);
        $i++;

    }

    if ($res!= FALSE) {
        printf("<DIV class='navBoxStyle'> Sucessfully marked $i messages as Unread \n ");
        header("refresh:3;url=dashboard.php?op=inbox");
    }
    else {
        print("<P> Failed to Mark as Unread</P>");
    }


}

function viewSent($db, $userID, $userName)
{

    $str = "SELECT t1.name, subject, sent, mid, seen"
        . " FROM message JOIN titan1 AS t1 ON t1.id = rid"
        . " WHERE sid = $userID"
        . " ORDER BY mid DESC";

    $res = $db->query($str);

    $msg = "
    <TABLE class='table table-hover'>
        <THEAD class='thead-dark'>
        <TR class='emailStyle'>
            <th scope='col-md-2'>Sender</th>
            <th scope='col-md-6'>Subject</th>
            <th scope='col-md-3'>Sent</th>
        </TR>
    </THEAD>
    <TBODY> ";

    print("$msg \n");

    if ($res != FALSE) {

        while ($row = $res->fetch()) {

            $sender = $row['name'];
            $subject = $row['subject'];
            $sent = $row['sent'];
            $mid = $row['mid'];

            $msg =
                " <tr class='emailStyle'>
                <td>$sender</td>
                <td>
                <a href='?op=view&mid=$mid'>
                $subject
                </a>
                </td>
                <td>$sent</td>
                
            </tr>";
            print("$msg \n");
        }

        $msg = " </TBODY>
                </TABLE> ";

    } else {
        print("<P> Failed to show Sent Messages</P>");
    }
}

function sendMsg($db, $senderID, $msgData)
{

    $sid = $senderID;
    $rid = $msgData['fReceiver'];
    $subject = $msgData['fSubject'];
    $content = $msgData['fContent'];
    

    $str = "INSERT INTO message(sid, rid, subject, content, sent) 
        VALUE($sid,$rid,'$subject', '$content', now() );";

    $res = $db->query($str);

    if ($res != FALSE) {
        header("refresh:3;url=?op=compose");
        printf("<P class='navBoxStyle'>Message Sent Successfully</P>\n");
    } else {
        printf("<P>Failed to Send Message </P>\n");
    }
}

function showMsgForm($db, $senderID)
{

    $getTitans = "SELECT id,name FROM titan1";
    $res = $db->query($getTitans);

    $str =
        "<FORM name='mail' method='POST' action='?op=mail'>
        <DIV class='row '>
            <DIV class='col-md-3 '>
                <H3 class='navBoxStyle' style='width:50%; background-color:#BEFACD'>TO: </H3>
                <SELECT name='fReceiver' class='navBoxStyle'>";

    print("$str \n");

    if ($res != FALSE) {

        while ($row = $res->fetch()) {

            $id = $row['id'];
            $name = $row['name'];

            $str = "<OPTION value='$id'>$name</OPTION>\n";
            print("$str \n");
        }

        $str =
            "</SELECT>     
            </DIV>
            <DIV class='col-md-9 navBoxStyle' style='padding:2rem ; background-color:#E3CAB8'>
                <INPUT class ='navBoxStyle' style='width: 90%' type='text' name ='fSubject' placeholder='SUBJECT'>
                </INPUT>
                <textarea class='navBoxStyle' name='fContent' style='background-color:antiquewhite; height:20rem; width:90%; background-color:antiquewhite' placeholder='Type message here'></textarea>
                </textarea>
                <INPUT type='submit' class = 'navBoxStyle' value='Send message' />
            </DIV>
        </FORM>";

        print("$str\n");

    }
    else{
        print("<P> Failed to show message form</P>");
    }
}
function showThreadForm($db, $myID)
{

    $getTitans = "SELECT id,name FROM titan1";
    $res = $db->query($getTitans);

    $str = "<DIV class='row navBoxStyle' style='background-color:antiquewhite'>
                    <DIV class='col-md-5 ' >
                        <H3> Show Message History With : </H3>
                    </DIV>
                    <FORM name='thread' method='POST' action='?op=historyAndForm&uid=$myID'>
                    <DIV class='col-md-3 '>
                        <SELECT name='fReceiver' class='navBoxStyle'>
                        <OPTION> </OPTION>";

    print("$str \n");

    if ($res != FALSE) {

        while ($row = $res->fetch()) {

            $id = $row['id'];
            $name = $row['name'];

            $str = "<OPTION value='$id'>$name</OPTION>\n";
            print("$str \n");
        }

        $str =
            "</SELECT> 
            </DIV>
            <DIV class='col-md-4 justify-content-end'>
            <INPUT type='submit' class='navBoxStyle' value='Send message' />
            </DIV>
             
             </FORM>
             </DIV>";

        print("$str \n");

    }
    else{
        print("<P> Failed to show users for history</P>");
    }




}

function showThread($db, $myID, $yourID)
{
    
    $str =
        "SELECT sid, t1.name AS sender, t2.name AS receiver, sent, subject, content
    FROM message JOIN titan1 AS t1 ON t1.id = sid
                JOIN titan1 AS t2 ON t2.id = rid
    WHERE (t1.id = $myID AND t2.id = $yourID) OR (t1.id=$yourID AND t2.id=$myID)
    ORDER BY sent DESC";

    $res = $db->query($str);

    if ($res != FALSE) {
        $exe =
            "<DIV class='container navBoxStyle' style='overflow:auto; width:100% ; height:40rem; background-color:antiquewhite'> ";

        print("$exe \n");
        while ($row = $res->fetch()) {
            $sid = $row['sid'];
            $sender = $row['sender'];
            $receiver = $row['receiver'];
            $sent = $row['sent'];
            $subject = $row['subject'];
            $content = $row['content'];

            if ($sid == $myID) {

                $exe =
                    "<DIV class='row justify-content-start' style='width: 50%;padding-left: 20px; margin-bottom:-30px'>
                    <p>From: $sender | To: $receiver | Sent: $sent </p>
                    </DIV>
                    <DIV class='row justify-content-start' style=' padding-bottom:0.7rem'>
                    <DIV class='col-md-6 navBoxStyle' style='  width: 50%; margin: 1rem; background-color:white' >
                    <H2> $subject </H2>
                         $content
                    </DIV>
                    </DIV>";
            } else {
                $exe =
                    "<DIV class='d-flex flex-row-reverse' style='margin-bottom:-40px'>
                    <p>From: $sender | To: $receiver | Sent: $sent </p>
                    </DIV>
                    <DIV class='row justify-content-end' style=' padding:0.7rem'>
                    <DIV class='col-md-6 navBoxStyle' style=' width: 50%; margin: 1rem; background-color:white' >
                      <H2> $subject </H2>
                      $content
                </DIV>
                </DIV>";
            }
            print("$exe \n");
        }

        $exe =
            "</DIV>";



        print("$exe \n");
    }
    else{
        print("<P> Failed to show History</P>");
    }


}
function logInError($db)
{
    
    $str =
        "<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> ";
            print("$str \n");

        $str=
        "<H3 style='text-align: center'> Please log in to use Services </H3>
        </DIV>";
        print("$str \n");

    

    

}




?>