<html>
<body>

<strong>
<?php

# SUPRESS WARNINGS
error_reporting(E_ERROR | E_PARSE);

$ip = $_SERVER['REMOTE_ADDR'];

$request_uri=$_SERVER['REQUEST_URI'];

$n= $_REQUEST['n'];


# CHECK IP ADDRESS IN BLACKLIST

function main($ip, $n, $request_uri) {
    if(checkIp($ip)) {
        echo 'SORRY, YOUVE ALREADY BLACKLISTED';    
        return http_response_code(444);
    }
    # CHOOSE URL REQUESTED
    if (strpos($request_uri, '?n=')) {
        return calc($n);
    } else if (strpos($request_uri, 'blacklisted')) {
        blockAddBlacklist($ip, $request_uri);
        return http_response_code(444);
    }
}

function checkIp($ip) {
    return queryDatabase($ip, $query='sel', $request_uri);
}

function calc($n) {
    echo $n*$n;
}

function blockAddBlacklist($ip, $request_uri) {
    queryDatabase($ip, $query='ins', $request_uri);
}

function queryDatabase($ip, $query, $request_uri) {

   $host        = "host = primary";
   $port        = "port = 5432";
   $dbname      = "dbname = blacklisted";
   $credentials = "user = postgres password=password";

   $db = pg_connect( "$host $port $dbname $credentials" );
#   if(!$db) {
#      echo "Error : Unable to open database\n";
#   } else {
#      echo "Opened database successfully\n";
#   }

    $sql_sel =<<<EOF
          SELECT * from blacklisted
          where "ip_address"='$ip';
EOF;

    $sql_ins =<<<EOF
          INSERT INTO blacklisted (location, ip_address)
          VALUES ('$request_uri', '$ip' );
EOF;
    if ($query == 'sel') {
        $sql = $sql_sel;
    } elseif ($query == 'ins') {
        $sql = $sql_ins;
    }

    $ret = pg_query($db, $sql);

#   if(!$ret) {
#      echo pg_last_error($db);
#   } else {
#      echo "Query successfully\n";
#   }
    #pg_close($db);
 
    if ($query == 'sel') {
        $rows=pg_num_rows($ret); 
        if ($rows == 0) {
            return False;
        } else {
            return True;
        }
    }
    pg_close($db);    
    
    return True;
}

main($ip, $n, $request_uri );

?>

</strong>
</body>
</html>