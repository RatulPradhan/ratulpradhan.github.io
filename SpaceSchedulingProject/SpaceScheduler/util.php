<?php

include_once("db_connect.php");


//retreives Student Name given sid
function getNameStudent($db, $sid)
{
    $str = "SELECT fname FROM Student WHERE sid= $sid ";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['fname'];
        return $name;
    } else {
        return "";
    }
}

//retreives Scheduler Name given scid
function getNameScheduler($db, $scid)
{

    $str = "SELECT fname FROM Scheduler WHERE scid= $scid ";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['fname'];
        return $name;
    } else {
        return messagePrint("ERROR");
    }
}

//retreives Scheduler Password given scid 
function getPassScheduler($db, $scid)
{

    $str = "SELECT password FROM Scheduler WHERE scid= $scid ";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['password'];
        return $name;
    } else {
        return messagePrint("ERROR");
    }
}

//retreives Student password given sid

function getPassStudent($db, $sid)
{

    $str = "SELECT password FROM Student WHERE sid= $sid ";

    $res = $db->query($str);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['password'];
        return $name;
    } else {
        return messagePrint("ERROR");
    }
}

//retrieves a list of Events in the next month and displays it on the dashboard. No log in in required for this information.
function upcomingEvents($db)
{

    $dates = getNextDate();

    $str =
        "SELECT eid,event_name, start_time, end_time
    FROM Event 
    WHERE start_time BETWEEN '$dates[0]' AND '$dates[2]' 
    ORDER BY start_time";

    $res = $db->query($str);

    if ($res != FALSE) {
        $exe = "
        <DIV class = 'container navBoxStyle' style='overflow:auto;'>
        <H2> Upcoming Events </H2>";

        print("$exe \n");

        while ($row = $res->fetch()) {
            $name = $row['event_name'];
            $start_time = $row['start_time'];
            $end_time = $row['end_time'];
            $eid = $row['eid'];

            $exe = "<a href='?op=view&eid=$eid'>
            <DIV class = 'row navBoxStyle' style='color:black;width: 50%; background-color:#defad7'>
            <p>$name</p>
            </DIV>
            </a>
            ";

            print("$exe \n");

        }
        $exe = "</DIV>";
        print("$exe \n");
    } else {
        print("<P> Failed to show calendar form</P>");
    }

}

//displays main dashboard, conatining a function all to upcomingEvents() and a button for location and event search. Does not need login information for these functions.
function dashboardForm($db)
{

    $str = "<DIV class='container Hi'>
    <BR>
    <DIV class='row'>

    <DIV class='col-md-6'>";

    print("$str \n");

    print(upcomingEvents($db) . " </DIV> ");


    $str = "

    <DIV class='col-md-3 '>
            <a class='nav-link' href='?op=locationSearch'>
                <!-- change to navBoxStyleDashboardButton (isnt working for some reason rn) -->
                <DIV class='navBoxStyle' style='color:black; background-color:#defad7; font-size: 2rem;'>
                    <H2>Location Search </H2>
                </DIV>
            </a>
    </DIV>



        <DIV class='col-md-3 '>

            <a class='nav-link' href='?op=eventSearchForm'>
                <!-- change to navBoxStyleDashboardButton (isnt working for some reason rn) -->
                <DIV class='navBoxStyle' style='color:black; background-color:#defad7; font-size: 2rem;'>
                    <H2>Event Search </H2>
                </DIV>
            </a>
        </DIV>

    </DIV>

    <BR>

</DIV>";

    print("$str");

}

//displays the form to retrieve available locations 
function locationSearch($db)
{
    $str =
        "<DIV class='container navBoxStyle' style=' background-color:antiquewhite' > ";
    print("$str \n");

    $str =
        "<H2 style='text-align: center'> Search Location </H2>
        <H2 style='font-size:1.5rem; text-align: center'> If unkown, leave field blank</H3>
        <FORM name='fmEventSearch' method='POST' action = '?op=locationSearchAndlocations'>
        <INPUT type='number' name='tflid' placeholder='lid'> 
        <INPUT type='text' name='tfBuildingName' placeholder='Location Name' />
        <INPUT type='number' name='tfRoomNumber' placeholder='Room Number'>
        <INPUT type='number' name='tfCapacity' placeholder='Minumum Capacity'>
        <INPUT type='submit' class='navBoxStyleDashboardButton' value='Location Search' />  
        </FORM>
        

    </DIV>";
    print("$str \n");
}

