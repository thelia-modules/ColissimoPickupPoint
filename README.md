ColissimoPickupPoint Module v1.0
author: <info@thelia.net>

Summary
=======

1. Install notes
2. How to use
3. Loops
4. Integration

Instructions
=====
Install notes
-----------
### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is ColissimoPickupPoint.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/colissimo-pickup-point-module:~1.0.0
```

How to use
-----------
First, go to your back office, tab Modules, and activate the module ColissimoPickupPoint.
Then go to the ColissimoPickupPoint config page, tab "Advanced Configuration" and enter your Colissimo id and password.
To import exported files in Expeditor INET, you need the file THELIA_INET.FMT, that is in the archive.

Loops
-----
1. colissimo.pickup.point.check.rights
    - Arguments:
        None
    - Output:
        1. $ERRMES: Error message
        2. $ERRFILE: File where the error has been detected
    - Usage:
        ```{loop name="yourloopname" type="colissimo.pickup.point.check.rights"}<!-- your template -->{/loop}```

2. colissimo.pickup.point
    - Arguments:
        1. area_id | mandatory | id of the area we want to know the price slices of
    - Output:
        1. $SLICE_ID: The ID of this price slice
        2. $MAX_WEIGHT: Max cart weight for the price slice
        3. $MAX_PRICE: Max cart price for the price slice
        4. $PRICE: Delivery price for this price slice
        5. $FRANCO: UNUSED
    - Usage:
        ```{loop name="yourloopname" type="colissimo.pickup.point"}<!-- your template -->{/loop}```

3. colissimo.pickup.point.id
    - Arguments:
        None
    - Output:
        1. $MODULE_ID: Id of the ColissimoPickupPoint module
    - Usage:
        ```{loop name="yourloopname" type="colissimo.pickup.point.id"}<!-- your template -->{/loop}```

4. colissimo.pickup.point.around
    - Arguments:
        1. countryid | optionnal | Country ID of where the search location is
        2. zipcode | optionnal | Zipcode of the searched city
        3. city    | optionnal | Name of the searched city
        4. address | optionnal | Id of the address to use for the search.
        address cannot be used at the same time as zipcode + city
    - Output:
        1. $LONGITUDE: longitude of the pickup & go store
        2. $LATITUDE : latitude of the pickup & go store
        3. $CODE     : ID of the pickup & go store
        4. $ADDRESS  : address of the pickup & go store
        5. $ZIPCODE  : zipcode of the pickup & go store
        6. $CITY     : city of the pickup & go store
        7. $DISTANCE : distance between the store and the customer's address/searched address
    - Usage:
        ```{loop name="yourloopname" type="colissimo.pickup.point.around"}<!-- your template -->{/loop}```

5. address.colissimo.pickup.point
    - Arguments:
        The same as the loop address
    - Output:
        The same as the loop address, but with pickup & go store's address
    - Usage:
        ```{loop name="yourloopname" type="address.colissimo.pickup.point"}<!-- your template -->{/loop}```

6. order.notsent.colissimo.pickup.point
    - Arguments:
        None
    - Output:
        The same as the loop order, but with not sent ColissimoPickupPoint orders.
    - Usage:
        ```{loop name="yourloopname" type="order.notsent.colissimo.pickup.point"}<!-- your template -->{/loop}```
        
7. colissimo.pickup.point.order_address
	- Arguments:
		1. id | mandatory | ID of the OrderAddressColissimoPickupPoint that should be retrieved by the loop.
	- Outputs:
		1. $ID : OrderAddressColissimoPickupPoint ID.
		2. $CODE : OrderAddressColissimoPickupPoint code.
		3. $TYPE : OrderAddressColissimoPickupPoint type.
	- Usage:
		```{loop name="yourloopname" type="colissimo.pickup.point.order_address"}<!-- your template -->{/loop}```
		
8. colissimo.pickup.point.area.freeshipping
	- Arguments:
		1. area_id | optionnal | Id of the area we want to know if freeshipping from is active
	- Outputs:
		1. $ID : ColissimoPickupPointAreaFreeshipping ID.
		2. $AREA_ID : The area ID.
		3. $CART_AMOUNT : The minimum cart amount to have free shipping for this area.
	- Usage:
		```{loop name="yourloopname" type="colissimo.pickup.point.area.freeshipping"}<!-- your template -->{/loop}```
		
9. colissimo.pickup.point.freeshipping
	- Arguments:
		1. id | optionnal | Should always be 1.
	- Outputs:
		1. $FREESHIPPING_ACTIVE : Whether free shipping is activated with no restrictions on all area.
		2. $FREESHIPPING_FROM : The minimum cart amount to have free shipping on all alreas.
	- Usage:
		```{loop name="yourloopname" type="colissimo.pickup.point.freeshipping"}<!-- your template -->{/loop}```
		
Plugins Smarty
-----
1. colissimoPickupPointDeliveryPrice
	- Arguments:
		1. country | optionnal | The country ID from which you want to get the delivery prices. Defaults to store country
	- Outputs:
		1. $isValidMode : Whether the delivery is valid for the cart in session and the chosen country.
		2. $deliveryPrice : The delivery price for the cart in session in the chosen country.
	- Usage:
		```{colissimoPickupPointDeliveryPrice country=64}```

Integration
-----------
A integration example is available for the default theme of Thelia.
To install it, copy the files of pathToColissimoPickupPoint/templates/frontOffice/default and
pathToColissimoPickupPoint/templates/frontOffice/default/ajax respectively in pathToThelia/templates/frontOffice/default
and pathToThelia/templates/frontOffice/default/ajax
