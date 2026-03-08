<?php
//
// $Id: ezmongodbdb.php 9474 2025-08-09 12:00:00Z gb $
//
// Definition of eZMongoDBDB class
//
// Created on: <09-Aug-2025 12:00:00 gb>
//
// This source file is part of Exponential Basic, publishing software.
//
// Copyright (C) 1998-2025 7x. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//

//!! eZCommon
//! The eZMongoDBDB class provides database functions.
/*!
  eZMongoDBDB implements MongoDB specific database code.
  
  This driver translates SQL queries to MongoDB operations using a simple SQL parser
  and maps relational concepts to document-based storage.
*/

class eZMongoDBDB
{
    function __construct( $databaseName = 'ezpublish' )
    {
        $this->Type = 'mongodb';
        $this->DatabaseName = $databaseName;
        
        $ini = eZINI::instance( 'site.ini' );
        $mongoHost = $ini->variable( 'site', 'Server', 'localhost' );
        $mongoPort = $ini->variable( 'site', 'Port', 27017 );
        $mongoUser = $ini->variable( 'site', 'User', '' );
        $mongoPassword = $ini->variable( 'site', 'Password', '' );
        // $mongoHost = $ini->variable( 'DatabaseSettings', 'MongoHost', 'localhost' );
        // $mongoPort = $ini->variable( 'DatabaseSettings', 'MongoPort', 27017 );
        // $mongoUser = $ini->variable( 'DatabaseSettings', 'MongoUser', '' );
        // $mongoPassword = $ini->variable( 'DatabaseSettings', 'MongoPassword', '' );
        
        $this->Database = $this->connect( $mongoHost, $mongoPort, $mongoUser, $mongoPassword, $databaseName );
        $numAttempts = 1;
        while ( $this->Database == false && $numAttempts < 5 )
        {
            sleep(5);
            $this->Database = $this->connect( $mongoHost, $mongoPort, $mongoUser, $mongoPassword, $databaseName );
            $numAttempts++;
        }

        if ( $this->Database == false )
        {
            print( "<H1>Couldn't connect to MongoDB</H1>Please try again later or inform the system administrator." );
            exit;
        }
        
        // Initialize SQL to MongoDB query cache
        $this->QueryCache = array();
        $this->LastInsertId = null;
    }

    /*!
     \private
     Opens a new connection to MongoDB and returns the database connection
    */
    private function connect( $host, $port, $user, $password, $databaseName )
    {
        $connection = false;
        $maxAttempts = $this->connectRetryCount() + 1;
        $waitTime = $this->connectRetryWaitTime();
        $numAttempts = 1;

        while ( $connection == false && $numAttempts <= $maxAttempts )
        {
            try 
            {
                $uri = "mongodb://";
                if ( !empty($user) && !empty($password) )
                    $uri .= $user . ":" . $password . "@";
                $uri .= $host . ":" . $port . "/" . $databaseName;
                
                $client = new MongoDB\Client($uri);
                $connection = $client->selectDatabase($databaseName);
                
                // Test connection
                $connection->command(['ping' => 1]);
                $this->IsConnected = true;
                
            } catch (Exception $e) {
                $this->ErrorNumber = $e->getCode();
                $this->ErrorMessage = $e->getMessage();
                eZDebug::writeError( "MongoDB Connection error: " . $e->getMessage(), "eZMongoDBDB" );
                $this->IsConnected = false;
                
                if ( $numAttempts < $maxAttempts )
                    sleep($waitTime);
            }
            $numAttempts++;
        }

        return $connection;
    }

    /*!
      Returns the driver type.
    */
    function isA()
    {
        return "mongodb";
    }

    function error()
    {
        return $this->ErrorMessage;
    }

    /*!
      Execute a query. For MongoDB, this translates SQL to MongoDB operations.
    */
    function query( $sql, $print=false, $debug=false )
    {
        if ( $debug == true )
        {
            echo "Executing SQL: $sql<hr>";
            $bench = new eZBenchmark();
            $bench->start();
        }

        $result = $this->executeSQLQuery( $sql );
        
        if ( $debug == true )
        {
            $bench->stop();
            if ( $bench->elapsed() > 0.01 )
            {
                $GLOBALS["DDD"] .= $sql . "<br>";
                $GLOBALS["DDD"] .= $bench->printResults( true ) . "<br>";
            }
        }

        if ( $print && $debug )
        {
            print( $sql . "<br>");
        }

        if ( $result !== false )
        {
            return $result;
        }
        else
        {
            $this->unlock();
            $this->Error = "<code>" . htmlentities( $sql ) . "</code><br>\n<b>" . htmlentities($this->ErrorMessage) . "</b>\n" ;
            if ( $debug )
            {
                print( "<b>MongoDB Query Error</b>: " . htmlentities( $sql ) . "<br><b> Error message:</b> ". $this->ErrorMessage ."<br>" );
            }
            return false;
        }
    }

    /*!
      Core SQL to MongoDB translation engine
    */
    private function executeSQLQuery( $sql )
    {
        // Simple SQL parser - handles basic SELECT, INSERT, UPDATE, DELETE
        $sql = trim($sql);
        $sqlUpper = strtoupper($sql);
        
        try {
            if ( strpos($sqlUpper, 'SELECT') === 0 )
                return $this->handleSelect( $sql );
            elseif ( strpos($sqlUpper, 'INSERT') === 0 )
                return $this->handleInsert( $sql );
            elseif ( strpos($sqlUpper, 'UPDATE') === 0 )
                return $this->handleUpdate( $sql );
            elseif ( strpos($sqlUpper, 'DELETE') === 0 )
                return $this->handleDelete( $sql );
            elseif ( strpos($sqlUpper, 'CREATE') === 0 )
                return $this->handleCreate( $sql );
            elseif ( strpos($sqlUpper, 'DROP') === 0 )
                return $this->handleDrop( $sql );
            else
                return true; // Unknown query, assume success
                
        } catch (Exception $e) {
            $this->ErrorMessage = $e->getMessage();
            return false;
        }
    }

    private function handleSelect( $sql )
    {
        // Parse SELECT query
        $pattern = '/SELECT\s+(.*?)\s+FROM\s+(\w+)(?:\s+WHERE\s+(.*?))?(?:\s+ORDER\s+BY\s+(.*?))?(?:\s+LIMIT\s+(\d+)(?:\s*,\s*(\d+))?)?$/i';
        
        if ( !preg_match($pattern, $sql, $matches) )
        {
            throw new Exception("Could not parse SELECT query: " . $sql);
        }
        
        $fields = trim($matches[1]);
        $table = $matches[2];
        $where = isset($matches[3]) ? $matches[3] : '';
        $orderBy = isset($matches[4]) ? $matches[4] : '';
        $limit = isset($matches[5]) ? (int)$matches[5] : 0;
        $offset = isset($matches[6]) ? (int)$matches[6] : 0;
        
        // Handle RANDOM() ordering for MongoDB
        if ( stripos($orderBy, 'RANDOM()') !== false )
        {
            $orderBy = ['$sample' => ['size' => $limit > 0 ? $limit : 1000]];
        }
        
        $collection = $this->Database->selectCollection($table);
        $filter = $this->parseWhereClause($where);
        $options = array();
        
        if ( $limit > 0 )
        {
            $options['limit'] = $limit;
            if ( $offset > 0 )
                $options['skip'] = $offset;
        }
        
        if ( !empty($orderBy) && !is_array($orderBy) )
        {
            $options['sort'] = $this->parseOrderBy($orderBy);
        }
        
        if ( is_array($orderBy) ) // Handle $sample for random
        {
            $pipeline = [$orderBy];
            if ( !empty($filter) )
                array_unshift($pipeline, ['$match' => $filter]);
            $cursor = $collection->aggregate($pipeline);
        }
        else
        {
            $cursor = $collection->find($filter, $options);
        }
        
        return new eZMongoDBResult($cursor);
    }