//retrieves and displays locations based on post data from locationSearch() in a table format, 
function locationSearchResult($db, $data)
{

    $lid = $data['tflid'];
    $building = $data['tfBuildingName'];
    $room = $data['tfRoomNumber'];
    $capacity = $data['tfCapacity'];

    $str = "SELECT * FROM Location";

    // if lid not empty -> add to string 
    if (!(empty($lid))) {
        $str .= " NATURAL JOIN (SELECT * FROM Location WHERE lid = $lid ) AS X1 ";
    }

    // if building -> add to string
    if (!(empty($building))) {
        $str .= " NATURAL JOIN (SELECT * FROM Location WHERE building LIKE '%$building%') AS X2 ";
    }

    // if room -> add to string
    if (!(empty($room))) {
        $str .= " NATURAL JOIN (SELECT * FROM Location WHERE room_number = $room) AS X3 ";
    }

    // if capacity -> AND capacity >= x.
    if (!(empty($capacity))) {
        $str .= " NATURAL JOIN (SELECT * FROM Location WHERE capacity >= $capacity) AS X4 ";
    }

    // echo($str);
    $res = $db->query($str);

    // if res is not empty
    if (($res->rowCount()) != 0) {
        print("<DIV style='height: 500px; margin-top: 25px; overflow: scroll;'>\n");
        print("<TABLE class='table table-hover' border='2' cellspacing='10' text-align='center' cellpadding='15'>\n");
        print("<TR class='tableStyle'>\n");
        print("<TH class='tableStyle'>Location ID</TH>\n");
        print("<TH class='tableStyle'>Building Name</TH>\n");
        print("<TH class='tableStyle'>Room Number</TH>\n");
        print("<TH class='tableStyle'>Capacity</TH>\n");
        print("</TR>\n");

        while ($row = $res->fetch()) {
            $row_lid = $row['lid'];
            $row_building = $row['building'];
            $row_room = $row['room_number'];
            $row_capacity = $row['capacity'];

            print("<TR>\n");
            printf("<TD>$row_lid</TD>");
            printf("<TD>$row_building</TD>");
            printf("<TD>$row_room</TD>");
            printf("<TD>$row_capacity</TD>");
            print("</TR>\n");
        }
        print("</TABLE>\n");
        print("</DIV>\n");
    } else {
        printf(messagePrint("No Locations Matched"));
    }

}

//form for eventSearch functionality.
function eventSearchForm($db)
{
    print("<DIV class='container navBoxStyle' style=' background-color:antiquewhite' > \n");
    print("<H2 style='text-align: center'> Event Search </H2>\n");
    print("<H2 style='text-align: center'> Leave empty if unknown </H2>\n");

    print("<FORM name='fmEventSearch' method='POST' action='?op=advEventSearchForm'>\n");

    print("<INPUT class='navBoxStyle' type='text' value name='tfName' placeholder='Event Name' /> \n");
    print("<INPUT class='navBoxStyle' type='text' value name='tfBuilding' placeholder='Building Name' /> \n");
    print("<INPUT class='navBoxStyle' type='text' value name='tfLocationId' placeholder='Location ID' /> \n");

    print("<INPUT class='navBoxStyle' type='submit' value='Submit'/>\n");

    print("<H2>Order By: </H2>\n");
    print("<SELECT class='navBoxStyle' name='searchEventVariable'>\n");

    print("<OPTION value='NULL'>--Select--</OPTION>\n");
    print("<OPTION value='start_time'> Start Time </OPTION>\n");
    print("<OPTION value='end_time'> End Time </OPTION>\n");
    print("<OPTION value='event_name'> Name </OPTION>\n");
    print("<OPTION value='building'> Building </OPTION>\n");
    print("<OPTION value='lid'> Location ID </OPTION>\n");


    print("</SELECT>\n");



    print("</FORM>\n");
    print("</DIV> \n");

}




//helper function for printTable(). Returns string with ordering value depending on user selection in SQL format.
function getEventSQL($searchVar)
{
    if ($searchVar == 'NULL') {
        $str = "";
    } else if ($searchVar == 'start_time') {
        $str = " ORDER BY start_time DESC ";
    } else if ($searchVar == 'end_time') {
        $str = " ORDER BY end_time DESC";
    } else if ($searchVar == 'event_name') {
        $str = " ORDER BY event_name ";
    } else if ($searchVar == 'building') {
        $str = " NATURAL JOIN Location ORDER BY building";
    } else if ($searchVar == 'lid') {
        $str = " ORDER BY lid ";
    }
    return $str;
}


//retrieves and displays eventSearch results post data eventSearchForm() in a table format
function printTable($db, $data, $op)
{


    $event_name = $data['tfName'];
    $building = $data['tfBuilding'];
    $locationID = $data['tfLocationId'];
    $searchVar = $data['searchEventVariable'];

    messagePrint($building);
    $str = 'SELECT * FROM Event ';

    if (!(empty($event_name))) {
        $str .= "NATURAL JOIN (SELECT * FROM Event WHERE event_name  LIKE '%$event_name%' ) AS X1";
    }
    if (!(empty($building))) {
        $str .= "NATURAL JOIN (SELECT * FROM Event 
        NATURAL JOIN Location WHERE building  LIKE '%$building%') AS X2";
    }
    if (!(empty($locationID))) {
        $str .= "NATURAL JOIN (SELECT * FROM Event WHERE lid  = $locationID ) AS X3";

    }
    if (!(empty($searchVar))) {
        $str .= getEventSQL($searchVar);
    }

    $res = $db->query($str);

    if (($res->rowCount()) != 0) {

        print("<TABLE class='table table-hover' margin-top='10px' cellspacing='1' text-align='center' cellpadding='5'>\n");
        print("<TR>\n");
        print("<TH>Event ID</TH>\n");
        print("<TH>Location ID</TH>\n");
        print("<TH>Event Name</TH>\n");
        print("<TH>Start Time</TH>\n");
        print("<TH>End Time</TH>\n");
        print("<TH>Description</TH>\n");
        print("</TR>\n");

        while ($row = $res->fetch()) {
            $row_eid = $row['eid'];
            $row_lid = $row['lid'];

            if ($searchVar == 'building') {
                $row_building = $row['building'];
            }

            $row_event_name = $row['event_name'];
            $row_start_time = $row['start_time'];
            $row_end_time = $row['end_time'];
            $row_description = $row['description'];

            print("<TR>\n");

            printf("<TD>$row_eid</TD>");
            printf("<TD>$row_lid</TD>");

            if ($searchVar == 'building') {
                printf("<TD>$row_building</TD>");
            }

            printf("<TD>
            
            <a href='?op=view&eid=$row_eid'>\n
            $row_event_name
            
            </a>\n

            </TD>");
            printf("<TD>$row_start_time</TD>");
            printf("<TD>$row_end_time</TD>");
            printf("<TD>$row_description</TD>");
            printf("</a>");

            print("</TR>\n");
        }
        print("</TABLE>\n");
        print("</DIV> \n");

    } else {
        print("<H2 style='text-align: center'> No Events Found! </H2>\n");;
    }
}

