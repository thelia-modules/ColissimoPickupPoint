# ColissimoPickupPoint

Adds a delivery system for Colissimo pickup point delivery, with or without signature. 

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is ReadmeTest.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/colissimo-pickup-point-module:~1.0.0
```

## Usage

From the module configuration tab :

- Price slice tab : Allow you to define price slices for every area served by your module, as well as to toggle free shipping, 
for a minimum price, minimum price by area, or for everyone.
- Advanced Configuration tab : Lets you configure your module

## Loop

If your module declare one or more loop, describe them here like this :

[colissimo.pickup.point.check.rights]

### Input arguments

None

### Output arguments

|Variable   |Description |
|---        |--- |
|$ERRMES    | Error message |
|$ERRFILE    | File where the error has been detected |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.check.rights"}<!-- your template -->{/loop}```

[colissimo.pickup.point]

### Input arguments

|Argument |Description |
|---      |--- |
|**area_id** | Mandatory. Id of the area we want to know the price slices of |

### Output arguments

|Variable   |Description |
|---        |--- |
|$SLICE_ID | The ID of this price slice |
|$MAX_WEIGHT | Max cart weight for the price slice |
|$MAX_PRICE | Max cart price for the price slice |
|$PRICE | Delivery price for this price slice |
|$FRANCO | UNUSED |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point"}<!-- your template -->{/loop}```

[colissimo.pickup.point.id]

### Input arguments

None

### Output arguments

|Variable   |Description |
|---        |--- |
|$MODULE_ID    | Id of the ColissimoPickupPoint module |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.id"}<!-- your template -->{/loop}```

[colissimo.pickup.point.around]

### Input arguments

|Argument |Description |
|---      |--- |
|**countryid** | Country ID of where the search location is |
|**zipcode** | Zipcode of the searched city |
|**city** | Name of the searched city |
|**address** | Id of the address to use for the search. Cannot be used at the same time as zipcode + city|

### Output arguments

|Variable   |Description |
|---        |--- |
|$LONGITUDE    | longitude of the pickup point relay |
|$LATITUDE    | latitude of the pickup point relay |
|$CODE    | ID of the pickup point relay |
|$ADDRESS    | address of the pickup point relay |
|$ZIPCODE    | zipcode of the pickup point relay |
|$CITY    | city of the pickup point relay |
|$DISTANCE    | distance between the relay point and the customer's address/searched address |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.around"}<!-- your template -->{/loop}```

[address.colissimo.pickup.point]

### Input arguments

The same as the loop address

### Output arguments

The same as the loop address, but with the pickup point's address

### Exemple
```{loop name="yourloopname" type="address.colissimo.pickup.point"}<!-- your template -->{/loop}```

[order.notsent.colissimo.pickup.point]

### Input arguments

None

### Output arguments

The same as the loop order, but with not sent ColissimoPickupPoint orders.

### Exemple
```{loop name="yourloopname" type="order.notsent.colissimo.pickup.point"}<!-- your template -->{/loop}```

[colissimo.pickup.point.order_address]

### Input arguments

|Argument |Description |
|---      |--- |
|**id** | Mandatory. ID of the OrderAddressColissimoPickupPoint that should be retrieved by the loop. |

### Output arguments

|Variable   |Description |
|---        |--- |
|$ID    | OrderAddressColissimoPickupPoint ID. |
|$CODE    | OrderAddressColissimoPickupPoint code. |
|$TYPE    | OrderAddressColissimoPickupPoint type. |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.order_address"}<!-- your template -->{/loop}```

[colissimo.pickup.point.area.freeshipping]

### Input arguments

|Argument |Description |
|---      |--- |
|**area_id** | Id of the area we want to know if freeshipping from is active |

### Output arguments

|Variable   |Description |
|---        |--- |
|$ID    | ColissimoPickupPointAreaFreeshipping ID. |
|$AREA_ID    | The area ID. |
|$CART_AMOUNT    | The minimum cart amount to have free shipping for this area. |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.area.freeshipping"}<!-- your template -->{/loop}```

[colissimo.pickup.point.freeshipping]

### Input arguments

|Argument |Description |
|---      |--- |
|**id** | Should always be 1. |

### Output arguments

|Variable   |Description |
|---        |--- |
|$FREESHIPPING_ACTIVE    | Whether free shipping is activated with no restrictions on all area. |
|$FREESHIPPING_FROM    | The minimum cart amount to have free shipping on all alreas. |

### Exemple
```{loop name="yourloopname" type="colissimo.pickup.point.freeshipping"}<!-- your template -->{/loop}```

##Plugins Smarty

[colissimoPickupPointDeliveryPrice]
### Input arguments

|Argument |Description |
|---      |--- |
|**country** | The country ID from which you want to get the delivery prices. Defaults to store country |

### Output arguments

|Variable   |Description |
|---        |--- |
|$isValidMode    | Whether the delivery is valid for the cart in session and the chosen country. |
|$deliveryPrice    | The delivery price for the cart in session in the chosen country. |

### Exemple
```{colissimoPickupPointDeliveryPrice country=64}```


##Integration
A integration example is available for the default theme of Thelia.
To install it, copy the files of pathToColissimoPickupPoint/templates/frontOffice/default and
pathToColissimoPickupPoint/templates/frontOffice/default/ajax respectively in pathToThelia/templates/frontOffice/default
and pathToThelia/templates/frontOffice/default/ajax


