<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/2.14.1/moment.min.js"></script>
    <script src="scripts.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel='stylesheet' type='text/css' href='styles.css'>
</head>

<body>
    <div class="sticky" style="z-index: 1;">
        <div class="navbar">
            <a href=".">Home</a>
            <?php
                if(isset($_COOKIE['user']) && $_COOKIE['user'] != '')
                    echo "
                    <a href='stats.php'>Statistics</a>
                    <a href='.' onclick='signOut()' style='float: right;'>Sign out</a>
                    ";
            ?>
            <script>
            </script>
        </div>
    </div>
    <center>
        <div style='margin-top:100px;'>
        <?php
            if((!isset($_COOKIE['user']) || $_COOKIE['user'] == ''))
                return;
            $user = $_COOKIE['user'];
            $query = "SELECT count(*),
            SUM(IF(imageRelated != 0 AND textRelated = 0, 1, 0)),
            SUM(IF(imageRelated = 0 AND textRelated = 1, 1, 0)),
            SUM(IF(imageRelated != 0 AND textRelated = 1, 1, 0)),
            SUM(IF(imageRelated = 0 AND textRelated = 0, 1, 0))
            FROM tweetClasses WHERE classified_by = '$user';";
            $con=mysqli_connect("geotwitter.uncg.edu","admin","geotwitter","geotwitter");
            $result = mysqli_query($con,$query);
            $table = mysqli_fetch_all($result);
            foreach($table as $row)
            {
                echo "
                    <div class='stat'>
                    <h1>Total Classified</h1>
                    <h2>$row[0]</h2>
                    </div>
                    <div class='stat'>
                    <h1>Image-Only Related</h1>
                    <h2>$row[1]</h2>
                    </div>
                    <div class='stat'>
                    <h1>Text-Only Related</h1>
                    <h2>$row[2]</h2>
                    </div>
                    <div class='stat'>
                    <h1>Totally Related</h1>
                    <h2>$row[3]</h2>
                    </div>
                    <div class='stat'>
                    <h1>Not Related at all</h1>
                    <h2>$row[4]</h2>
                    </div>
                ";
            }            
        ?>
        </div>
    </center>
</body>
</html>