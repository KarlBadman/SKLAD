# swebs.helper
Модуль для [1С-Битрикс](http://www.1c-bitrix.ru/). Он не несёт в себе ничего нового, кроме "синтаксического сахара" используемого автором [API](http://dev.1c-bitrix.ru/api_help/) cms [1С-Битрикс](http://www.1c-bitrix.ru/).

Распространяется под лицензией [MIT](https://en.wikipedia.org/wiki/MIT_License). Автор не принимает на себя никаких гарантийных обязательств в отношении данного модуля и не несет ответственности за:

  * любой прямой или косвенный ущерб и упущенную выгоду, даже если это стало результатом использования или невозможности использования модуля;
  * убытки, включая общие, предвидимые, реальные, прямые, косвенные и прочие убытки, включая утрату или искажение информации, убытки, понесенные Пользователем или третьими лицами, невозможность работы модуля и несовместимость с любым другим модулем и т.д.
  * за любые повреждения оборудования или программного обеспечения Пользователя, возникшие в результате использовании модуля.

-----------------------------------

**Краткий перечень классов и методов:**

## Swebs\Helper\Highload\Element
```php
Element::getElement($intIblockID, $arFilter, $arSelect, $intLimit)
```
_Возвращает элементы Highload инфоблока в виде массива. При необходимости можно использовать фильтрацию, указать нужные поля и ограничить количество._

## Swebs\Helper\Iblock\Element
```php
Element::delete($arIDs)
```
_Пакетное удаление элементов информационных блоков._
```php
Element::getFieldsByID($intElementID, $strFieldName = '')
```
_Возвращает конкретное поле или массив полей элемента информационного блока._
```php
Element::getPropertiesByID($intElementID, $strPropertyName = '')
```
_Возвращает конкретное свойство или массив свойств элемента информационного блока._

## Swebs\Helper\Iblock\Section
```php
Section::delete($arIDs)
```
_Пакетное удаление разделов информационных блоков._
```php
Section::getFieldsByID($intSectionID, $strFieldName = '')
```
_Возвращает конкретное поле или массив полей раздела информационного блока._

## Swebs\Helper\IO\Serialize
```php
Serialize::write($strName, $obData)
```
_Сохраняет любой объект в виде файла в upload_
```php
Serialize::ride($strName)
```
_Получает любой сохранённый предыдущим методом объект из файла в upload._

## Swebs\Helper\Sale\Order
```php
Order::getPropertyValueByCode($obOrder, $strCode)
```
_Возвращает значение свойства заказа. На вход принимает объект заказа ([d7](http://dev.1c-bitrix.ru/api_d7/bitrix/sale/order/index.php)) и символьный код свойства._
```php
Order::setPropertyValueByCode($obOrder, $strCode, $strValue)
```
_Записывает значение свойства заказа. На вход принимает объект заказа ([d7](http://dev.1c-bitrix.ru/api_d7/bitrix/sale/order/index.php)), символьный код свойства и значение._
```php
Order::getDeliveries($intUserID = NULL)
```
_Возвращает массив объектов доставок с учётом ограничений. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним._
```php
Order::getPaySystems($intUserID = NULL, $intDeliveryID = false)
```
_Возвращает массив платёжных систем с учётом ограничений. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним. Необходимо передать id службы доставки если используется ограничение по доставке._
```php
Order::simpleOrder($intUserID = NULL, $arProperties)
```
_Создаёт простой заказ. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним. Массив $arProperties обязательно должен имет заполненными ключи 'DELIVERY_ID', 'PAYMENT_ID'. Из не обязательных 'PERSONAL_ID', 'COUPON'. Так же можно передать в ключе 'ORDER_PROPERTIES' масив со свойствами заказа, а в ключе 'ORDER_FIELDS' массив с полями заказа. Массив должен иметь формат 'CODE' => 'VALUE'. Метод создавался для часто используемого функционала "Заказ в один клик". Для более сложной задачи создания заказа он вряд ли подойдёт._
```php
Order::byOneClick($intUserID = NULL, $arProperties)
```
_Создаёт быстрый заказ. От предыдущего метода отличается необходимостью передать в ключе 'PRODUCT_ID' в массиве $arProperties идентификатор товара. Метод создавался для часто используемого функционала "Заказ в один клик"._

## Swebs\Helper\Sale\Price
```php
Price::setMinMax($intIblockElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
```
_Заполняет указанные свойства товара минимальной и максимальной ценой из всех имеющихся предложений данного товара._

## Swebs\Helper\Others\Cookie
```php
Cookie::getCookie($strName = false)
```
_Получает конктретно указанный cookie или массив всех имеющихся._
```php
Cookie::setCookie($strName, $strValue, $strDomain = '')
```
_Записывает данные в cookie._

## Swebs\Helper\Others\Strings
```php
Strings::getStringOfNum($intNum)
```
_Возвращает прописью переданное число._

## Swebs\Helper\Main\User
```php
User::getID($isAllowAnonymous = false)
```
_Возвращает id текущего пользователя или NULL, если передан параметр $isAllowAnonymous как true, то в случае отутсвия пользователя создаст анонима и вернёт его id._