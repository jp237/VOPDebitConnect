<?php
/**
 * EAP-DebitConnect (shopware Edition)
 *
 * V.O.P GmbH & Co. KG
 * Hauptstraße 62
 * 56745 Bell
 * Telefon: +49 (2652) 529-0
 * Telefax: +49 (2652) 529-129
 * E-Mail: info@eaponline.de
 * USt-IdNr.: DE 261 538 563
 * Pers. Haft. Gesellschafter:
 * V.O.P Verwaltungs GmbH, HRB 21231, Koblenz
 * Geschäftsführer: Thomas Pütz
 * Handelsregister HRA20499, Koblenz
 */

class dbConn
{
    /** @var mysqli */
    public $openConnection;
    public $dbSettings;
    public $instance;
    public $lastError;
    public $lastErrors;
    // LOADING SHOPWARE CONFIG FILE

    public function __construct()
    {
        $connection = Shopware()->Container()->get('dbal_connection');
        $this->dbsettings = ['db' => Shopware()->Container()->getParameter('shopware.db')];
        $this->dbsettings['db']['port'] = $this->dbsettings['db']['port'] === null ? 3306 : $this->dbsettings['db']['port'];
    }

    public function dbOpen()
    {
        $this->openConnection = mysqli_connect($this->dbsettings['db']['host'], $this->dbsettings['db']['username'], $this->dbsettings['db']['password'], $this->dbsettings['db']['dbname'], $this->dbsettings['db']['port']);
        mysqli_set_charset($this->openConnection, 'utf8');
        // REMOVING ONLY_FULL_GROUP_BY FOR $this->openConnection
        $this->rmSqlMode('ONLY_FULL_GROUP_BY');
        $this->rmSqlMode('STRICT_TRANS_TABLES');
    }

    public function rmSqlMode($mode)
    {
        /** @todo fix this ASAP !!!! */
        $sqlMode = $this->singleResult('SELECT @@sql_mode as mode ');
        if (strpos($sqlMode['mode'], "$mode,") !== false) {
            $newMode = str_replace("$mode,", '', $sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '" . $newMode . "'");
        } elseif (strpos($sqlMode['mode'], ",$mode") !== false) {
            $newMode = str_replace(",$mode", '', $sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '" . $newMode . "'");
        } elseif (strpos($sqlMode['mode'], "$mode") !== false) {
            $newMode = str_replace("$mode", '', $sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '" . $newMode . "'");
        }
    }

    public function dbClose()
    {
        mysqli_close($this->openConnection);
    }

    public function dbTransActionList($queryList, $echo = false)
    {
        return false;
    }

    public function dbInsertList($tbl, $arr)
    {
        $this->openConnection->begin_transaction();

        foreach ($arr as $row) {
            if (!$this->dbInsert($tbl, $row, false)) {
                $this->openConnection->rollback();

                return false;
            }
        }
        $this->openConnection->commit();

        return true;
    }

    public function dbInsert($tbl, $object, $echo = false, $queryoutput = false)
    {
        foreach ($object as $key => $value) {
            $keys[] = $key;
            if (isset($value)) {
                $values[] = gettype($value) == 'string' ? "'" . $this->dbEscape($value) . "'" : (int) $value;
            } else {
                $values[] = 'null';
            }
        }

        $stmt = 'INSERT INTO ' . $tbl . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
        if ($queryoutput) {
            return $stmt;
        }
        mysqli_query($this->openConnection, $stmt);
        if (mysqli_error($this->openConnection)) {
            $this->dbError(mysqli_error($this->openConnection), $stmt, $echo);

            return false;
        }
        if ($echo) {
            echo $stmt;
        }

        return mysqli_affected_rows($this->openConnection) > 0 ? true : false;
    }

    public function dbQuery($sql, $echo = false)
    {
        if ($echo) {
            echo $sql;
        }
        mysqli_query($this->openConnection, $sql);
        if (mysqli_error($this->openConnection)) {
            //echo $sql. " > ".mysqli_error($this->openConnection);
            $this->dbError(mysqli_error($this->openConnection), $sql, $echo);

            return false;
        }

        return true;
    }

    public function tableExists($tblname, $prefix = 'dc_')
    {
        if ($prefix != 'dc_') {
            $tblname = $prefix . $tblname;
        }
        $allTables = $this->getSQLResults("show tables LIKE '" . $prefix . "%'");

        foreach ($allTables as $row) {
            foreach ($row as $key => $val) {
                if (strtoupper($val) == strtoupper($tblname)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function dbError($errortxt, $query, $output = false)
    {
        $this->lastError = $errortxt;
        $this->lastErrors[] = $errortxt;

        if (DC()->getConf('output_query_failure', 0, true) == 1) {
            $errortxt .= '<br>' . $query;
        }
        DC()->View('SQL_ERROR', $this->dbEscape($errortxt));
    }

    public function dbUpdate($tbl, $object, $where, $echo = false, $queryoutput = false)
    {
        $values = [];
        foreach ($object as $key => $value) {
            if (isset($value)) {
                $values[] = gettype($value) == 'string' ? $key . " = '" . $this->dbEscape($value) . "'" : $key . ' = ' . (int) $value;
            } else {
                $values[] = $key . ' = null';
            }
        }
        $stmt = 'UPDATE ' . $tbl . ' SET ' . implode(',', $values) . ' where ' . $where;
        if ($queryoutput) {
            return $stmt;
        }
        if ($echo) {
            echo $stmt;
        }
        mysqli_query($this->openConnection, $stmt);
        if (mysqli_error($this->openConnection)) {
            $this->dbError(mysqli_error($this->openConnection), $stmt, $echo);
        }

        return mysqli_affected_rows($this->openConnection) > 0 ? true : false;
    }

    public function dbEscape($value)
    {
        global $connect;

        return mysqli_real_escape_string($this->openConnection, $value);
    }

    public function getSQLResults($query, $echo = false)
    {
        if ($echo) {
            echo $query;
        }
        $arr = [];
        if ($result = mysqli_query($this->openConnection, $query)) {
            while ($row = $result->fetch_assoc()) {
                $arr[] = $row;
            }
            $result->free();
        }
        if (mysqli_error($this->openConnection)) {
            $this->dbError(mysqli_error($this->openConnection), $query, $echo);
        }

        return $arr;
    }

    public function singleResult($sql, $echo = false)
    {
        if ($echo) {
            echo $sql;
        }
        $returnwert = false;
        $result = mysqli_query($this->openConnection, $sql);
        if (mysqli_error($this->openConnection)) {
            $this->dbError(mysqli_error($this->openConnection), $sql, $echo);
        }

        return mysqli_fetch_assoc($result);
    }
}
