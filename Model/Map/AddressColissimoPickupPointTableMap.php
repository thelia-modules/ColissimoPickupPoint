<?php

namespace ColissimoPickupPoint\Model\Map;

use ColissimoPickupPoint\Model\AddressColissimoPickupPoint;
use ColissimoPickupPoint\Model\AddressColissimoPickupPointQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'address_colissimo_pickup_point' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class AddressColissimoPickupPointTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'ColissimoPickupPoint.Model.Map.AddressColissimoPickupPointTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'address_colissimo_pickup_point';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\ColissimoPickupPoint\\Model\\AddressColissimoPickupPoint';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'ColissimoPickupPoint.Model.AddressColissimoPickupPoint';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 14;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 14;

    /**
     * the column name for the ID field
     */
    const ID = 'address_colissimo_pickup_point.ID';

    /**
     * the column name for the TITLE_ID field
     */
    const TITLE_ID = 'address_colissimo_pickup_point.TITLE_ID';

    /**
     * the column name for the COMPANY field
     */
    const COMPANY = 'address_colissimo_pickup_point.COMPANY';

    /**
     * the column name for the FIRSTNAME field
     */
    const FIRSTNAME = 'address_colissimo_pickup_point.FIRSTNAME';

    /**
     * the column name for the LASTNAME field
     */
    const LASTNAME = 'address_colissimo_pickup_point.LASTNAME';

    /**
     * the column name for the ADDRESS1 field
     */
    const ADDRESS1 = 'address_colissimo_pickup_point.ADDRESS1';

    /**
     * the column name for the ADDRESS2 field
     */
    const ADDRESS2 = 'address_colissimo_pickup_point.ADDRESS2';

    /**
     * the column name for the ADDRESS3 field
     */
    const ADDRESS3 = 'address_colissimo_pickup_point.ADDRESS3';

    /**
     * the column name for the ZIPCODE field
     */
    const ZIPCODE = 'address_colissimo_pickup_point.ZIPCODE';

    /**
     * the column name for the CITY field
     */
    const CITY = 'address_colissimo_pickup_point.CITY';

    /**
     * the column name for the COUNTRY_ID field
     */
    const COUNTRY_ID = 'address_colissimo_pickup_point.COUNTRY_ID';

    /**
     * the column name for the CODE field
     */
    const CODE = 'address_colissimo_pickup_point.CODE';

    /**
     * the column name for the TYPE field
     */
    const TYPE = 'address_colissimo_pickup_point.TYPE';

    /**
     * the column name for the CELLPHONE field
     */
    const CELLPHONE = 'address_colissimo_pickup_point.CELLPHONE';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'TitleId', 'Company', 'Firstname', 'Lastname', 'Address1', 'Address2', 'Address3', 'Zipcode', 'City', 'CountryId', 'Code', 'Type', 'Cellphone', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'titleId', 'company', 'firstname', 'lastname', 'address1', 'address2', 'address3', 'zipcode', 'city', 'countryId', 'code', 'type', 'cellphone', ),
        self::TYPE_COLNAME       => array(AddressColissimoPickupPointTableMap::ID, AddressColissimoPickupPointTableMap::TITLE_ID, AddressColissimoPickupPointTableMap::COMPANY, AddressColissimoPickupPointTableMap::FIRSTNAME, AddressColissimoPickupPointTableMap::LASTNAME, AddressColissimoPickupPointTableMap::ADDRESS1, AddressColissimoPickupPointTableMap::ADDRESS2, AddressColissimoPickupPointTableMap::ADDRESS3, AddressColissimoPickupPointTableMap::ZIPCODE, AddressColissimoPickupPointTableMap::CITY, AddressColissimoPickupPointTableMap::COUNTRY_ID, AddressColissimoPickupPointTableMap::CODE, AddressColissimoPickupPointTableMap::TYPE, AddressColissimoPickupPointTableMap::CELLPHONE, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'TITLE_ID', 'COMPANY', 'FIRSTNAME', 'LASTNAME', 'ADDRESS1', 'ADDRESS2', 'ADDRESS3', 'ZIPCODE', 'CITY', 'COUNTRY_ID', 'CODE', 'TYPE', 'CELLPHONE', ),
        self::TYPE_FIELDNAME     => array('id', 'title_id', 'company', 'firstname', 'lastname', 'address1', 'address2', 'address3', 'zipcode', 'city', 'country_id', 'code', 'type', 'cellphone', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'TitleId' => 1, 'Company' => 2, 'Firstname' => 3, 'Lastname' => 4, 'Address1' => 5, 'Address2' => 6, 'Address3' => 7, 'Zipcode' => 8, 'City' => 9, 'CountryId' => 10, 'Code' => 11, 'Type' => 12, 'Cellphone' => 13, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'titleId' => 1, 'company' => 2, 'firstname' => 3, 'lastname' => 4, 'address1' => 5, 'address2' => 6, 'address3' => 7, 'zipcode' => 8, 'city' => 9, 'countryId' => 10, 'code' => 11, 'type' => 12, 'cellphone' => 13, ),
        self::TYPE_COLNAME       => array(AddressColissimoPickupPointTableMap::ID => 0, AddressColissimoPickupPointTableMap::TITLE_ID => 1, AddressColissimoPickupPointTableMap::COMPANY => 2, AddressColissimoPickupPointTableMap::FIRSTNAME => 3, AddressColissimoPickupPointTableMap::LASTNAME => 4, AddressColissimoPickupPointTableMap::ADDRESS1 => 5, AddressColissimoPickupPointTableMap::ADDRESS2 => 6, AddressColissimoPickupPointTableMap::ADDRESS3 => 7, AddressColissimoPickupPointTableMap::ZIPCODE => 8, AddressColissimoPickupPointTableMap::CITY => 9, AddressColissimoPickupPointTableMap::COUNTRY_ID => 10, AddressColissimoPickupPointTableMap::CODE => 11, AddressColissimoPickupPointTableMap::TYPE => 12, AddressColissimoPickupPointTableMap::CELLPHONE => 13, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'TITLE_ID' => 1, 'COMPANY' => 2, 'FIRSTNAME' => 3, 'LASTNAME' => 4, 'ADDRESS1' => 5, 'ADDRESS2' => 6, 'ADDRESS3' => 7, 'ZIPCODE' => 8, 'CITY' => 9, 'COUNTRY_ID' => 10, 'CODE' => 11, 'TYPE' => 12, 'CELLPHONE' => 13, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title_id' => 1, 'company' => 2, 'firstname' => 3, 'lastname' => 4, 'address1' => 5, 'address2' => 6, 'address3' => 7, 'zipcode' => 8, 'city' => 9, 'country_id' => 10, 'code' => 11, 'type' => 12, 'cellphone' => 13, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('address_colissimo_pickup_point');
        $this->setPhpName('AddressColissimoPickupPoint');
        $this->setClassName('\\ColissimoPickupPoint\\Model\\AddressColissimoPickupPoint');
        $this->setPackage('ColissimoPickupPoint.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('TITLE_ID', 'TitleId', 'INTEGER', 'customer_title', 'ID', true, null, null);
        $this->addColumn('COMPANY', 'Company', 'VARCHAR', false, 255, null);
        $this->addColumn('FIRSTNAME', 'Firstname', 'VARCHAR', true, 255, null);
        $this->addColumn('LASTNAME', 'Lastname', 'VARCHAR', true, 255, null);
        $this->addColumn('ADDRESS1', 'Address1', 'VARCHAR', true, 255, null);
        $this->addColumn('ADDRESS2', 'Address2', 'VARCHAR', true, 255, null);
        $this->addColumn('ADDRESS3', 'Address3', 'VARCHAR', true, 255, null);
        $this->addColumn('ZIPCODE', 'Zipcode', 'VARCHAR', true, 10, null);
        $this->addColumn('CITY', 'City', 'VARCHAR', true, 255, null);
        $this->addForeignKey('COUNTRY_ID', 'CountryId', 'INTEGER', 'country', 'ID', true, null, null);
        $this->addColumn('CODE', 'Code', 'VARCHAR', true, 10, null);
        $this->addColumn('TYPE', 'Type', 'VARCHAR', true, 10, null);
        $this->addColumn('CELLPHONE', 'Cellphone', 'VARCHAR', false, 20, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CustomerTitle', '\\ColissimoPickupPoint\\Model\\Thelia\\Model\\CustomerTitle', RelationMap::MANY_TO_ONE, array('title_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('Country', '\\ColissimoPickupPoint\\Model\\Thelia\\Model\\Country', RelationMap::MANY_TO_ONE, array('country_id' => 'id', ), 'RESTRICT', 'RESTRICT');
    } // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? AddressColissimoPickupPointTableMap::CLASS_DEFAULT : AddressColissimoPickupPointTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (AddressColissimoPickupPoint object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = AddressColissimoPickupPointTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = AddressColissimoPickupPointTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + AddressColissimoPickupPointTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AddressColissimoPickupPointTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            AddressColissimoPickupPointTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = AddressColissimoPickupPointTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = AddressColissimoPickupPointTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AddressColissimoPickupPointTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::ID);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::TITLE_ID);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::COMPANY);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::FIRSTNAME);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::LASTNAME);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::ADDRESS1);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::ADDRESS2);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::ADDRESS3);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::ZIPCODE);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::CITY);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::COUNTRY_ID);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::CODE);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::TYPE);
            $criteria->addSelectColumn(AddressColissimoPickupPointTableMap::CELLPHONE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.TITLE_ID');
            $criteria->addSelectColumn($alias . '.COMPANY');
            $criteria->addSelectColumn($alias . '.FIRSTNAME');
            $criteria->addSelectColumn($alias . '.LASTNAME');
            $criteria->addSelectColumn($alias . '.ADDRESS1');
            $criteria->addSelectColumn($alias . '.ADDRESS2');
            $criteria->addSelectColumn($alias . '.ADDRESS3');
            $criteria->addSelectColumn($alias . '.ZIPCODE');
            $criteria->addSelectColumn($alias . '.CITY');
            $criteria->addSelectColumn($alias . '.COUNTRY_ID');
            $criteria->addSelectColumn($alias . '.CODE');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.CELLPHONE');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(AddressColissimoPickupPointTableMap::DATABASE_NAME)->getTable(AddressColissimoPickupPointTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(AddressColissimoPickupPointTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(AddressColissimoPickupPointTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new AddressColissimoPickupPointTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a AddressColissimoPickupPoint or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or AddressColissimoPickupPoint object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AddressColissimoPickupPointTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \ColissimoPickupPoint\Model\AddressColissimoPickupPoint) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AddressColissimoPickupPointTableMap::DATABASE_NAME);
            $criteria->add(AddressColissimoPickupPointTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = AddressColissimoPickupPointQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { AddressColissimoPickupPointTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { AddressColissimoPickupPointTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the address_colissimo_pickup_point table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return AddressColissimoPickupPointQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a AddressColissimoPickupPoint or Criteria object.
     *
     * @param mixed               $criteria Criteria or AddressColissimoPickupPoint object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AddressColissimoPickupPointTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from AddressColissimoPickupPoint object
        }


        // Set the correct dbName
        $query = AddressColissimoPickupPointQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // AddressColissimoPickupPointTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
AddressColissimoPickupPointTableMap::buildTableMap();