//displays specific event information in it's entirety. Used by every table and upcoming events(). Is in text format for accescible copying
function eventDetails($db, $eid)
{
    $str = "SELECT eid, lid, event_name,date_requested, start_time, end_time, description, 
    CONCAT(fname,' ',lname) AS requester_name, building, room_number
    FROM Event 
            NATURAL JOIN Requests
            JOIN Student ON Student.sid = Requests.uid
            NATURAL JOIN Location
    WHERE eid = $eid
    ";

    $res = $db->query($str);

    if ($res != FALSE) {

        $row = $res->fetch();
        $lid = $row['lid'];
        $event_name = $row['event_name'];
        $date_requested = $row['date_requested'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $description = $row['description'];
        $requester_name = $row['requester_name'];
        $building = $row['building'];
        $room_number = $row['room_number'];

        $timestart = getDateSeperateTime($start_time);
        $timeend = getDateSeperateTime($end_time);

        $str = "<DIV class='navBoxStyle' style='padding:2rem ; background-color:#E3CAB8'>
        <H2>Event Name: $event_name</H2>
        <textarea class='navBoxStyle' name='fContent' style='font-size:1.5rem;background-color:antiquewhite; height:20rem; width:90%; background-color:antiquewhite' readonly>
Location: $building $room_number

Time: 
    From  $timestart[0], $timestart[1] 
    To      $timeend[0], $timeend[1]

Requested By: $requester_name

Description of Event: 
$description

        </textarea>
        </DIV>";

        print("$str\n");
    } else {
        $message = "Failed to Retrieve Event";
        printf(messagePrint($message), "\n");
    }
}

// form for deleting events
function deleteEventForm($db, $eid)
{
    print("<FORM class='navBoxStyle' style='background-color:antiquewhite' name='fmDeleteEvent' method='POST' action ='?op=deleteEventAction&eid=$eid'> \n");
    print("<H2 >Delete this Event with EID:</H2>\n");
    print("<INPUT class='navBoxStyle' type ='text' name='tfEid' value='$eid' readonly></INPUT>");
    print("<INPUT class= 'navBoxStyle' type='submit' value='Submit'");
}

//deletes selected event post data from deleteEventForm
function deleteEventAction($db, $eid)
{
    $str = "DELETE FROM Event WHERE eid = $eid;"
        . "DELETE FROM Requests WHERE eid = $eid;";

    $res = $db->query($str);

    if ($res != FALSE) {
        header("refresh:2;url=?op=dashboard");
        $message = "Sucessfully Deleted $eid";
        messagePrint($message);
    } else {
        header("refresh:2;url=?op=dashboard");
        $message = "Failed to Delete $eid";
        messagePrint($message);


    }
}

//form to add/schedule events. Sends sid automatically since this form is accessed by students only
function addEventForm()
{

    printf("");
    printf("<DIV class='container navBoxStyle'>");
    printf("<p> REQUEST EVENT FORM </p> \n");
    printf("<FORM name='fmAdd' method='POST' action='?op=add'> \n");
    printf("<INPUT type='text' name='tfName' placeholder='Event Name' /> \n");
    printf("<p> start time </p> \n");
    printf("<INPUT type='datetime-local' name='tfStart' /> \n");
    printf("<p> end time </p> \n");
    printf("<INPUT type='datetime-local' name='tfEnd' /> \n");
    printf("<INPUT type='number' name='tfLocation' placeholder='Location ID' /> \n");
    printf("<INPUT type='text' name='tfDescription' placeholder='Brief Event Description' /> \n");
    printf("<INPUT type='submit' value='Request Event' /> \n");
    printf("</FORM> \n");
    printf("</DIV>");


}

//form to add/schedule events by schedulers. Extra textfield where scheduler needs to specify which student sid to schedule events via.
function addEventFormScheduler()
{

    printf("");
    printf("<DIV class='container navBoxStyle'>");
    printf("<p> REQUEST EVENT FORM </p> \n");
    printf("<FORM name='fmAdd' method='POST' action='?op=add'> \n");
    printf("<INPUT type='text' name='tfName' placeholder='Event Name' /> \n");
    printf("<INPUT class ='navBoxStyle' type='text' name='tfID' placeholder='Requestor ID' /> \n");
    printf("<p> start time </p> \n");
    printf("<INPUT type='datetime-local' name='tfStart' /> \n");
    printf("<p> end time </p> \n");
    printf("<INPUT type='datetime-local' name='tfEnd' /> \n");
    printf("<INPUT type='number' name='tfLocation' placeholder='Location ID' /> \n");
    printf("<INPUT type='text' name='tfDescription' placeholder='Brief Event Description' /> \n");
    printf("<INPUT type='submit' value='Request Event' /> \n");
    printf("</FORM> \n");
    printf("</DIV>");


}

//uses POST data from addEventForms to add events into SQL databases Event and Requests. Calls checkEventClash() to check each added event for clashes (flag)
function addEvent($db, $data, $sid)
{
    if ($data['tfID'] != null) {
        $sid = $data['tfID'];
    }
    $event_name = $data['tfName'];
    $start_time = $data['tfStart'];
    $end_time = $data['tfEnd'];
    $lid = $data['tfLocation'];
    $description = $data['tfDescription'];


    $upDate = getNextDate();

    $str_location = "SELECT lid FROM Location WHERE lid = $lid";
    $res_location = $db->query($str_location);

    if ($start_time < $upDate[0]) {
        printf("<P>Invalid Start Date</P>");
    } else if ($res_location == Null) {
        // check for location AND date conflicts later
        printf("<P>Invalid Location</P>");
    } else {


        $str1 = "INSERT INTO Event (lid, event_name, start_time, end_time, flag, description, date_requested) 
        VALUES($lid,'$event_name', '$start_time', '$end_time', 0, '$description', '$upDate[0]');";

        $res1 = $db->query($str1);

        $str1 = "INSERT INTO Requests (eid, uid) VALUES(LAST_INSERT_ID(), $sid);";

        $res1 = $db->query($str1);

        $str3 = "SELECT MAX(eid) AS eid FROM Event ;";

        $res2 = $db->query($str3);
        $row = $res2->fetch();
        $eidNew = $row['eid'];

        $str3 = "SELECT start_time, end_time FROM Event WHERE eid = $eidNew";
        $res2 = $db->query($str3);
        $row = $res2->fetch();
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];

        
        checkEventClash($db,$eidNew, $lid, $start_time, "$end_time");

        if ($res1 != FALSE) {
            header("refresh:2;url=?op=dashboard");
            //printf("<P>Successfully added event request for $event_name</P>\n");
            $message = "Successfully added event request for $event_name";
            printf(messagePrint($message), "\n");
        } else {
            $message = "Failed to add event request for $event_name";
            printf(messagePrint($message), "\n");
        }
    }


}

