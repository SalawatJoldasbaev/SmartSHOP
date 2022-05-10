<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models {
    /**
     * App\Models\Basket
     *
     * @property int $id
     * @property int $user_id
     * @property int $employee_id
     * @property mixed $card
     * @property mixed $cash
     * @property mixed $debt
     * @property mixed $remaining_debt
     * @property mixed $paid_debt
     * @property string|null $term
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Basket newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Basket newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Basket query()
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereCard($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereCash($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereDebt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereEmployeeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket wherePaidDebt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereRemainingDebt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereTerm($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Basket whereUserId($value)
     */
    class Basket extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Cashier
     *
     * @property int $id
     * @property string $date
     * @property float $balance
     * @property float $profit
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier query()
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereBalance($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereProfit($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Cashier whereUpdatedAt($value)
     */
    class Cashier extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Category
     *
     * @property int $id
     * @property int $parent_id
     * @property string $name
     * @property float $min_percent
     * @property float $max_percent
     * @property float $whole_percent
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Category query()
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereMaxPercent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereMinPercent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereWholePercent($value)
     */
    class Category extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Code
     *
     * @property int $id
     * @property int|null $warehouse_id
     * @property int $unit_id
     * @property int $product_id
     * @property string $code
     * @property array|null $cost_price
     * @property float $count
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Code newCode()
     * @method static \Illuminate\Database\Eloquent\Builder|Code newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Code newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Code query()
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereCostPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereUnitId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Code whereWarehouseId($value)
     */
    class Code extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Consumption
     *
     * @property int $id
     * @property int $employee_id
     * @property int $consumption_category_id
     * @property string $date
     * @property mixed $price
     * @property string|null $description
     * @property string $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption query()
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereConsumptionCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereEmployeeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Consumption whereUpdatedAt($value)
     */
    class Consumption extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\ConsumptionCategory
     *
     * @property int $id
     * @property string $name
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory query()
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ConsumptionCategory whereUpdatedAt($value)
     */
    class ConsumptionCategory extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Currency
     *
     * @property int $id
     * @property string $name
     * @property string $code
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Forex[] $rate
     * @property-read int|null $rate_count
     * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUpdatedAt($value)
     */
    class Currency extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Employee
     *
     * @property int $id
     * @property string $name
     * @property string $phone
     * @property string|null $password
     * @property string $pincode
     * @property float|null $salary
     * @property float|null $flex
     * @property string $role
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
     * @property-read int|null $tokens_count
     * @method static \Database\Factories\EmployeeFactory factory(...$parameters)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFlex($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePhone($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePincode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereRole($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereSalary($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
     */
    class Employee extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Forex
     *
     * @property int $id
     * @property int $currency_id
     * @property int $to_currency_id
     * @property float $rate
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Currency|null $currency
     * @method static \Illuminate\Database\Eloquent\Builder|Forex newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Forex newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Forex query()
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereCurrencyId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereRate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereToCurrencyId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Forex whereUpdatedAt($value)
     */
    class Forex extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Order
     *
     * @property int $id
     * @property int $basket_id
     * @property int $user_id
     * @property int $product_id
     * @property int $unit_id
     * @property float $count
     * @property mixed $price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Order query()
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereBasketId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereUnitId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
     */
    class Order extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Product
     *
     * @property int $id
     * @property int $category_id
     * @property string|null $image
     * @property string $name
     * @property string|null $brand
     * @property array $cost_price
     * @property array $min_price
     * @property array $max_price
     * @property array $whole_price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Product query()
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereBrand($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereCostPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereMaxPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Product whereWholePrice($value)
     */
    class Product extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Salary
     *
     * @property int $id
     * @property int $employee_id
     * @property string $date
     * @property float $salary
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Salary newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Salary newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Salary query()
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereEmployeeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereSalary($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Salary whereUpdatedAt($value)
     */
    class Salary extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Unit
     *
     * @property int $id
     * @property string $name
     * @property string $unit
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Unit newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Unit newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Unit query()
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUnit($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedAt($value)
     */
    class Unit extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\User
     *
     * @property int $id
     * @property string $full_name
     * @property string $phone
     * @property string $type
     * @property int|null $tin
     * @property float $balance
     * @property string|null $about
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
     * @property-read int|null $tokens_count
     * @method static \Database\Factories\UserFactory factory(...$parameters)
     * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User query()
     * @method static \Illuminate\Database\Eloquent\Builder|User whereAbout($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereFullName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereTin($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
     */
    class User extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\Warehouse
     *
     * @property int $id
     * @property int $product_id
     * @property int $unit_id
     * @property float $count
     * @property array $codes
     * @property string $date
     * @property int $active
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse setWarehouse($product_id, $code, $count, $unit_id)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCodes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUnitId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedAt($value)
     */
    class Warehouse extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\WarehouseBasket
     *
     * @property int $id
     * @property int $employee_id
     * @property string $date
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket query()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereEmployeeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseBasket whereUpdatedAt($value)
     */
    class WarehouseBasket extends \Eloquent
    {
    }
}

namespace App\Models {
    /**
     * App\Models\WarehouseOrder
     *
     * @property int $id
     * @property int $warehouse_basket_id
     * @property int $product_id
     * @property int $unit_id
     * @property float $count
     * @property string $code
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereUnitId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WarehouseOrder whereWarehouseBasketId($value)
     */
    class WarehouseOrder extends \Eloquent
    {
    }
}
