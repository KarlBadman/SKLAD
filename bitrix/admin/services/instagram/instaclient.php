<?

    namespace Insta;

    use \Bitrix\Main\Application;
    use \Bitrix\Main\Config\Option;
    use \Instagram\Auth;
    use \Instagram\Instagram;
    use \Bitrix\Highloadblock;
    use \Bitrix\Main\Entity;
    
    class ClientInterface extends ClientInterfaceModel {
        
        protected $authConfig;
        
        protected $authHandler;
        
        protected $instaHandler;
        
        public $viewDisabled; 
        
        function __construct ($scope = []) {
            
            session_start(); global $APPLICATION;
            
            $this->file = realpath(__DIR__ )."/img/content.png";
            
            $this->urlPath = substr($_SERVER['PHP_SELF'], 0, strripos($_SERVER['PHP_SELF'], '/') + 1);

            $this->defaultDateFormat = "d.m.Y H:i:s";
            
            $this->configKeys = [
                'client_id', 
                'client_secret', 
                'redirect_url', 
                'scope', 
                'iblock_id', 
                'images_count',
                'client_access_token',
                'users_count'
                // '',
            ];
            
            $this->authConfig = array(
                'client_id' => $this->getSettings('client_id'), 
                'client_secret' => $this->getSettings('client_secret'), 
                'redirect_uri' => $this->getSettings('redirect_url'),
                'scope' => array_map(function ($item) {
                    return trim($item);
                }, explode(',', $this->getSettings('scope')))
            );
            
            $this->authHandler = new Auth($this->authConfig);
        
            $this->isAuthorised = $this->getAuthToken() ? true : false;
    
            $this->miniRouter();
            
            // if (!$this->isAuthorised)
                // LocalRedirect("/bitrix/admin/services/instagram/");
            
            $this->viewDisabled = $this->getSettings('images_count') ? true : false;
            
        }
        
        /**
            Mini router by class actions
            @param $case | string
        */ 
        function miniRouter ($case = "") {
            
            switch ($this->getCurrentRequest()->getQuery('ACTION')) {
                
                case ('CHECK_AUTH') : $this->authAction();
                    break;
                case ('GET_MEDIA') : $this->getMediaAction();
                    break;
                case ('GET_TAG') : $this->isTagView = true;
                    break;
                case ('GET_LOCATION') : $this->isLocationView = true;
                    break;
                case ('GET_CURRENT_USER') : $this->currentUserAction();
                    break;
                case ('GET_USER_BY_ID') : $this->userByIDAction();
                    break;
                case ('SETTINGS') : $this->settingsGetAction();
                    break;
                case ('SETTINGS_SAVE') : $this->settingsSaveAction();
                    break;
                case ('WIDGET') : $this->widgetAction();
                    break;
                case ('LOGOUT') : $this->logoutAction();
                    break;
                case ('AJAX_CALL') : $this->ajaxCallAction(); 
                    break;
            }
        }
        
        /**
            Settings SAVE action
        */ 
        function settingsSaveAction () {
            
            $this->isSettingsView = true;
            $settings = $this->getCurrentRequest()->getQueryList();
            foreach ($settings as $key => $value)
                $this->setSettings($key, $value);
            
            $this->logoutAction();
                
            LocalRedirect($this->urlPath . '?ACTION=SETTINGS', 'refresh');
        } 
        
        /**
            Logout action
        */ 
        function logoutAction () {
            
            unset($_SESSION['instagram_access_token']);
            $this->setSettings("client_access_token", "");
            $this->instaHandler->logout();
            LocalRedirect($this->urlPath, 'refresh');
            
        }
        
        /**
            Settings get action
        */ 
        function settingsGetAction () {
            
            $this->isSettingsView = true;
            $this->isCurrentPage = "SETTINGS";
            foreach ($this->configKeys as $key)
                $this->config[$key] = $this->getSettings($key);
                
            $this->config['iblocks'] = $this->getHLIblockInfo($this->config['iblock_id']);
        }
        
        /**
            Auth action
        */ 
        function authAction () {
            
            if (!$this->getAuthToken()) {
                
                $this->authHandler->authorize();
                
            } else {
                
                $this->isAuthorised = true;
                LocalRedirect($this->urlPath, 'refresh');
                
            }
        }
        
        /**
            Current user action
        */ 
        function currentUserAction () {
            
            $this->isCurrentUserView = true;
            $this->isCurrentPage = "GET_CURRENT_USER";
            $currentUser = $this->getCurrentUserInfo();
            $this->currentUserInfo = [
                'profile_picture' => $currentUser->getProfilePicture() ? : false,
                'user_name' => $currentUser->getUserName() ? : "- засекречено -",
                'full_name' => $currentUser->getFullName() ? : "- засекречено -",
                'bio' => $currentUser->getBio()  ? : "- засекречено -",
                'web' => $currentUser->getWebsite() ? : "- засекречено -",
                'follows' => $currentUser->getFollowsCount() ? : "- засекречено -",
                'followers' => $currentUser->getFollowersCount() ? : "- засекречено -",
                'media_count' => $currentUser->getMediaCount() ? : "- засекречено -",
            ];
        }
        
        /**
            User by ID action
        */ 
        function userByIDAction () {
            
            $this->isUserByIDView = true;
            $this->isCurrentPage = "GET_USER_BY_ID";
        }
        
        /**
            Media action
        */ 
        function getMediaAction () {

            $this->isMediaView = true;
            $currentID = $this->getCurrentRequest()->getQuery('max_id') ? : false;
            $_SESSION['prev_id'][0] = false;
            $this->prevPageID = $currentID ? $_SESSION['prev_id'][$currentID] : $_SESSION['prev_id'][0];
            $this->isCurrentPage = "GET_MEDIA";
            $userID = $this->getCurrentUserInfo()->getId();
            $this->user = $this->getUserInfo($userID);
            $media = $this->user->getMedia([
                'count' =>18,
                'max_id' => $currentID ? : ''
            ]);
            $this->currentMedia = $this->dataParse($media->getData());
            $this->nextPageID = $media->getNext();
            $_SESSION['prev_id'][$currentID] = $currentID;

        } 
        
        /**
            Widget action
        */ 
        function widgetAction () {
            
            $this->widgetMedia = [];
            $this->widgetMediaCount = $this->getSettings('images_count') ? : 5;
            $this->isWidgetView = true;
            $this->isCurrentPage = "WIDGET";
            $rsData = $this->mediaGet();
            if (is_array($rsData) && !empty($rsData)) {
                $this->widgetMedia = array_map(function ($item) {
                    if (!empty($item['UF_IMG'])) {
                        $picture = \CFile::GetFileArray($item['UF_IMG']);
                        $item['UF_IMG'] = [
                            'WIDTH' => $picture['WIDTH'],
                            'HEIGHT' => $picture['HEIGHT'],
                            'SRC' => $picture['SRC']
                        ];
                    }
                    return $item;
                }, $rsData); 
            }
        }
        
        /**
            Ajax call action
        */ 
        function ajaxCallAction () {
            
            global $APPLICATION; $APPLICATION->RestartBuffer();
            $request = $this->getCurrentRequest()->getQueryList();
            $this->ajaxMethodErrorMsg = "";
            
            // AJAX MINI ROUTER
            if (!empty($request['METHOD'])) {
                
                if ($request['METHOD'] == 'IBLOCKADD') {
                    // TODO add iblock modified
                    // $respond = $this->hlIblockAdd($_REQUEST['iblock_name']);
                }
                
                if ($request['METHOD'] == 'MEDIAADD') {
                    $respond = $this->mediaAdd($_REQUEST['data-source'], $_REQUEST['data-img']);
                }
                
                if ($request['METHOD'] == 'MEDIAUPDATE') {
                    
                    $_requestFields = [
                        "img-id" => $_REQUEST["img-id"],
                        "popup-left-position" => $_REQUEST["popup-left-position"],
                        "popup-top-position" => $_REQUEST["popup-top-position"],
                        "tag" => $_REQUEST["tag"],
                        "tag-link" => $_REQUEST["tag-link"],
                        "popup-text" => $_REQUEST["popup-text"],
                        "popup-link" => $_REQUEST["popup-link"],
                    ];
                    
                    $respond = $this->mediaUpdate($_REQUEST['id'], $_requestFields);
                }
                
                if ($request['METHOD'] == 'MEDIADROP') {
                    // TODO if this need
                    // $respond = $this->mediaDrop($_REQUEST['media_id']);
                }
                
                $result = [
                    "status" => $respond,
                    "errorText" => $this->ajaxMethodErrorMsg
                ];
                
            } else {
                
                $result = [
                    "status" => false,
                    "errorText" => "Datakey \"METHOD\" do not getting by ajax_call request"
                ];
            }
            
            echo json_encode($result);
            die();
        }
        
    }
    
    class ClientInterfaceModel {
        
        /**
            Iblock add HL
            @param $iblock_name
            @return mixed
        */ 
        function hlIblockAdd ($iblock_name = "") {
            
            $result = "Iblock_name field is empty or not valid"; $userFieldsEntity = new \CUserTypeEntity();
            $iblock_name = str_ireplace(' ', '_', trim(strip_tags(strtoupper($iblock_name))));
            
            if (!empty($iblock_name)) {
                
                $getListArray = [
                    'filter' => [
                        'TABLE_NAME' => strtolower($iblock_name),
                    ]
                ];
                $hl = Highloadblock\HighloadBlockTable::getList($getListArray)->fetchAll();

                if (count ($hl) == 0) {

                    $hlArray = [
                        'NAME' => $iblock_name,
                        'TABLE_NAME' => strtolower($iblock_name),
                    ];  
                    $res = Highloadblock\HighloadBlockTable::add($hlArray);
                    
                    if ($res->isSuccess()) {
                        $userFields = [
                            [
                                'ENTITY_ID' => 'HLBLOCK_' . $res->getId(),
                                'FIELD_NAME' => 'UF_IMG',
                                'USER_TYPE_ID' => 'file',
                                'MANDATORY' => 'N',
                                'SHOW_FILTER' => 'Y',
                                'EDIT_FORM_LABEL' => ['ru' => 'Картинка', 'en' => 'Picture']
                            ],[
                                'ENTITY_ID' => 'HLBLOCK_' . $res->getId(),
                                'FIELD_NAME' => 'UF_PICTURE_SORT',
                                'USER_TYPE_ID' => 'integer',
                                'MANDATORY' => 'N',
                                'SHOW_FILTER' => 'Y',
                                'EDIT_FORM_LABEL' => ['ru' => 'Сортировка', 'en' => 'Sort']
                            ],[
                                'ENTITY_ID' => 'HLBLOCK_' . $res->getId(),
                                'FIELD_NAME' => 'UF_PICTURE_POINT',
                                'USER_TYPE_ID' => 'string',
                                'MANDATORY' => 'N',
                                'SHOW_FILTER' => 'Y',
                                'EDIT_FORM_LABEL' => ['ru' => 'Метка', 'en' => 'Point']
                            ],[
                                'ENTITY_ID' => 'HLBLOCK_' . $res->getId(),
                                'FIELD_NAME' => 'UF_PICTURE_ID',
                                'USER_TYPE_ID' => 'string',
                                'MANDATORY' => 'N',
                                'SHOW_FILTER' => 'Y',
                                'EDIT_FORM_LABEL' => ['ru' => 'ID instagram', 'en' => 'ID instagram']
                            ]
                        ];
                        foreach ($userFields as $field)
                            $userFieldsEntity->add($field);
                        
                        $result = true; 
                    }
                    else 
                        $result = $res->getErrorMessages();
                    
                } else 
                    $result = true;
            }
            
            return $result;
        }
        
        /**
            Get Iblock entity class
            @return mixed
        */ 
        function getHlEntityClass () {
            if (empty($this->getSettings('iblock_id')) || $this->getSettings('iblock_id') < 1)
                return false;
            $hlblock = Highloadblock\HighloadBlockTable::getById($this->getSettings('iblock_id'))->fetch();
            return Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();
        }
        
        /**
            Media add method
            @param $data | string
            @param $imgdata | string
            @return mixed
        */ 
        function mediaAdd ($source = "", $imgdata = "") {
            $hlEntityClass = $this->getHlEntityClass();
            $unserialisedSource = json_decode($source);
            
            if (empty($imgdata)) {
                $this->ajaxMethodErrorMsg = "You are not crop image, before saving result";
                return false;
            }
            
            if ($hlEntityClass && !empty($unserialisedSource->ID)) {
                
                $rsData = $this->mediaGet(['UF_IMG_ID' => $unserialisedSource->ID]);
                
                if (is_array($rsData) && count ($rsData) == 0) {
                    
                    $data = explode(',', $imgdata);
                    file_put_contents($this->file, base64_decode($data[1]));
                    
                    if ($hlEntityClass::add([
                        "UF_IMG" => \CFile::MakeFileArray($this->file),
                        "UF_LIKES" => intVal($unserialisedSource->LIKES),
                        "UF_TAG" => $unserialisedSource->DISPLAY_TAGS,
                        "UF_TAG_LINK" => $unserialisedSource->LINK,
                        "UF_IMG_ID" => $unserialisedSource->ID,
                        "UF_INSTAGRAM_ORIGIN" => $unserialisedSource->ORIGIN_SRC->SRC,
                        "UF_INSTA_LINK" => $unserialisedSource->LINK
                    ])) {
                        
                        $this->ajaxMethodErrorMsg = "Media file is success added, congratulations!!!";
                        return true;
                    }
                    
                    
                } else {
                    
                    $this->ajaxMethodErrorMsg = "This media is already exists";
                    return false;
                }
            }
            
            $this->ajaxMethodErrorMsg = "MediaAdd method is failed, check the data";
            return false;
            
        } 
        
        /**
            Drop media method
            @param $id | string
            @return mixed
        */ 
        function mediaDrop ($id = "") {
            $result = "Media delete is not success"; $hlEntityClass = $this->getHlEntityClass();
            
            if ($hlEntityClass && !empty($id)) {
                $rsData = $this->mediaGet(['UF_PICTURE_ID' => $id]);
                
                if (is_array($rsData) && count ($rsData) > 0) {
                    foreach ($rsData as $item) 
                        if ($hlEntityClass::delete($item['ID'])) {
                            
                        }
                        
                    return true;
                    
                } else {
                    $result = "Media with ID " . $id . " not found";
                }
                
                return $result;
            }
        }
        
        /**
            Media update method
            @param $id | string
            @param $fields | array
            @return mixed
        */ 
        function mediaUpdate ($id = "", $fields = "") {
            $hlEntityClass = $this->getHlEntityClass();
            
            if ($hlEntityClass && (!empty($id) || !empty($fields['img-id']))) {
                
                $rsData = $this->mediaGet(['ID' => $id]);
                
                if (empty($rsData) && !empty($fields['img-id'])) {
                    $rsData = $this->mediaGet(['UF_IMG_ID' => $fields['img-id']]);
                }
                
                if (is_array($rsData) && count($rsData) > 0) {
                    foreach ($rsData as $item) {
                        
                        isset($fields["img-id"]) ? $dataFields["UF_IMG_ID"] = $fields["img-id"]: null;
                        isset($fields["popup-left-position"]) ? $dataFields["UF_POPUP_LEFT_POS"] = $fields["popup-left-position"]: null;
                        isset($fields["popup-top-position"]) ? $dataFields["UF_POPUP_TOP_POS"] = $fields["popup-top-position"]: null;
                        isset($fields["popup-text"]) ? $dataFields["UF_POPUP_TEXT"] = $fields["popup-text"]: null;
                        isset($fields["popup-link"]) ? $dataFields["UF_POPUP_LINK"] = $fields["popup-link"]: null;
                        isset($fields["tag-link"]) ? $dataFields["UF_INSTA_LINK"] = $fields["tag-link"]: null;
                        isset($fields["tag"]) ? $dataFields["UF_TAG"] = $fields["tag"]: null;
                        isset($fields["tag-link"]) ? $dataFields["UF_TAG_LINK"] = $fields["tag-link"]: null;
                        isset($fields["likes"]) ? $dataFields["UF_LIKES"] = $fields["likes"] : null;
                        
                        if ($hlEntityClass::update($item['ID'], $dataFields)) {
                            $this->ajaxMethodErrorMsg .= "Element ID - " . $id . " or INSTA ID - " . $fields["img-id"] . " was updated \r\n";
                        }
                    }
                    
                    return true;
                    
                } else {
                    
                    $this->ajaxMethodErrorMsg = "Element with ID - " . $id . " not found, and INSTAGRAM ID - " . $fields['img-id'] . " too!!!";
                    return false;
                    
                }
            }
            
            $this->ajaxMethodErrorMsg = "MediaUpdate method is failed, check the data";
            return false;
        }
        
        /**
            Get media by filter
            @param $filter | array
            @return mixed
        */ 
        function mediaGet ($filter = []) {
            $result = []; $hlEntityClass = $this->getHlEntityClass();
            $rsData = $hlEntityClass::getList([
                'filter' => array_merge([], $filter),
                'select' => ['*'],
                'order' => []
            ]);
            $result = $rsData->fetchAll();
            return $result;
        }
        
        /**
            Get media default sort index
            @return int
        */ 
        function mediaGetDefaultSortIndex () {
            $media = $this->mediaGet(); $index = 0;
            if (is_array($media) && !empty($media)) {
                foreach ($media as $item) {
                    if (intVal($item['UF_PICTURE_SORT']) > $index)
                        $index = intVal($item['UF_PICTURE_SORT']);
                }
            }
            
            return $index + 10;
        }
        
        /**
            Get current request
            @return \Bitrix\Application\Request !!
        */ 
        function getCurrentRequest () {
            return Application::getInstance()->getContext()->getRequest();
        }
        
        /**
            Get settings defaults
            @param $key | string
            @return string
        */ 
        function getSettings ($key = "") {
            return !empty($key) ? Option::get("instadmin", $key) : false;
        }
        
        /**
            Set settings defaults
            @param $key | string
            @param @value | string
            @return boolean
        */ 
        function setSettings ($key = "", $value = "") {
            return !empty($key) ? Option::set("instadmin", $key, $value) : false;
        } 
        
        /**
            Get auth token if auth success
            @return string
        */ 
        function getAuthToken () {
            
            $code = $this->getCurrentRequest()->getQuery('code');
            if (!$_SESSION['instagram_access_token'] && empty($this->getSettings('client_access_token')))
                $this->authToken = $code ? $this->authHandler->getAccessToken($code) : "";
            else if (!empty($this->getSettings('client_access_token'))) 
                $this->authToken = $_SESSION['instagram_access_token'] = $this->getSettings('client_access_token');
            else 
                $this->authToken = $_SESSION['instagram_access_token'];
                
            $this->setAuthToken();
            
            return $this->authToken ? : false;
        }
        
        /**
            Setup auth token if success auth
            @param $token | string
            @return mixed
        */ 
        function setAuthToken () {
            
            $_SESSION['instagram_access_token'] = $this->authToken;
            $this->instaHandler = new Instagram;
            $this->instaHandler->setAccessToken($this->authToken);
            $this->instaHandler->setClientID($this->getSettings('client_id'));
            $this->setSettings('client_access_token', $_SESSION['instagram_access_token']);
            
        }
        
        /**
            Get HL iblocks
            @param $id | string
            @return array
        */ 
        function getHLIblockInfo ($id = "") {

            $hlblock = Highloadblock\HighloadBlockTable::getList()->fetchAll();
            foreach ($hlblock as &$hl) {
                if ($hl['ID'] == $id) $hl['CHECKED'] = true;
            }
            
            return $hlblock;
        }
        
        /**
            Data parse
            @param $data | object
            @return array
        */ 
        function dataParse ($data) {
            
            $array = [];
            if (!empty($data))
                foreach ($data as $mediaData) {
                    $array[] = [
                        'ID' => $mediaData->getId(),
                        'TYPE' => $mediaData->getType() == "image" ? "Картинка" : "<b>Не картинка</b>",
                        'ACTIVE' => $mediaData->getType() == "image" ? 'Y' : 'N',
                        'THUMB' => [
                            'WIDTH' => $mediaData->getThumbnail()->width,
                            'HEIGHT' => $mediaData->getThumbnail()->height,
                            'SRC' => $mediaData->getThumbnail()->url
                        ],
                        "ORIGIN_SRC" => $mediaData->getType() == "image" ? [
                            'WIDTH' => $mediaData->getStandardRes()->width,
                            'HEIGHT' => $mediaData->getStandardRes()->height,
                            'SRC' => $mediaData->getStandardRes()->url
                        ] : [],
                        'CAPTION' => TruncateText($mediaData->getCaption(), 20) ? : "- засекречено -",
                        'CREATED' => date($this->defaultDateFormat, $mediaData->getCreatedTime()) ? : "- засекречено -",
                        'USER' => $mediaData->getUser()->getUserName() ? : "- засекречено -",
                        'FILTER' => $mediaData->getFilter() ? : "- засекречено -",
                        'DISPLAY_TAGS' => TruncateText(implode(' ', array_map(function($item) {
                            return '#' . $item->getName();
                        }, $mediaData->getTags()->getData())), 20),
                        'TAGS' => array_map(function($item) {
                            return '#' . $item->getName();
                        }, $mediaData->getTags()->getData()),
                        'LINK' => $mediaData->getLink(),
                        'LIKES' => $mediaData->getLikesCount() ? : "- засекречено -",
                        'SELECTED' => "N",
                    ];
                } 

            return $array;
                
        }
        
        /**
            Get and update likes count on current image ID
            @return 
        */ 
        function updateLikesForWidget () {
            
            $media = $this->mediaGet(); $count = 0;
            if (!empty($media) && is_array($media)) {
                foreach ($media as $img) {
                    if (!empty($img['UF_IMG_ID'])) {
                        $instaImg = $this->getMediaByID($img['UF_IMG_ID']);
                        $likesCount = $instaImg->getLikesCount();
                        $this->mediaUpdate(null, ['img-id' => $img['UF_IMG_ID'], 'likes' => $likesCount]);
                        $count++;
                    } 
                }
            }
            
            return $count;
            
        }
    
        /**
            Get follovers count and update on current account
            @return 
        */ 
        function updateFolloversCountInAccount () {
            
            $userInfo = $this->getCurrentUserInfo();
            $folloversCount = $userInfo->getFollowersCount();
            if ($folloversCount > 0)
                $this->setSettings('users_count', $folloversCount);
        }
        
        /**
            Prepare array to JSON
            @param $array | array
            @return string | JSON
        */ 
        function prepare2json ($array = []) {
            return is_array($array) ? htmlspecialchars(json_encode($array), ENT_QUOTES, "UTF-8") : "";
        }
        
        /**
            Get user info by ID
            @param $id | string 
            @return 
        */ 
        function getUserInfo ($id = "") {
            return $this->instaHandler->getUser($id);
        }
        
        /**
            Get user media by media ID
            @param @id | string
            @return mixed
        */ 
        function getMediaByID ($id = "") {
            return $this->instaHandler->getMedia($id);
        }
        
        /**
            Get tag 
            @param $tag | string 
            @return 
        */ 
        function getTag ($tag = "") {
            return $this->instaHandler->getTag($tag);
        }
        
        /**
            Get location by ID
            @param $id | string
            @return mixed
        */ 
        function getLocationByID ($id = "") {
            return $this->instaHandler->getLocation($id);
        }
        
        /**
            Get current auth user info 
            @return 
        */ 
        function getCurrentUserInfo () {
            return $this->instaHandler->getCurrentUser();
        }
        
    }
    
?>