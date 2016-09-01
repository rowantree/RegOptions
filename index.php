<?php
    include_once("OpenDb.php");

    // get the users id from the request
    if (!isset($_REQUEST['ID']))
    {
        TraceMsg("index.php Your link is not complete. Please be sure to use the entire link you were given.");
        echo "<h2>Your link is not complete. Please be sure to use the entire link you were given.</h2>";
        exit;
    }
    $userid = $_REQUEST['ID'];
    TraceMsg("index.php userid=$userid");


    // Open the database and get the users name and email for verification
    $db = OpenPDO();
    $stmt = $db->prepare("SELECT r.firstName, r.lastName, r.email, p.kitchenSetup, p.postCleanup, p.ritualSupport FROM registration r INNER JOIN person p on p.regID=r.regID WHERE guid=:id");
    $stmt->bindValue(':id', $userid);
    ExecutePDO($stmt);

    $stmt->bindColumn(1, $firstName);
    $stmt->bindColumn(2, $lastName);
    $stmt->bindColumn(3, $email);
    $stmt->bindColumn(4, $kitchen);
    $stmt->bindColumn(5, $cleanup);
    $stmt->bindColumn(6, $ritual);

    if ( !$stmt->fetch(PDO::FETCH_BOUND) )
    {
        TraceMsg("index.php ERROR provided ID was not valid");
        echo "<h2>The id you provided is not valid</h2>";
        exit;
    }

    $init = "Reg.formData.kitchen=" . ($kitchen==1 ? 'true' : 'false');
    $init .= ";Reg.formData.cleanup=" . ($cleanup==1 ? 'true' : 'false');
    $init .= ";Reg.formData.ritual=" . ($ritual==1 ? 'true' : 'false');
    $init .= ";firstName='$firstName'";
    $init .= ";lastName='$lastName'";
    $init .= ";email='$email'";
    $init .= ";Reg.formData.userid='$userid'";

    TraceMsg("index.php firstName=$firstName lastName=$lastName email=$email kitchen=$kitchen cleanup=$cleanup ritual=$ritual");
    TraceMsg("index.php $init");


?>
<html ng-app="RegForm">
<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript" ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular-route.js" type="text/javascript"></script>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="RegForm.js"></script>
</head>
<body>
<div class="container">

    <h2>Twilight Covening : Registration Options</h2>

    <p>When you registered for Twilight Covening we asked a couple questions concerning your ability and desire to help support the weekend. Unfortunately we made a small mistake in the latest version of the registration process and your answers were not saved. Below are the three questions we asked. If you checked any of these boxes during the registration process please check them again now and click the 'Submit' button.</p>


    <div ng-controller="RegController as Reg" ng-init="<?php echo $init;?>" >

    Your Name: <span class="bg-info">{{firstName}} {{lastName}}</span><br>
    Your Email: <span class="bg-info">{{email}}</span>

        <form name="registerForm" class="form-horizontal form-small" action="update.php" method="post">

            <input type="text" name="guid" ng-model="Reg.formData.userid">

            <div class="checkbox"><label><input type="checkbox" name="kitchen" ng-model="Reg.formData.kitchen"/>I can volunteer for pre-event kitchen and setup work</label></div>
            <div class="checkbox"><label><input type="checkbox" name="cleanup" ng-model="Reg.formData.cleanup"/>I can stay for post event clean up and packing</label></div>
            <div class="checkbox"><label><input type="checkbox" name="ritual" ng-model="Reg.formData.ritual"/>I have previously attended several Covenings and would like to offer support for the Visioning Ritual on Sunday</label></div>

            <button class="btn btn-primary" type="submit">Submit</button>

        </form>
    </div>

</div>
</body>
</html>

