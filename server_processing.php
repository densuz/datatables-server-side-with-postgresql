<?php
// Nama Kolom di dalam Tabel
$aColumns = array('namakolomtabel1', 'namakolomtabel2', 'namakolomtabel3', 'namakolomtabel4');

/* Indexed column (used for fast and accurate table cardinality) */
// id kolom
$sIndexColumn = "isi dengan id tabel mu"; //example : tb_report.id

/* DB table to use */
// nama Database
$sTable = "isi dengan nama tabel yang kamu gunakan disini";

/* Database connection information */
$gaSql['user']       = "idi dengan host user yang kamu gunakan"; // example: postgres
$gaSql['password']   = "isi dengan host password ";
$gaSql['db']         = "isi dengan nama databasemu";
$gaSql['server']     = "isi dengan host yang kamu gunakan"; //exanple : using ip address 192.168.100.1 or localhost


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */

/*
     * DB connection
     */
$gaSql['link'] = pg_connect(
    " host=" . $gaSql['server'] .
        " dbname=" . $gaSql['db'] .
        " user=" . $gaSql['user'] .
        " password=" . $gaSql['password']
) or die('Could not connect: ' . pg_last_error());


/*
     * Paging
     */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . intval($_GET['iDisplayLength']) . " OFFSET " .
        intval($_GET['iDisplayStart']);
}


/*
     * Ordering
     */
if (isset($_GET['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
                    " . ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
}


/*
     * Filtering
     * NOTE This assumes that the field that is being searched on is a string typed field (ie. one
     * on which ILIKE can be used). Boolean fields etc will need a modification here.
     */
$sWhere = "";
if ($_GET['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($_GET['bSearchable_' . $i] == "true") {
            $sWhere .= $aColumns[$i] . " ILIKE '%" . pg_escape_string($_GET['sSearch']) . "%' OR ";
        }
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ")";
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " ILIKE '%" . pg_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}


$sQuery = "
        SELECT " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
$rResult = pg_query($gaSql['link'], $sQuery) or die(pg_last_error());

$sQuery = "
        SELECT $sIndexColumn
        FROM   $sTable
    ";
$rResultTotal = pg_query($gaSql['link'], $sQuery) or die(pg_last_error());
$iTotal = pg_num_rows($rResultTotal);
pg_free_result($rResultTotal);

if ($sWhere != "") {
    $sQuery = "
            SELECT $sIndexColumn
            FROM   $sTable
            $sWhere
        ";
    $rResultFilterTotal = pg_query($gaSql['link'], $sQuery) or die(pg_last_error());
    $iFilteredTotal = pg_num_rows($rResultFilterTotal);
    pg_free_result($rResultFilterTotal);
} else {
    $iFilteredTotal = $iTotal;
}



/*
     * Output
     */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ($aRow = pg_fetch_array($rResult, null, PGSQL_ASSOC)) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);

// Free resultset
pg_free_result($rResult);

// Closing connection
pg_close($gaSql['link']);
