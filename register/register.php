<?php

header("Access-Control-Allow-Origin: website.com");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

//if (isset($_POST['submit'])) {

    $xArray= array();
    $xIndex= 0;
    $hostname = 'website.com';
    $username = '';
    $dbPassword = '';
    $dbname = '';

    $apiEndpoint = "https://api.openai.com/v1/moderations";
    $apiKey = "";
    $apiResponse;


    $conn = mysqli_connect($hostname, $username, $dbPassword, $dbname) or die("Couldn't connect to MySQL: " . mysqli_connect_error());

    // Sanitize and validate input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $passwordConfirm = mysqli_real_escape_string($conn, $_POST['passwordConfirm']);

    $userLengthOK = true;
    $userCharOK = true;
    $userAppropriateOK = true;
    $userDupeOK = true;
    $emailOK = true;
    $emailDupeOK = true;
    $passOK = true;
    $passConfOK = true;

    
    //username check
    if (strlen($username) < 3) {
        //echo "xU";
        $xArray[$xIndex]="xUL";
        $xIndex++;
        $userLengthOK = false;
    
    }  
    if ((preg_match('/^[a-zA-Z0-9]+$/', $username))==0) {
        $xArray[$xIndex]="xUC";
        $xIndex++;
        $userCharOK = false;
    } 

    
   if ($userLengthOK && $userCharOK) {

    $sql="SELECT * FROM users WHERE displayname = '" . mysqli_real_escape_string($conn, $username) . "'";
    $result = mysqli_query($conn,$sql);
    //if($result){}else{echo("xNF");}

        if (mysqli_num_rows($result) == 1) {
            $xArray[$xIndex]="xUD";
            $xIndex++;
            $userDupeOK = false;
        }
   }
    
    //email check
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //echo "xE";
        $xArray[$xIndex]="xE";
        $xIndex++;
        $emailOK = false;
    }  

    if ($emailOK) {

        $sql="SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
        $result = mysqli_query($conn,$sql);
        //if($result){}else{echo("xNF");}
    
            if (mysqli_num_rows($result) == 1) {
                $xArray[$xIndex]="xED";
                $xIndex++;
                $emailDupeOK = false;
            }
       }
    
    //password check
    if (strlen($password) < 8) {
        //echo "xP";
        $xArray[$xIndex]="xP";
        $xIndex++;
        $passOK = false;
    }  
    if ($password != $passwordConfirm) {
        //echo $password . " - " . $passwordConfirm . ',a';
        $xArray[$xIndex]="xPM"; 
        $xIndex++;
        $passConfOK = false;
    }  

    if ($userLengthOK&&$userCharOK){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/moderations');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey ,
        ]);
    
        $inputValue = $username;
        $data = array("input" => $inputValue);
        $jsonString = json_encode($data);

        //$curlInput = '{input=" . $username ."}';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);

        $response = curl_exec($ch);
        //$xArray[$xIndex]=$response; 
        //$xIndex++;
        
        $flaggedIndex = strpos($response,"flagged");
        $flaggedIndex+=9;

        $isFlagged = (substr($response,($flaggedIndex),1));
        //$xArray[$xIndex]=$isFlagged; 
        //$xIndex++;

        $manualFlag = false;

        $badWords =
         ['insert bad words'];

            foreach($badWords as $foul){
                if(strpos($username,$foul)>-1){
                    $manualFlag = true;
                }
            }
        
        if($isFlagged == "t" || $manualFlag){
             //echo "xP";
            $xArray[$xIndex]="xUA";
            $xIndex++;
            $userAppropriateOK = false;
              //echo("TRUE");
        }else{
            $userAppropriateOK = true;
        }
        curl_close($ch);
        //echo($response);
    }
    

   //}
    if($userLengthOK && $userCharOK && $emailOK && $passOK && $passConfOK && $userAppropriateOK && $userDupeOK && $emailDupeOK ){
        // Hash the password
        $hashbrown = password_hash($password, PASSWORD_DEFAULT);

        // Insert the sanitized data into the database
        $sql = "INSERT INTO `users`(`displayname`, `email`, `passwordhash`) VALUES ('$username', '$email', '$hashbrown')";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            $xArray[$xIndex]="success"; 
            $xIndex++;
            //echo 'Entry Success!!';
        } else {
            $xArray[$xIndex]="Query failed: " . mysqli_error($conn); 
            $xIndex++;
            //echo "Query failed: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
    $return="";
    for($i=0;$i<count($xArray);$i++){
        if($i==count($xArray)-1){
            $return=$return . $xArray[$i];
        }else{
            $return=$return . $xArray[$i] . ",";
        }
    }
    echo $return;

/*} else {
    echo('SERVER FAILED! ');
    echo(isset($_POST['submit']));
}*/
?>