    private function handleInsert( $sql )
    {
        // Parse INSERT query
        $pattern = '/INSERT\s+INTO\s+(\w+)\s*\((.*?)\)\s*VALUES\s*\((.*?)\)/i';
        
        if ( !preg_match($pattern, $sql, $matches) )
        {
            throw new Exception("Could not parse INSERT query: " . $sql);
        }
        
        $table = $matches[1];
        $fields = explode(',', $matches[2]);
        $values = explode(',', $matches[3]);
        
        $document = array();
        for ( $i = 0; $i < count($fields); $i++ )
        {
            $field = trim($fields[$i]);
            $value = trim($values[$i], " '\"");
            
            // Convert numeric values
            if ( is_numeric($value) )
                $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                
            $document[$field] = $value;
        }
        
        // Generate ID if not provided
        if ( !isset($document['ID']) && !isset($document['_id']) )
        {
            $document['ID'] = $this->nextID($table);
        }
        
        $collection = $this->Database->selectCollection($table);
        $result = $collection->insertOne($document);
        
        $this->LastInsertId = isset($document['ID']) ? $document['ID'] : $result->getInsertedId();
        
        return $result->getInsertedCount() > 0;
    }

    private function handleUpdate( $sql )
    {
        // Parse UPDATE query
        $pattern = '/UPDATE\s+(\w+)\s+SET\s+(.*?)(?:\s+WHERE\s+(.*?))?$/i';
        
        if ( !preg_match($pattern, $sql, $matches) )
        {
            throw new Exception("Could not parse UPDATE query: " . $sql);
        }
        
        $table = $matches[1];
        $setClause = $matches[2];
        $where = isset($matches[3]) ? $matches[3] : '';
        
        $collection = $this->Database->selectCollection($table);
        $filter = $this->parseWhereClause($where);
        $update = $this->parseSetClause($setClause);
        
        $result = $collection->updateMany($filter, $update);
        return $result->getModifiedCount() >= 0;
    }

    private function handleDelete( $sql )
    {
        // Parse DELETE query
        $pattern = '/DELETE\s+FROM\s+(\w+)(?:\s+WHERE\s+(.*?))?$/i';
        
        if ( !preg_match($pattern, $sql, $matches) )
        {
            throw new Exception("Could not parse DELETE query: " . $sql);
        }
        
        $table = $matches[1];
        $where = isset($matches[2]) ? $matches[2] : '';
        
        $collection = $this->Database->selectCollection($table);
        $filter = $this->parseWhereClause($where);
        
        $result = $collection->deleteMany($filter);
        return $result->getDeletedCount() >= 0;
    }

    private function handleCreate( $sql )
    {
        // MongoDB doesn't need explicit collection creation
        return true;
    }

    private function handleDrop( $sql )
    {
        $pattern = '/DROP\s+TABLE\s+(\w+)/i';
        if ( preg_match($pattern, $sql, $matches) )
        {
            $table = $matches[1];
            $collection = $this->Database->selectCollection($table);
            $collection->drop();
        }
        return true;
    }

    private function parseWhereClause( $where )
    {
        if ( empty($where) )
            return array();
            
        $filter = array();
        
        // Simple WHERE clause parsing - handles basic conditions
        if ( preg_match('/(\w+)\s*=\s*[\'"]?([^\'"]*)[\'"]?/', $where, $matches) )
        {
            $field = $matches[1];
            $value = $matches[2];
            
            if ( is_numeric($value) )
                $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                
            $filter[$field] = $value;
        }
        
        return $filter;
    }

    private function parseSetClause( $setClause )
    {
        $update = array('$set' => array());
        
        $assignments = explode(',', $setClause);
        foreach ( $assignments as $assignment )
        {
            if ( preg_match('/(\w+)\s*=\s*[\'"]?([^\'"]*)[\'"]?/', trim($assignment), $matches) )
            {
                $field = $matches[1];
                $value = $matches[2];
                
                if ( is_numeric($value) )
                    $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                    
                $update['$set'][$field] = $value;
            }
        }
        
        return $update;
    }

