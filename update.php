<?php
    include_once("OpenDb.php");

    TraceMsg("update.php " . json_encode($_REQUEST));

    if (!array_key_exists('guid', $_REQUEST))
    {
        TraceMsg("update.php ERROR: No GUID was provided");
        echo "<h2>Invalid Request";
        exit;
    }

    $guid = $_REQUEST['guid'];

    if ( $guid == '' )
    {
        TraceMsg("GUID is empty");
        echo "<h2>Invalid Request";
        exit;
    }




    $kitchen = ISSET($_REQUEST['kitchen']) && $_REQUEST['kitchen'] == 'on' ? 1 : 0;
    $cleanup = ISSET($_REQUEST['cleanup']) && $_REQUEST['cleanup'] == 'on' ? 1 : 0;
    $ritual = ISSET($_REQUEST['ritual']) && $_REQUEST['ritual'] == 'on' ? 1 : 0;

    TraceMsg("update.php guid=$guid kitchen=$kitchen cleanup=$cleanup ritual=$ritual");

    $db = OpenPDO();

    $stmt = $db->prepare("SELECT regID FROM registration WHERE guid=:id");
    $stmt->bindValue(':id', $guid);
    ExecutePDO($stmt);
    $stmt->bindColumn(1, $regID);
    if ( !$stmt->fetch(PDO::FETCH_BOUND) )
    {
        TraceMsg("update.php ERROR: Provided GUID could not be found");
        echo "<h2>The id you provided is not valid</h2>";
        exit;
    }

    TraceMsg("update.php regID=$regID");

    $stmt = $db->prepare("UPDATE person SET kitchenSetup=:kitchen, postCleanup=:cleanup, ritualSupport=:ritual WHERE regID=:regID");
    $stmt->bindValue(':regID', $regID);
    $stmt->bindValue(':kitchen', $kitchen);
    $stmt->bindValue(':cleanup', $cleanup);
    $stmt->bindValue(':ritual', $ritual);
    ExecutePDO($stmt);


    TraceMsg("update.php Done");

    echo "<h2>Thank you. Your options have been saved!</h2>"

?>