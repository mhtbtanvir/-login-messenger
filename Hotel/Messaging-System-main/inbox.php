<?php 
    /**** inbox page ****/
    // Requires edit to get the user username

    session_start();
    // DB connection
    include_once 'db.config.php'; 
  
    /********** To Be Edited ************/
    
    /* NOTICE: Edit variable $user to be equal to the user username using your session or other variable */
    /* DO NOT DELETE VARIABLE $user */
    $user = $_SESSION["username"];
        
    /************************************/

    // if not logged in
    if(empty($user)){
        echo "<h1>You must login first</h1>";
        die();
    }
    
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 

    <style>
        .container{
            margin-top:3%;
            font-family:arial;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
            padding:20px
        }
        th, td {
            padding: 16px;
        }
        tr:hover{
            background-color:#eee;
        }
        tr{
            border:1px solid #ddd;
        }
        .title{
            font-size:20px;color:#17075e;font-family:Lucida Sans Unicode;
        }
        .msg{
            line-height:1.8;
            white-space: pre-wrap;
            font-size:17px;
        }
        .sender{
            color:#666;
            font-size:16px;
        }
        .date{
            font-size:14px;float:right
        }
        .click{
            color:black;
            display:block;
        }
        .click:hover{
            color:black;
            text-decoration:none;
        }
        .new{
            background-color:red;
            border-radius:15px;
            color:white;
            font-size:14px;
            padding-right:8px;
            padding-left:8px;
        }
        body {
            background-image: url('https://images.pexels.com/photos/66284/winter-nature-season-trees-66284.jpeg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center; /* Centers the image */
            height: 100vh; /* Ensures the body takes full height */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-sm-12" style="overflow-wrap: break-word;">
          <div><h1>Received Messages</h1></div><br>

          <!-- Check for current page name -->
          <div style="float:left;font-size:22px"><a href="index.php">Send message</a> |&nbsp;</div>
          <?php if(basename($_SERVER['PHP_SELF']) == 'inbox.php'){?>
              <div style="float:left;font-size:22px"><b><a href="inbox.php">Inbox</a></b> |&nbsp;</div>
          <?php } ?>
          <div style="float:left;font-size:22px"><a href="outbox.php">Outbox</a></div>
              
          <table>
              <thead>
              <!-- Table head -->
              <tr style="text-align:center;color:red">
                  <th>Inbox</th>
              </tr>
              
              <?php
              // if you want to display all messages remove =>  LIMIT 15  after DESC in the next line
              $sql = "SELECT * FROM messages WHERE send_to = '$user' OR send_to = 'all' ORDER BY id DESC LIMIT 15";
              $result = mysqli_query($conn, $sql);
              
              // if no messages received
              if(mysqli_num_rows($result) == 0){
                echo '<tr style="font-size:20px"><td>No messages received yet</tr></td>';
                die();
              }
              echo '<div style="float:right">Total results: '.mysqli_num_rows($result).'</div>';

              while($row = mysqli_fetch_assoc($result)){
                $msg = $row['message'];

                // if message public
                if($row['send_to'] == 'all' || $row['send_to'] == $user){
                    // Check if public message has been seen
                    if ($row['public'] == 1) {
                        $sql_public = "SELECT * FROM public_seen WHERE id = ".$row['id']." AND user = '$user'";
                        $result_public = mysqli_query($conn, $sql_public);
                        $isRead = mysqli_num_rows($result_public) > 0;

                        // Display message
                        echo "
                        <tr>
                          <td style='max-width:400px; background-color:#eee'>
                            <span class='date'>".$row['send_on']."</span>
                            <span class='title'>".$row['title'].($isRead ? "" : "&nbsp;&nbsp;<label class='new'>New</label>")."</span>
                            <br><br>
                            <span class='msg'>$msg</span>
                            <span class='sender'>Sent from ".$row['send_from']."</span>
                            <a href='index.php?reply=".$row['send_from']."&subject=".$row['title']."' title='Reply' style='float:left;text-decoration:none'><i class='fa fa-reply'></i>&nbsp;</a>
                          </td>
                        </tr>";

                        // Mark public message as seen
                        if (!$isRead) {
                            $sql_public = "INSERT INTO public_seen (id, user) VALUES (".$row['id'].", '$user')";
                            mysqli_query($conn, $sql_public);
                        }
                    } else { // Handle personal messages
                        if ($row['seen'] == 0) {
                            echo "
                            <tr>
                              <td style='max-width:400px;background-color:#eee'>
                                <span class='date'>".$row['send_on']."</span>
                                <span class='title'>".$row['title']."&nbsp;&nbsp;<label class='new'>New</label></span>
                                <br><br>
                                <span class='msg'>$msg</span>
                                <span class='sender'>Sent from ".$row['send_from']."</span>
                                <a href='index.php?reply=".$row['send_from']."&subject=".$row['title']."' title='Reply' style='float:left;text-decoration:none'><i class='fa fa-reply'></i>&nbsp;</a>
                              </td>
                            </tr>";
                            // Update seen status for personal messages
                            $update = "UPDATE messages SET seen = 1 WHERE id = ".$row['id'];
                            mysqli_query($conn, $update);
                        } else {
                            echo "
                            <tr>
                              <td style='max-width:400px'>
                                <span class='date'>".$row['send_on']."</span>
                                <span class='title'>".$row['title']."</span>
                                <br><br>
                                <span class='msg'>$msg</span>
                                <span class='sender'>Sent from ".$row['send_from']."</span>
                                <a href='index.php?reply=".$row['send_from']."&subject=".$row['title']."' title='Reply' style='float:left;text-decoration:none'><i class='fa fa-reply'></i>&nbsp;</a>
                              </td>
                            </tr>";
                        }
                    }
                }
              }
            ?>
            </table>
          <br><br>
        </div>
      </div> 
    <br>
  </body>
</html> 

<!-- New message badge fade out script -->
<script>
    setTimeout(function() {
        $(".new").fadeOut();
    }, 4000);
</script>