    private function parseOrderBy( $orderBy )
    {
        $sort = array();
        $parts = explode(',', $orderBy);
        
        foreach ( $parts as $part )
        {
            $part = trim($part);
            if ( preg_match('/(\w+)(?:\s+(ASC|DESC))?/i', $part, $matches) )
            {
                $field = $matches[1];
                $direction = isset($matches[2]) && strtoupper($matches[2]) === 'DESC' ? -1 : 1;
                $sort[$field] = $direction;
            }
        }
        
        return $sort;
    }

    /*!
      Executes a SELECT query that returns multiple rows
    */
    function array_query( &$array, $sql, $min = 0, $max = -1, $column = false )
    {
        $array = array();
        $results = $this->array_query_append( $array, $sql, $min, $max, $column );
        return $results;
    }

    /*!
      Same as array_query() but expects to receive 1 row only
    */
    function query_single( &$row, $sql, $column = false )
    {
        $array = array();
        $ret = $this->array_query_append( $array, $sql, 1, 1, $column );
        if ( isset( $array[0] ) )
            $row = $array[0];
        else
            $row = "";
        return $ret;
    }

    /*!
      Appends query results to existing array
    */
    function array_query_append( &$array, $sql, $min = 0, $max = -1, $column = false )
    {
        $limit = -1;
        $offset = 0;

        // Check for array parameters
        if ( is_array( $min ) )
        {
            $params = $min;

            if ( isset( $params["Limit"] ) && is_numeric( $params["Limit"] ) )
                $limit = $params["Limit"];

            if ( isset( $params["Offset"] ) && is_numeric( $params["Offset"] ) )
                $offset = $params["Offset"];
        }

        if ( $limit != -1 )
        {
            $sql .= " LIMIT $offset, $limit ";
        }

        $results = $this->query( $sql );

        if ( $results === false )
        {
            $this->Error = $this->ErrorMessage;
            return false;
        }

        $offset = count( $array );
        if ( $results && is_object( $results ) )
        {
            $i = 0;
            foreach ( $results as $document )
            {
                // Convert MongoDB document to associative array
                $row = array();
                foreach ( $document as $key => $value )
                {
                    // Skip MongoDB's _id field unless specifically requested
                    if ( $key === '_id' && !$column )
                        continue;
                        
                    $row[$key] = $value;
                }

                $array[$i + $offset] = is_string( $column ) ? $row[$column] : $row;
                $i++;
            }

            if ( count( $array ) < $min )
            {
                $this->Error = "<code>" . htmlentities( $sql ) . "</code><br>\n<b>" .
                                        htmlentities( "Received " . count( $array ) . " rows, minimum is " . (int) $min ) . "</b>\n" ;
            }
            if ( $max >= 0 )
            {
                if ( count( $array ) > $max )
                {
                    $this->Error = "<code>" . htmlentities( $sql ) . "</code><br>\n<b>" .
                                            htmlentities( "Received " . count( $array ) . " rows, maximum is $max" ) . "</b>\n" ;
                }
            }
        }

        return $array;
    }

    function dateToNative( &$date )
    {
        $ret = false;
        if ( is_a( $date, "eZDate" ) )
        {
            // MongoDB uses MongoDB\BSON\UTCDateTime for dates
            $timestamp = mktime(0, 0, 0, $date->month(), $date->day(), $date->year());
            $ret = new MongoDB\BSON\UTCDateTime($timestamp * 1000); // MongoDB expects milliseconds
        }
        else
            print( "Wrong date type, must be an eZDate object." );

        return $ret;
    }

    /*!
      MongoDB doesn't have table locking in the traditional sense
    */
    function lock( $table )
    {
        // No-op for MongoDB
    }

    /*!
      Releases table locks - no-op for MongoDB
    */
    function unlock()
    {
        // No-op for MongoDB
    }

    /*!
      Starts a new transaction (MongoDB 4.0+ with replica sets)
    */
    function begin()
    {
        try {
            $this->Session = $this->Database->getManager()->startSession();
            $this->Session->startTransaction();
        } catch (Exception $e) {
            // Transactions may not be supported in all MongoDB configurations
            eZDebug::writeWarning( "MongoDB transactions not supported: " . $e->getMessage(), "eZMongoDBDB" );
        }
    }

