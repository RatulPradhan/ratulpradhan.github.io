<!doctype html>

<?php
    include_once("db_connect.php");
?>

<HTML>
<HEAD>
<TITLE>Titans: php-mysql</TITLE>
</HEAD>

<BODY>
<H1>Titans: php-mysql</H1>

<!-- goal: display titans, 
           be able to add a new titan,
           be able to delete multiple "checked titans" -->

<!-- add new titan form -->
<FORM name="fmAdd" method="POST" action="titansUtil.php?op=add">
<INPUT type="text" name="tfID" placeholder="id" />
<INPUT type="text" name="tfName" placeholder="name" />
<INPUT type="text" name="tfPlanet" placeholder="planet" />
<INPUT type="text" name="tfPower" placeholder="power" />
<INPUT type="submit" value="Add new titan" />
</FORM>

<FORM name="fmDel" method="POST" action="titansUtil.php?op=delete">
<TABLE border="1" cellspacing="0" cellpadding="5">
<TR>
<TH>id</TH>
<TH>name</TH>
<TH>planet</TH>
<TH>power</TH>
<TH>sent</TH>
<TH>received</TH>
<TH><INPUT type="submit" value="Delete checked titans" /></TH>
</TR>

<!--
Query to show complete information for each titan.

SELECT titan1.id, name, planet, power, nSent, nReceived
FROM  (titan1 NATURAL JOIN (SELECT   id, COUNT(rid) AS nSent
                            FROM     titan1 LEFT OUTER JOIN message ON id=sid
                            GROUP BY id) AS S
              NATURAL JOIN (SELECT   id, COUNT(sid) AS nReceived
                            FROM     titan1 LEFT OUTER JOIN message ON id=rid
                            GROUP BY id) AS R)
              LEFT OUTER JOIN titan2 ON titan1.id=titan2.id

-->

<?php
// retrieve all titans from titan1 and titan2


$str = "SELECT titan1.id, name, planet, power, nSent, nReceived "
		."FROM  (titan1 NATURAL JOIN (SELECT   id, COUNT(rid) AS nSent "
                .            "FROM     titan1 LEFT OUTER JOIN message ON id=sid "
                 .           "GROUP BY id) AS S "
              ."NATURAL JOIN (SELECT   id, COUNT(sid) AS nReceived "
                 .           "FROM     titan1 LEFT OUTER JOIN message ON id=rid "
                  .          "GROUP BY id) AS R) "
              ."LEFT OUTER JOIN titan2 ON titan1.id=titan2.id ";

$res = $db->query($str);

$titans = array();

if ($res != FALSE) {

    printf("<P>data has %d rows and %d columns</P>\n",
           $res->rowCount(), $res->columnCount());

    $i = 0;
    while ($row = $res->fetch()) {
        $id     = $row['id'];
        $name   = $row['name'];
        $planet = $row['planet'];
        $power  = $row['power'];
        $nSent = $row['nSent'];
        $nReceived = $row['nReceived'];
        $titans[$i] = array($id, $name);
        ++$i;

        // create a string with 1 HTML row
        $tr = "<TR>"
            . "<TD>$id</TD>"
            . "<TD>$name</TD>"
            . "<TD>$planet</TD>"
            . "<TD>$power</TD>"
            ."<TD>$sent</TD>"
            ."<TD>$received</TD>"
            . "<TD><INPUT type='checkbox' name='cbTitans[]' value='$id' /></TD>"
            . "</TR>";

        printf("$tr\n");
    }

    //echo "<PRE>\n";
    //print_r($titans);
    //echo "</PRE>\n";

}
else {
    printf("<P>Error executing query: $str</P>\n");
}

?>

</TABLE>
</FORM>

<BR />
<HR />

<!-- message section -->
<H3>Send Mail to a Titan</H3>

<FORM name="fmMail" method="POST" action="titansUtil.php?op=mail">

<b>Sender</b>
<SELECT name="ddlSender">

<?php
    for ($i = 0; $i < count($titans); ++$i) {
        $id   = $titans[$i][0];
        $name = $titans[$i][1];
        $str = "<OPTION value='$id'>$name</OPTION>\n";

        echo $str;
    }
?>

</SELECT>

&nbsp; &nbsp; &nbsp; &nbsp;

<b>Recipient</b>
<SELECT name="ddlReceiver">
<?php
    for ($i = 0; $i < count($titans); ++$i) {
        $id   = $titans[$i][0];
        $name = $titans[$i][1];
        $str = "<OPTION value='$id'>$name</OPTION>\n";

        echo $str;
    }
?>

</SELECT>

<BR />
<BR />
<INPUT type="text" name="tfSubject" placeholder="mail subject" />
<BR />
<BR />
<TEXTAREA name="taContent" rows="10" cols="50">
</TEXTAREA>
<BR />
<BR />
<INPUT type="submit" value="Send message" />
</FORM>

</BODY>
</HTML>