//helper function to check whether event that is added has an conflicts with other events in terms of location and time. Flags events as 2 if conflict exists. Else flag remains 0
function checkEventClash($db, $event_eid, $event_lid, $event_start, $event_end)
{

    $str = "SELECT * FROM Event NATURAL JOIN Location WHERE lid = $event_lid AND eid != $event_eid AND flag = '0' ;";
    $res = $db->query($str);

    $clash_eid = NULL;

    if ($res != FALSE) {
        while ($row = $res->fetch()) {
            $event2_eid = $row['eid'];
            $event2_lid = $row['lid'];
            $event2_start = $row['start_time'];
            $event2_end = $row['end_time'];


            // string for clashes
            $str_flag = "UPDATE Event SET flag = 2 WHERE eid = $event_eid ";

            // flag if partial beginning overlap

            if (($event2_start < $event_start) && ($event2_end >= $event_start)) {
                
                $db->query($str_flag);
                $clash_eid = $event2_eid;
                messagePrint("Event has been flagged due to location conflict");

            }
            // flag if partial end overlap
            else if (($event2_start <= $event_end) && ($event2_end > $event_end)) {
                
                $db->query($str_flag);
                $clash_eid = $event2_eid;
                messagePrint("Event has been flagged due to location conflict");
            }

            // flag if complete overlap
            else if (($event2_start >= $event_start) && ($event2_end <= $event_end)) {
                
                $db->query($str_flag);
                $clash_eid = $event2_eid;
                messagePrint("Event has been flagged due to location conflict");

            }
            // flag if complete containment
            else if (($event2_start > $event_start) && ($event2_end < $event_end)) {
                
                $db->query($str_flag);
                $clash_eid = $event2_eid;
                messagePrint("Event has been flagged due to location conflict");
            }

        }
    }

    return array($event_eid, $clash_eid);

}





//helper function that gives the current DateTime, DateTime + 1 day, DateTime + 1 month in an array format
function getNextDate()
{
    $today_date = date("Y-m-d h:i:s");
    $tomorrow_date = date("Y-m-d h:i:s", strtotime("+1 Day"));
    $month_date = date("Y-m-d h:i:s", strtotime("+1 Month"));

    $date_array = array($today_date, $tomorrow_date, $month_date);

    return $date_array;
}

function getDateSeperateTime($dateTime)
{
    $dt = new DateTime($dateTime);

    $date = $dt->format('m/d/Y');
    $time = $dt->format('H:i:s A');

    $dateSeperateTime = array($date, $time);

    return $dateSeperateTime;

}

//displays message for any log in based error
function logInError($db)
{

    $str =
        "<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> ";
    print("$str \n");

    $str =
        "<H3 style='text-align: center'> Please log in to use Services </H3>
        </DIV>";
    print("$str \n");
}

// generic Message Printer for debugging
function messagePrint($message)
{
    $str =
        "<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> ";
    print("$str \n");

    $str =
        "<H2 style='text-align: center'> $message </H2>
        </DIV>";
    print("$str \n");

}