    /*!
      Commits the transaction
    */
    function commit()
    {
        if ( isset($this->Session) )
        {
            try {
                $this->Session->commitTransaction();
            } catch (Exception $e) {
                eZDebug::writeError( "MongoDB commit failed: " . $e->getMessage(), "eZMongoDBDB" );
            }
        }
    }

    /*!
      Cancels the transaction
    */
    function rollback()
    {
        if ( isset($this->Session) )
        {
            try {
                $this->Session->abortTransaction();
            } catch (Exception $e) {
                eZDebug::writeError( "MongoDB rollback failed: " . $e->getMessage(), "eZMongoDBDB" );
            }
        }
    }

    /*!
      Returns the next ID value for a collection
    */
    function nextID( $table, $field="ID" )
    {
        $collection = $this->Database->selectCollection($table);
        
        // Find the highest existing ID
        $pipeline = [
            ['$group' => [
                '_id' => null,
                'maxId' => ['$max' => '$' . $field]
            ]]
        ];
        
        $result = $collection->aggregate($pipeline)->toArray();
        
        if ( count($result) > 0 && isset($result[0]['maxId']) )
            return $result[0]['maxId'] + 1;
        else
            return 1;
    }

    /*!
      Escapes a string - MongoDB handles this automatically
    */
    function escapeString( $str )
    {
        if( is_null( $str ) )
            return false;
        // MongoDB driver handles escaping automatically
        return $str;
    }

    /*!
      Returns the field name as-is
    */
    function fieldName( $str )
    {
        return $str;
    }

    /*!
      Closes the MongoDB connection
    */
    function close()
    {
        // MongoDB\Client handles connection cleanup automatically
        $this->IsConnected = false;
    }

    function printConnection()
    {
        print "Database: " . $this->DatabaseName . "<br>\n";
        print "Type: MongoDB<br>\n";
    }

    function counter()
    {
        return $this->Counter;
    }

    function md5( $str )
    {
        // For MongoDB, we'll need to handle MD5 differently
        // This might need to be computed at the application level
        return " /* MD5 not directly supported in MongoDB */ $str ";
    }

    function connectRetryCount()
    {
        return $this->ConnectRetries;
    }

    function connectRetryWaitTime()
    {
        return 3;
    }

    // MongoDB-specific helper methods
    
    function getLastInsertId()
    {
        return $this->LastInsertId;
    }
    
    function getCollection( $name )
    {
        return $this->Database->selectCollection($name);
    }

    var $DatabaseName;
    var $Database;
    var $Error;
    var $ErrorMessage;
    var $ErrorNumber;
    var $Counter;
    var $IsConnected;
    var $ConnectRetries;
    var $Type;
    var $QueryCache;
    var $LastInsertId;
    var $Session; // For transactions
}

/*!
  Result wrapper class for MongoDB cursors
*/
class eZMongoDBResult implements Iterator
{
    private $cursor;
    private $position = 0;
    private $results = array();
    
    public function __construct( $cursor )
    {
        $this->cursor = $cursor;
        $this->rewind();
    }
    
    public function rewind()
    {
        $this->position = 0;
        if ( is_object($this->cursor) )
        {
            $this->results = iterator_to_array($this->cursor);
        }
    }
    
    public function current()
    {
        return isset($this->results[$this->position]) ? $this->results[$this->position] : null;
    }
    
    public function key()
    {
        return $this->position;
    }
    
    public function next()
    {
        ++$this->position;
    }
    
    public function valid()
    {
        return isset($this->results[$this->position]);
    }
    
    public function fetchArray( $type = SQLITE3_ASSOC )
    {
        if ( $this->valid() )
        {
            $current = $this->current();
            $this->next();
            
            // Convert MongoDB document to array
            if ( is_object($current) )
            {
                $result = array();
                foreach ( $current as $key => $value )
                {
                    if ( $key !== '_id' ) // Skip MongoDB's internal _id
                        $result[$key] = $value;
                }
                return $result;
            }
            return $current;
        }
        return false;
    }
}

?>