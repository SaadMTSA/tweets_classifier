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

<body onload='onLoad()'>
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
        <script type='text/javascript'>

            function onLoad() {
                var myDate = getCookie('dtfrom');
                if (myDate == '')
                    myDate = new Date(2017, 8, 01);
                else
                    myDate = new Date(myDate); $(function () {
                        $('#dtp1').datetimepicker({
                            viewMode: 'years',
                            format: 'YYYY-MM-DD HH:mm:ss',
                            date: myDate,
                        });
                    });
                myDate = getCookie('dtto');
                if (myDate == '')
                    myDate = new Date(2017, 9, 10, 22);
                else
                    myDate = new Date(myDate);
                $('#dtp2').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY-MM-DD HH:mm:ss',
                    date: myDate,
                });
            }
        </script>
        <?php
        if(isset($_POST['signin']))
        {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $query = "SELECT count(*) FROM classifierUsers WHERE username = '$username' AND password = PASSWORD('$password');";
            $con=mysqli_connect("geotwitter.uncg.edu","admin","geotwitter","geotwitter");
            $result = mysqli_query($con,$query);            
            $table = mysqli_fetch_all($result);
            foreach($table as $row)
            {

                if($row[0] == '1')
                {
                    echo "<script>setCookie('user', '$username', 700)</script>";
                    $val = 'YES';
                }
            }
            header("Refresh:0");
        }
        if((!isset($_COOKIE['user']) || $_COOKIE['user'] == '') && $val != 'YES')
        {
            echo "
                <form method='POST'>
                <div class='tweet' style='margin-top:50px;'>
                <h3>Username</h3>
                <input type='text' name='username' maxlength='20'>
                <h3>Password</h3>
                <input type='password' name='password' maxlength='20'>
                <br>
                <button class='btn' name='signin' value='1' type='submit'>Sign in</button>
                </div>
                </form>
            ";
        }
        else
        {
            echo "
            <form method='POST'>
            <div id='filter' class='filtersTable'>
                <table>
                    <tr>
                        <th>
                            <div class='text'>From</div>
                        </th>
                        <th>
                            <div class='text'>To</div>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <div class='input-group date' id='dtp1' style='z-index: 0;'>
                                <input type='text' class='form-control' name='dtfrom' style='z-index: 0;'/>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'>
                                    </span>
                                </span>
                            </div>
                            
                        </td>
                        <td>
                            <div class='input-group date' id='dtp2' style='z-index: 0;'>
                                <input type='text' class='form-control' name='dtto' style='z-index: 0;'/>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'>
                                    </span>
                                </span>
                            </div>
    
                        </td>
                    </tr>
                </table>
                <input type='submit' name='submit' value='Apply' class='btn'>
            </div>
        </form>";
        }
        if(isset($_POST['submit']))
        {
            applyFilters($_POST['dtfrom'], $_POST['dtto'], 0);
        }
        else if(isset($_POST['classify']))
        {
            $con=mysqli_connect("geotwitter.uncg.edu","admin","geotwitter","geotwitter");
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            else
            {
                $tweetid = $_POST['classify'];
                $user = $_COOKIE['user'];
                $imageArr = $_POST['image'];
                $imageVal = 0;
                $ni = count($imageArr);
                for($i = 0; $i < $ni; $i++)
                {
                    $item = $imageArr[$i];
                    if($item == 'wr')
                    {
                        $imageVal += 1;
                    }
                    if($item == 'fr')
                    {
                        $imageVal += 2;
                    }
                    if($item == 'dr')
                    {
                        $imageVal += 4;
                    }
                }

                $query = "SELECT text INTO @myText FROM tweetCoords WHERE tweet_id = '$tweetid';";
                $query .= "INSERT INTO tweetClasses VALUES('$tweetid', $imageVal, if(@myText like '%irma%' or @myText like '%hurricane%', 1, 0), '$user');";
                $con->multi_query($query);
            }
            applyFilters(htmlspecialchars($_COOKIE["dtfrom"]), htmlspecialchars($_COOKIE["dtto"]), intval(htmlspecialchars($_COOKIE["next"])));
        }
        else if(isset($_POST['corr']))
        {
            $con=mysqli_connect("geotwitter.uncg.edu","admin","geotwitter","geotwitter");
            $tweetid = $_POST['corr'];
            $query = "UPDATE tweetCoords SET image_url = NULL WHERE tweet_id = '$tweetid';";
            $con->query($query);
            applyFilters(htmlspecialchars($_COOKIE["dtfrom"]), htmlspecialchars($_COOKIE["dtto"]), intval(htmlspecialchars($_COOKIE["next"])));            
        }
        else if(isset($_POST['next']))
        {
            applyFilters(htmlspecialchars($_COOKIE["dtfrom"]), htmlspecialchars($_COOKIE["dtto"]), intval(htmlspecialchars($_COOKIE["next"])) + 1);            
        }
        else if(isset($_POST['prev']))
        {
            applyFilters(htmlspecialchars($_COOKIE["dtfrom"]), htmlspecialchars($_COOKIE["dtto"]), intval(htmlspecialchars($_COOKIE["next"])) - 1);            
        }
        else if(isset($_COOKIE['user']) && $_COOKIE['user'] != '')
        {
            applyFilters(htmlspecialchars($_COOKIE["dtfrom"]), htmlspecialchars($_COOKIE["dtto"]), intval(htmlspecialchars($_COOKIE["next"])));            
        }
        function applyFilters($dtf, $dtt, $next)
        {
            if($next < 0)
                $next = 0;
            echo "<script>setCookie('dtfrom','$dtf',700)</script><br>";
            echo "<script>setCookie('dtto', '$dtt', 700)</script><br>";
            echo "<script>setCookie('next', '$next', 700)</script><br>";
            $con=mysqli_connect("geotwitter.uncg.edu","admin","geotwitter","geotwitter");
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            else
            {
                $user = $_COOKIE['user'];
                $query = "SELECT t.tweet_id, t.text, t.image_url, t.created_at FROM tweetCoords as t WHERE t.created_at >= ('$dtf') AND t.created_at <= ('$dtt') AND t.tweet_id NOT IN (SELECT tweet_id from tweetClasses WHERE classified_by = '$user') AND t.image_url IS NOT NULL limit $next,1;";
                $result = mysqli_query($con,$query);            
                $table = mysqli_fetch_all($result);
                foreach($table as $row)
                {   
                    echo "<form action='' method='POST'>";
                    echo "<div class='tweet'>";
                    echo "
                    <button class='btn' name='corr' value='$row[0]' type='submit' style='float:right'>ImageCorrupted</button>
                    <h1><a target='_blank' style='color:black' href='http://localhost:8000/filter/$row[0]'>$row[0]</a></h1>
                    <h1>$row[3]</h1>
                    <h2>$row[1]</h2>
                    <a target'_blank' href='$row[2]'><img src='$row[2]'style='max-height: 450px;max-width:450px;'/></a><br>";
                    
                    echo "
                        <h2>Image Class</h1>
                        <input type='checkbox' name='image[]' value='wr'>Wind-Related<br>
                        <input type='checkbox' name='image[]' value='fr'>Flood-Related<br>
                        <input type='checkbox' name='image[]' value='dr'>Destruction-Related<br>
                        <br>
                        <button class='btn' name='classify' value='$row[0]' type='submit'>Classify</button>
                    ";

                    echo "<button class='btn' name='prev' value='1' type='submit' style='float:left'>Previous</button>
                    <button class='btn' name='next' value='1' type='submit' style='float:right'>Next</button>
                    <br>

                    ";
                    echo "</div>";
                    echo "</form>";
                }
            }
        }
    ?>
    </center>
</body>

</html>