//form for adding and deleting locations
function addDeleteLocationForm($db)
{
    print("<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> \n");
    print("<H2 style='margin-top: 10px; margin-bottom: 10px;'> Add Location: </H2> \n");
    print("<FORM name='fmAddLocation' method='POST' action='?op=addLocation'>\n");
    print("<INPUT class='navBoxStyle' type='text' name='tfBuilding' placeholder='Building Name'/>\n");
    print("<INPUT class='navBoxStyle' type='text' name='tfRoomNumber' placeholder='Room Number'/>\n");
    print("<INPUT class='navBoxStyle' type='text' name='tfCapacity' placeholder='Room Capacity'/>\n");
    print("<INPUT class='navBoxStyle' type='submit' value='Add'/>\n");
    print("</FORM>\n");

    print("<BR>\n");

    print("<H2 style='margin-top: 10px; margin-bottom: 10px;'> Delete Location: </H2> \n");

    $str = "SELECT * FROM Location ORDER BY building";

    $res = $db->query($str);

    if ($res != FALSE) {
        print("<FORM name='fmDelLocation' method='POST' action='?op=delLocation'>\n");
        print("<DIV style='height: 500px; overflow: scroll;'>\n");
        print("<TABLE class='table table-hover' >\n");
        print("<THEAD class='thead-dark'>");
        print("<TR>\n");
        print("<TH>Location ID</TH>\n");
        print("<TH>Building</TH>\n");
        print("<TH>Room Number</TH>\n");
        print("<TH>Capacity</TH>\n");
        print("<TH><INPUT type='submit' value='Delete Selected Locations'/></TH>\n");
        print("</TR>\n");
        print("</THEAD \n>");

        while ($row = $res->fetch()) {

            $lid = $row['lid'];
            $building = $row['building'];
            $room_number = $row['room_number'];
            $capacity = $row['capacity'];

            print("<TR>\n");
            printf("<TD>$lid</TD>");
            printf("<TD>$building</TD>");
            printf("<TD>$room_number</TD>");
            printf("<TD>$capacity</TD>");
            printf("<TD><INPUT type='checkbox' name='cbDelLocations[]' value='$lid'/></TD>");
            print("</TR>\n");
        }

        print("</TABLE>\n");
        print("</DIV>\n");
        print("</FORM>\n");

        print("</DIV>");

    } else {
        print("Error Getting Locations");
    }

}

//uses POST data from addDeleteLocations to delete location from database
function delLocation($db, $data)
{
    $delLocations = $data['cbDelLocations'];

    foreach ($delLocations as $lid) {
        $str = "DELETE FROM Location WHERE lid = $lid";

        $res = $db->query($str);

        if ($res != FALSE) {
            header("refresh:3;url=?op=addDeleteLocations");
            printf("<P>Successfully deleted $lid</P>\n");
        } else {
            printf("<P>Failed to delete $lid </P>\n");
            printf("<P>$str</P>\n");
        }
    }
}

//uses POST data from  addDeleteLocation to add location from databases
function addLocation($db, $data)
{

    $building = $data['tfBuilding'];
    $room_number = $data['tfRoomNumber'];
    $capacity = $data['tfCapacity'];

    if (empty($building) || empty($room_number) || empty($capacity)) {
        header("refresh:2;url=?op=dashboard");
        $message = "Please make sure all values are filled!";
        printf(messagePrint($message), "\n");
    } else {
        $str = "INSERT INTO Location (building, room_number, capacity) 
          VALUES('$building',$room_number, $capacity) ";

        $res = $db->query($str);


        if ($res != FALSE) {
            header("refresh:2;url=?op=dashboard");
            $message = "Successfully added building $building, Room Number $room_number";
            printf(messagePrint($message), "\n");
        } else {
            header("refresh:2;url=?op=dashboard");
            $message = "Failed to add building $building, Room Number $room_number";
            printf(messagePrint($message), "\n");
        }
    }

}

//displays events scheduled by particular user. 
function checkEvents($db, $uid)
{


    $str = "SELECT * FROM Requests NATURAL JOIN Event NATURAL JOIN Location WHERE uid = $uid ORDER BY date_requested;";
    $res = $db->query($str);


    print("<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> \n");
    print("<H2 style='margin-top: 10px; text-align: center; margin-bottom: 10px;'> My Events </H2> \n");

    if ($res != FALSE) {
        print("<TABLE class='table table-hover' >\n");
        print("<THEAD class='thead-dark'>");
        print("<TR>\n");
        print("<TH>Date Requested</TH>\n");
        print("<TH>Event ID</TH>\n");
        print("<TH>Location ID</TH>\n");
        print("<TH>Building</TH>\n");
        print("<TH>Room Number</TH>\n");
        print("<TH>Event Name</TH>\n");
        print("<TH>Start Time</TH>\n");
        print("<TH>End Time</TH>\n");
        print("<TH>Description</TH>\n");
        print("<TH>Flag</TH>\n");
        // print("<TH><INPUT type='submit' value='Cancel Event'/></TH>\n");
        print("</TR>\n");
        print("</THEAD \n>");

        while ($row = $res->fetch()) {

            $row_eid = $row['eid'];
            $row_lid = $row['lid'];
            $row_building = $row['building'];
            $row_room_number = $row['room_number'];
            $row_event_name = $row['event_name'];
            $row_flag = $row['flag'];
            $row_date_requested = $row['date_requested'];
            $row_start_time = $row['start_time'];
            $row_end_time = $row['end_time'];
            $row_description = $row['description'];



            print("<TR>\n");
            printf("<TD>$row_date_requested</TD>");
            printf("<TD>$row_eid</TD>");
            printf("<TD>$row_lid</TD>");
            printf("<TD>$row_building</TD>");
            printf("<TD>$row_room_number</TD>");

            printf("
            <TD>

            <a href='?op=view&eid=$row_eid'>\n

            $row_event_name
            
            </a>\n
            </TD>");



            printf("<TD>$row_start_time</TD>");
            printf("<TD>$row_end_time</TD>");
            printf("<TD>$row_description</TD>");

            if ($row_flag == 0) {
                printf("<TD><strong>Approved</strong></TD>");
            } else {
                printf("<TD><strong>Flagged</strong></TD>");
            }

            print("</TR>\n");
        }
        print("</TABLE>\n");
    } else {

        print("<H4 style='margin-top: 10px; margin-bottom: 10px; text-align: center'> You Have Not Requested Any Events Yet! </H2> \n");
    }
    print("</DIV> \n");


}

//Scheduler function. Displays all events that may be flagged.
function flaggedEvents($db)
{
    $str = "
        SELECT * FROM Requests NATURAL JOIN Event NATURAL JOIN Location WHERE flag !=0 ORDER BY date_requested;";

    $res = $db->query($str);

    print("<DIV class='container navBoxStyle' style=' background-color:antiquewhite'> \n");
    print("<H2 style='margin-top: 10px; text-align: center; margin-bottom: 10px;'> Flagged Events </H2> \n");


    if ($res != FALSE) {
        print("<TABLE class='table table-hover' >\n");
        print("<THEAD class='thead-dark'>");
        print("<TR>\n");
        print("<TH>Date Requested</TH>\n");
        print("<TH>Event ID</TH>\n");
        print("<TH>Location ID</TH>\n");
        print("<TH>Building</TH>\n");
        print("<TH>Room Number</TH>\n");
        print("<TH>Event Name</TH>\n");
        print("<TH>Start Time</TH>\n");
        print("<TH>End Time</TH>\n");
        print("<TH>Description</TH>\n");
        print("<TH>Flag</TH>\n");
        print("</TR>\n");
        print("</THEAD \n>");

        while ($row = $res->fetch()) {

            $row_eid = $row['eid'];
            $row_lid = $row['lid'];
            $row_building = $row['building'];
            $row_room_number = $row['room_number'];
            $row_event_name = $row['event_name'];
            $row_flag = $row['flag'];
            $row_date_requested = $row['date_requested'];
            $row_start_time = $row['start_time'];
            $row_end_time = $row['end_time'];
            $row_description = $row['description'];



            print("<TR>\n");
            printf("<TD>$row_date_requested</TD>");
            printf("<TD>$row_eid</TD>");
            printf("<TD>$row_lid</TD>");
            printf("<TD>$row_building</TD>");
            printf("<TD>$row_room_number</TD>");

            printf("
                <TD>
    
                <a href='?op=view&eid=$row_eid'>\n
    
                $row_event_name
                
                </a>\n
                </TD>");



            printf("<TD>$row_start_time</TD>");
            printf("<TD>$row_end_time</TD>");
            printf("<TD>$row_description</TD>");

            if ($row_flag == 0) {
                printf("<TD><strong>Approved</strong></TD>");
            } else {
                printf("<TD><strong>Flagged</strong></TD>");
            }

            print("</TR>\n");
        }
        print("</TABLE>\n");
    } else {

        print("<H4 style='margin-top: 10px; margin-bottom: 10px; text-align: center'> You Have Not Requested Any Events Yet! </H2> \n");
    }
    print("</DIV> \n");
}





?>