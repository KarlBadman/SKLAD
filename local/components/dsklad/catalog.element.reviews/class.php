<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
class companyReveiws
{
    const DEBUG = false;
    /**
     * @link https://developers.google.com/places/web-service/details
     * ЛК для получения ключей и настройки OAuth 2.0
     * @link https://console.developers.google.com/apis/credentials?authuser=0&project=data-avatar-217514
     * справки по отзывам для mybusiness
     * @link https://stackoverflow.com/questions/48007574/how-to-use-google-my-business-api-to-get-the-reviews-and-reply-to-that
     * @link https://developers.google.com/my-business/reference/rest/v4/accounts.locations.reviews
     * Справка по OAuth 2.0 с библиотеками
     * @link https://developers.google.com/identity/protocols/OAuth2
     * При необходимости работы через mybusiness, который предоставляет возможность сортировок возрващаемых параметров, похоже необходимо реализовать подключение через OAuth на сервера гугл, получить токен и подставить его в ссылку
     *
     * @param $params
     * @return string
     */
    public function getReviewsUrl($params)
    {
        if ($params['USE_MYBUSINESS'] == 'Y') {
            $url = 'https://mybusiness.googleapis.com/v4/accounts/' . $params['ACCOUNT_ID'] . '/locations/' . $params['LOCATION_ID'] . '/reviews?access_token=' . $params['AUTH_TOKEN'];
        } else {
            $fields = '&fields=rating,user_ratings_total,reviews&language=ru';
            $url = 'https://maps.googleapis.com/maps/api/place/details/json?placeid=' . $params['PLACE_ID'] . $fields . '&key=' . $params['AUTH_TOKEN'];
        }
        return $url;
    }

    public function getGoogleReviews($params)
    {
        $url = self::getReviewsUrl($params);
        if(self::DEBUG) {
            $result = self::getExampleByPlacesApi();
            return json_decode($result, true);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            #@TODO тут логировать о проблеме, если это нужно
        }
        curl_close($ch);
        return json_decode($result, true);
    }

    public function getExampleByPlacesApi()
    {
        $response = '{
           "html_attributions" : [],
           "result" : {
              "rating" : 4.7,
              "reviews" : [
                 {
                    "author_name" : "Марина Баданина",
                    "author_url" : "https://www.google.com/maps/contrib/114130535622557375464/reviews",
                    "language" : "ru",
                    "profile_photo_url" : "https://lh3.googleusercontent.com/-GuyXp4yERo4/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3rf1VQKVlqCzgZrApGpjd4rAGqQH8w/s128-c0x00000000-cc-rp-mo/photo.jpg",
                    "rating" : 5,
                    "relative_time_description" : "3 недели назад",
                    "text" : "Модная мебель и предметы декора, удобная навигация по сайту, большой ассортимент, доступные цены, и яркий Инстаграм аккаунт с реальными отзывами довольных  покупателей. В магазине часто проходят акции и розыгрыши. \nЯ заказала стол и стулья, стол был в наличии в интернет магазине, но при проверке заказа перед отправкой  обнаружилось, что к нему не хватает комплектующих, о чем меня уведомили по телефону и предложили подождать следующей поставки. Однако поставка задерживалась, а в это время в магазине проходила акция и мои стулья стали стоить дешевле. Я немного расстроилась, ведь за стулья я уже заплатила, а по факту их не получила. В итоге я нашла подходящий стол в другом магазине, и решила отменить заказ на стол.  Мне без вопросов отменили заказ, пересчитали стоимость стульев с учетом скидки, и на следующий день они уже стояли на моей кухне! Девочки консультанты просто умнички, вежливые, компетентные очень приятно было с ними общаться. Доставка тоже порадовала, курьер доставил заказ прямо до квартиры. Стулья тщательно упакованы, все комплектующие на месте. \nОднозначно рекомендую этот интернет магазин. Удачных вам покупок!",
                    "time" : 1563657007
                 },
                 {
                    "author_name" : "Артем Б",
                    "author_url" : "https://www.google.com/maps/contrib/103669447799466125129/reviews",
                    "language" : "ru",
                    "profile_photo_url" : "https://lh3.googleusercontent.com/-OeNXSyCxjao/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3reeOtQO2IOTsUKtY9vNaIBj_0Ua5w/s128-c0x00000000-cc-rp-mo/photo.jpg",
                    "rating" : 5,
                    "relative_time_description" : "меньше недели назад",
                    "text" : "Мною был приобретён комплект стол и 4 стула Eames Style. Доставка осуществлялась всего 3 дня, все было аккуратно упаковано и уложено в коробки. Кстати, коробки достаточно лёгкие. Комплект очень добротный, красивый и комфортный. Я осталась довольна покупкой и качеством мебели. Отдельное спасибо прекрасной команде магазина за оперативность и профессионализм!",
                    "time" : 1565294598
                 },
                 {
                    "author_name" : "Violetta Balakireva",
                    "author_url" : "https://www.google.com/maps/contrib/104753532438027393231/reviews",
                    "language" : "ru",
                    "profile_photo_url" : "https://lh5.googleusercontent.com/-N3zCO_cxKIo/AAAAAAAAAAI/AAAAAAAAAkk/NsYnY3Z3eqk/s128-c0x00000000-cc-rp-mo/photo.jpg",
                    "rating" : 5,
                    "relative_time_description" : "меньше недели назад",
                    "text" : "Хороший сайт с удобным интерфейсом. Очень приятные менеджеры! Доставку заказывала до пункта самовывоза. Всё привезли в срок. Стулья были упакованы качественно. Уже рекомендую этот магазин свои друзьям и коллегам!",
                    "time" : 1565356613
                 },
                 {
                    "author_name" : "Виталий Куракин",
                    "author_url" : "https://www.google.com/maps/contrib/110823420226147437504/reviews",
                    "language" : "ru",
                    "profile_photo_url" : "https://lh4.googleusercontent.com/-5pUk9GHOz1M/AAAAAAAAAAI/AAAAAAAAAHE/-6_t6b0gqZc/s128-c0x00000000-cc-rp-mo/photo.jpg",
                    "rating" : 5,
                    "relative_time_description" : "меньше недели назад",
                    "text" : "Покупатель из г. Калуги. Приобретала стол и стулья. Благодарю Интернет-магазин. Искренне довольна взаимоотношениями. Очень клиентоориентированные и профессионально грамотные менеджеры, сориентировали по ассортименту, дали полезные советы по доставке, выслали договор , были всегда на связи. По срокам все было четко соблюдено. Товар был упакован очень аккуратно.  Качеством товара тоже очень довольна, в своем городе не могла найти требуемый функционал.",
                    "time" : 1565356232
                 },
                 {
                    "author_name" : "Юлия Кубарева",
                    "author_url" : "https://www.google.com/maps/contrib/114159316408350142459/reviews",
                    "language" : "ru",
                    "profile_photo_url" : "https://lh4.googleusercontent.com/-vYzzfgDJntQ/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3rcl4pQphajzjtDV0lc34llvZbHp6Q/s128-c0x00000000-cc-rp-mo/photo.jpg",
                    "rating" : 5,
                    "relative_time_description" : "меньше недели назад",
                    "text" : "Осталась очень довольна магазином. Заказывала стульчик, очень быстро доставили . Все пришло в целости и сохранности. Сам стульчик удобный и качественный, отлично вписался в интерьер.\n100% рекомендую и сама буду заказывать еще👍🏻",
                    "time" : 1565166528
                 }
              ],
              "user_ratings_total" : 215
           },
           "status" : "OK"
        }';
        return $response;
    }
}