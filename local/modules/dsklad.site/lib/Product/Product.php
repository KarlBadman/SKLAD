<?
namespace Dsklad;

class Product
{
    /**
     * Обновление для товара статистики по отзывам
     * @param $productId
     * @param string $deletedReviewId  ID удаляемого отзыва, если вызывается перед удалением
     * @throws \Exception
     */
    public static function updateReviewsStatistic($productId, $deletedReviewId = '')
    {
        $dbReviewElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => Config::getParam('iblock/reviews'),
                'ACTIVE' => 'Y',
                'PROPERTY_SHIPMENT' => $productId
            ],
            false,
            false,
            ['ID', 'PROPERTY_RATING']
        );
        $reviewsCount = 0;
        $reviewsAggregate = 0;
        while ($arReviewFields = $dbReviewElements->Fetch()) {
            if ($arReviewFields['ID'] != $deletedReviewId) {
                $reviewsCount++;
                $reviewsAggregate += $arReviewFields['PROPERTY_RATING_VALUE'];
            }
        }
        if ($reviewsCount > 0) {
            $reviewsAggregate = $reviewsAggregate / $reviewsCount;
        }

        \CIBlockElement::SetPropertyValuesEx(
            $productId,
            Config::getParam('iblock/catalog'),
            [
                'REVIEWS_COUNT' => $reviewsCount,
                'REVIEWS_AGGREGATE' => $reviewsAggregate
            ]
        );
    }
}