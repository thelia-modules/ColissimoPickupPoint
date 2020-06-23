<?php

namespace ColissimoPickupPoint\Model\Base;

use \Exception;
use \PDO;
use ColissimoPickupPoint\Model\OrderAddressColissimoPickupPoint as ChildOrderAddressColissimoPickupPoint;
use ColissimoPickupPoint\Model\OrderAddressColissimoPickupPointQuery as ChildOrderAddressColissimoPickupPointQuery;
use ColissimoPickupPoint\Model\Map\OrderAddressColissimoPickupPointTableMap;
use ColissimoPickupPoint\Model\Thelia\Model\OrderAddress;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order_address_colissimo_pickup_point' table.
 *
 *
 *
 * @method     ChildOrderAddressColissimoPickupPointQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderAddressColissimoPickupPointQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildOrderAddressColissimoPickupPointQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method     ChildOrderAddressColissimoPickupPointQuery groupById() Group by the id column
 * @method     ChildOrderAddressColissimoPickupPointQuery groupByCode() Group by the code column
 * @method     ChildOrderAddressColissimoPickupPointQuery groupByType() Group by the type column
 *
 * @method     ChildOrderAddressColissimoPickupPointQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderAddressColissimoPickupPointQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderAddressColissimoPickupPointQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderAddressColissimoPickupPointQuery leftJoinOrderAddress($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderAddress relation
 * @method     ChildOrderAddressColissimoPickupPointQuery rightJoinOrderAddress($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderAddress relation
 * @method     ChildOrderAddressColissimoPickupPointQuery innerJoinOrderAddress($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderAddress relation
 *
 * @method     ChildOrderAddressColissimoPickupPoint findOne(ConnectionInterface $con = null) Return the first ChildOrderAddressColissimoPickupPoint matching the query
 * @method     ChildOrderAddressColissimoPickupPoint findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderAddressColissimoPickupPoint matching the query, or a new ChildOrderAddressColissimoPickupPoint object populated from the query conditions when no match is found
 *
 * @method     ChildOrderAddressColissimoPickupPoint findOneById(int $id) Return the first ChildOrderAddressColissimoPickupPoint filtered by the id column
 * @method     ChildOrderAddressColissimoPickupPoint findOneByCode(string $code) Return the first ChildOrderAddressColissimoPickupPoint filtered by the code column
 * @method     ChildOrderAddressColissimoPickupPoint findOneByType(string $type) Return the first ChildOrderAddressColissimoPickupPoint filtered by the type column
 *
 * @method     array findById(int $id) Return ChildOrderAddressColissimoPickupPoint objects filtered by the id column
 * @method     array findByCode(string $code) Return ChildOrderAddressColissimoPickupPoint objects filtered by the code column
 * @method     array findByType(string $type) Return ChildOrderAddressColissimoPickupPoint objects filtered by the type column
 *
 */
abstract class OrderAddressColissimoPickupPointQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \ColissimoPickupPoint\Model\Base\OrderAddressColissimoPickupPointQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\ColissimoPickupPoint\\Model\\OrderAddressColissimoPickupPoint', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderAddressColissimoPickupPointQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderAddressColissimoPickupPointQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \ColissimoPickupPoint\Model\OrderAddressColissimoPickupPointQuery) {
            return $criteria;
        }
        $query = new \ColissimoPickupPoint\Model\OrderAddressColissimoPickupPointQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildOrderAddressColissimoPickupPoint|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderAddressColissimoPickupPointTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderAddressColissimoPickupPointTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildOrderAddressColissimoPickupPoint A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CODE, TYPE FROM order_address_colissimo_pickup_point WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildOrderAddressColissimoPickupPoint();
            $obj->hydrate($row);
            OrderAddressColissimoPickupPointTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildOrderAddressColissimoPickupPoint|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @see       filterByOrderAddress()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::TYPE, $type, $comparison);
    }

    /**
     * Filter the query by a related \ColissimoPickupPoint\Model\Thelia\Model\OrderAddress object
     *
     * @param \ColissimoPickupPoint\Model\Thelia\Model\OrderAddress|ObjectCollection $orderAddress The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function filterByOrderAddress($orderAddress, $comparison = null)
    {
        if ($orderAddress instanceof \ColissimoPickupPoint\Model\Thelia\Model\OrderAddress) {
            return $this
                ->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $orderAddress->getId(), $comparison);
        } elseif ($orderAddress instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $orderAddress->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderAddress() only accepts arguments of type \ColissimoPickupPoint\Model\Thelia\Model\OrderAddress or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderAddress relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function joinOrderAddress($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderAddress');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderAddress');
        }

        return $this;
    }

    /**
     * Use the OrderAddress relation OrderAddress object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \ColissimoPickupPoint\Model\Thelia\Model\OrderAddressQuery A secondary query class using the current class as primary query
     */
    public function useOrderAddressQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderAddress($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderAddress', '\ColissimoPickupPoint\Model\Thelia\Model\OrderAddressQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrderAddressColissimoPickupPoint $orderAddressColissimoPickupPoint Object to remove from the list of results
     *
     * @return ChildOrderAddressColissimoPickupPointQuery The current query, for fluid interface
     */
    public function prune($orderAddressColissimoPickupPoint = null)
    {
        if ($orderAddressColissimoPickupPoint) {
            $this->addUsingAlias(OrderAddressColissimoPickupPointTableMap::ID, $orderAddressColissimoPickupPoint->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_address_colissimo_pickup_point table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderAddressColissimoPickupPointTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrderAddressColissimoPickupPointTableMap::clearInstancePool();
            OrderAddressColissimoPickupPointTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderAddressColissimoPickupPoint or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrderAddressColissimoPickupPoint object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderAddressColissimoPickupPointTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderAddressColissimoPickupPointTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        OrderAddressColissimoPickupPointTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            OrderAddressColissimoPickupPointTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderAddressColissimoPickupPointQuery
