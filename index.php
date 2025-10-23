<?php
class Violation
{
//private string $name;
private function __construct(string $name)
{
$this->name = $name;
}
public static function NO_USER_ID_FIELD(): self
{
return new self('NO_USER_ID_FIELD');
}
public static function INVALID_USER_ID_TYPE(): self
{
return new self('INVALID_USER_ID_TYPE');
}
public static function INVALID_USER_10_VALUE(): self
{
return new self('INVALID_USER_10_VALUE');
}
public static function NO_CART_FIELD(): self
{
return new self('NO_CART_FIELD');
}
public static function INVALID_CART_TYPE(): self
{
return new self('INVALID_CART_TYPE');
}
public static function INVALID_DATE_CREATED(): self
{
return new self('INVALID_DATE_CREATED');
}
public static function NO_PRODUCT_ID_FIELD(): self
{
return new self('Product ID must be a positive integer.');
}
public static function INVALID_PRODUCT_ID_TYPE(): self
{
return new self('INVALID_PRODUCT_ID_TYPE');
}
public static function INVALID_PRODUCT_ID_VALUE(): self
{
return new self('INVALID_PRODUCT_ID_VALUE');
}
public static function NO_QUANTITY_FIELD(): self
{
return new self('NO_QUANTITY_FIELD');
}
public static function INVALID_QUANTITY_TYPE(): self
{
return new self('INVALID_QUANTITY_TYPE');
}
public static function INVALID_QUANTITY_VALUR(): self
{
return new self('INVALID_QUANTITY_VALUR');
}
public static function INVALID_ITEM_TYPE(): self
{
return new self('INVALID_ITEM_TYPE');
}
public function name(): string
{
return $this->name;
}
public function __toString(): string
{
return $this->name;
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
} elseif (!is_int($data['userId'])) {
$violations[] = Violation::INVALID_USER_ID_TYPE();
} elseif ($data['userId'] <= 0) {
$violations[] = Violation::INVALID_USER_10_VALUE();
}
// 2. Check cart
if (!array_key_exists('cart', $data)) {
$violations[] = Violation::NO_CART_FIELD();
} elseif (!is_array($data['cart'])) {
$violations[] = Violation::INVALID_CART_TYPE();
} else {
foreach ($data['cart'] as $item) {
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
} else {
$date = date_create($data['dateCreated']);
if (!$date || date_format($date, 'Y-m-d\TH:i:s\Z') !== $data['dateCreated']) {
$violations[] = Violation::INVALID_DATE_CREATED();
}
}
return $violations;
}
}
//$obj = new Violation();
$json = '{
"userId": 10,
"cart": [{"productId": "abc", "quantity": -1}],
"dateCreated": "invalid-date"
}';
$errors = Violation::validateOrderRequest($json);
foreach ($errors as $error) {
echo $error->name . "<br>";
//print_r( $error->name . "\n");
}
?>