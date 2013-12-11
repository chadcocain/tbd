<?php
require_once 'phpgraphlib.php';

try {
    $id = (int) $_GET['id'];
    if($id != $_GET['id']) {
        throw new Exception('Supplied value does not seem consistent');
    }
    
    $DB = new mysqli('localhost', 'twobeerdudes', '24038678', 'twobeerdudes_beer');
    if($DB->connect_error) {
        throw new Exception('Connect Error (' . $DB->connect_errno . ') ' . $DB->connect_error);
    }
    
    $query = '
        SELECT
            AVG((aroma * (25 / 100)) + (taste * (25 / 100)) + (look * (15 / 100)) + (drinkability * (35 / 100))) AS trendrating
            , username
            , beerName
            , dateAdded
        FROM (
            SELECT
                r.aroma
                , r.taste
                , r.look
                , r.drinkability
                , u.username
                , be.beerName
                , r.dateAdded
            FROM beers be                
            LEFT OUTER JOIN ratings r ON r.beerID = be.id
            INNER JOIN users u ON u.id = r.userID
            WHERE
                be.id = ' . $DB->real_escape_string($id) . '
                AND u.active = "1"
                AND u.banned = "0"
            ORDER BY
                r.dateAdded DESC
            LIMIT 5
        ) AS tmp
        GROUP BY
            username
        ORDER BY
            dateAdded DESC
    ';
    $rs = $DB->query($query);
    if($rs === false) {
        throw new Exception('Query create a problem.');
    }
    
    $data = array();
    $beer = '';
    if($rs->num_rows > 0) {
        while($row = $rs->fetch_assoc()) {
            $data[$row['username']] = number_format($row['trendrating'], 1);
            $beer = $row['beerName'];
        }
    }    
    $rs->close();    
    $DB->close();
    
    $Graph = new PHPGraphLib(520, 320);
    $Graph->addData($data);
    //$Graph->setBackgroundColor('black');
    //$Graph->setBarColor('255,255,204');
    $Graph->setTitle('Latest Reviews for ' . $beer);
    $Graph->setTitleColor('5,97,162');
    $Graph->setupYAxis(12);
    $Graph->setupXAxis(20);
    $Graph->setRange(10, 0);
    //$Graph->setGrid(false);
    $Graph->setGradient('37, 156, 239', '5,97,162');
    //$Graph->setBarOutlineColor('white');
    //$Graph->setTextColor('white');
    $Graph->setDataValues(true);
    //$Graph->setDataValueColor('maroon');
    //$Graph->setDataPoints(true);
    //$Graph->setDataPointColor('yellow');
    //$Graph->setLine(true);
    //$Graph->setLineColor('yellow');
    $Graph->createGraph();
} catch(Exception $E) {
    echo $E->getMessage();
}
?>
