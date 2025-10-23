<?php
class Violation
{
    const INVALID_USER_ID = 'INVALID_USER_ID';
    const INVALID_CART_TYPE = 'INVALID_CART_TYPE';
    const INVALID_ITEM_TYPE = 'INVALID_ITEM_TYPE';
    const INVALID_PRODUCT_ID = 'INVALID_PRODUCT_ID';
    const INVALID_QUANTITY = 'INVALID_QUANTITY';
    const INVALID_DATE_CREATED = 'INVALID_DATE_CREATED';

    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function INVALID_USER_ID()
    {
        return ['code' => self::INVALID_USER_ID, 'message' => 'User ID must be a positive integer.'];
    }

    public static function INVALID_CART_TYPE()
    {
        return ['code' => self::INVALID_CART_TYPE, 'message' => 'Cart must be an array of items.'];
    }

    public static function INVALID_ITEM_TYPE()
    {
        return ['code' => self::INVALID_ITEM_TYPE, 'message' => 'Each item in the cart must be an associative array.'];
    }

    public static function INVALID_PRODUCT_ID()
    {
        return ['code' => self::INVALID_PRODUCT_ID, 'message' => 'Product ID must be a positive integer.'];
    }

    public static function INVALID_QUANTITY()
    {
        return ['code' => self::INVALID_QUANTITY, 'message' => 'Quantity must be a positive integer.'];
    }

    public static function INVALID_DATE_CREATED()
    {
        return ['code' => self::INVALID_DATE_CREATED, 'message' => 'Date Created must be a valid date in YYYY-MM-DD format.'];
    }

    public static function validateOrderRequest(string $json): array
    {
        $violations = [];

        // Decode JSON
        $data = json_decode($json, true);

        if (!is_array($data)) {
        // If JSON is invalid or not an object
        return [Violation::INVALID_ITEM_TYPE()];
        }
            // 1. Check userId
            if (!array_key_exists('userId', $data)) {
                $violations[] = Violation::NO_USER_ID_FIELD();
            } 
            elseif (!is_int($data['userId'])) {
                $violations[] = Violation::INVALID_USER_ID_TYPE();
            } 
            elseif ($data['userId'] <= 0) {
                $violations[] = Violation::INVALID_USER_10_VALUE();
            }
            // 2. Check cart
            if (!array_key_exists('cart', $data)) {
                $violations[] = Violation::NO_CART_FIELD();
            } 
            elseif (!is_array($data['cart'])) {
                $violations[] = Violation::INVALID_CART_TYPE();
            } 
            else {
            foreach ($data['cart'] as $item) 
            {
                if (!is_array($item)) {
                    $violations[] = Violation::INVALID_ITEM_TYPE();
                    continue;
                }
                // productId
                if (!array_key_exists('productId', $item)) {
                $violations[] = Violation::NO_PRODUCT_ID_FIELD();
                } elseif (!is_int($item['productId'])) {
                $violations[] = Violation::INVALID_PRODUCT_ID_TYPE();
                } elseif ($item['productId'] <= 0) {
                $violations[] = Violation::INVALID_PRODUCT_ID_VALUE();
                }
                // quantity
                if (!array_key_exists('quantity', $item)) {
                $violations[] = Violation::NO_QUANTITY_FIELD();
                } elseif (!is_int($item['quantity'])) {
                $violations[] = Violation::INVALID_QUANTITY_TYPE();
                } elseif ($item['quantity'] <= 0) {
                $violations[] = Violation::INVALID_QUANTITY_VALUR();
                }
            }
        }
        // 3. Check dateCreated
        if (!array_key_exists('dateCreated', $data) || !is_string($data['dateCreated'])) {
            $violations[] = Violation::INVALID_DATE_CREATED();
        } 
        else {
                $date = date_create($data['dateCreated']);
                if (!$date || date_format($date, 'Y-m-d\TH:i:s\Z') !== $data['dateCreated']) {
                $violations[] = Violation::INVALID_DATE_CREATED();
            }
        }
        return $violations;
    }
    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

$json = '{
"userId": 10,
"cart": [{"productId":123 , "quantity": 1}],
"dateCreated": "invalid-date"
}';

$errors = Violation::validateOrderRequest($json);

var_dump($errors);

?>