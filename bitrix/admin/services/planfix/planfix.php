<?
    require "plan_client.php";
    
    class PlanFixTaskList {
        
        public $apiUrl = 'https://apiru.planfix.ru/xml/';
        
        public $apiKey = 'd12f296e258d057bd6e39ec7eb3f8069';
        
        public $apiSecret = 'f0185fd99365c1a3f734480af1f7af0c';
        
        public $login = 'apiuser';
        
        public $password = '7bn8vrt';
        
        public $account = 'dsklad';
        
        public $pageCurrent = 1;
        
        public $pageSize = 100;

        public $autoIndexPriority = 10;
        
        public $priorityCoef = 1;
        
        function __construct () {

            session_start();
            $this->PF = new Planfix_API(['apiUrl' => $this->apiUrl, 'apiKey' => $this->apiKey, 'apiSecret' => $this->apiSecret]);
            $this->PF->setAccount($this->account);
            
            if (!$_SESSION['planfixSid']) {
                
                $this->PF->setUser(['login' => $this->login, 'password' => $this->password]);
                $this->PF->authenticate();
                $_SESSION['planfixSid'] = $this->PF->getSid();
                
            } else {
                
                $this->PF->setSid($_SESSION['planfixSid']);
                
            }

            $this->paramsDefault = [
                'pageCurrent' => $this->pageCurrent,
                'pageSize' => $this->pageSize,
            ];
            
        }
        
        function GetUsers () {
            
             return $this->PF->api('user.getList', $this->paramsDefault)['data']['users'];
            
        }

        function GetContacts () {

             return $this->PF->api('contact.getList', $this->paramsDefault)['data']['contacts'];

        }

        function GetProjects () {
            
            return $this->PF->api('project.getList', $this->paramsDefault)['data']['projects'];
            
        }
        
        function GetTaskList ($params = []) {
            return $this->PF->api('task.getList', array_merge($this->paramsDefault, $params))['data']['tasks'];
        }
        
        function GetTaskStatusList () {
            
            return $this->PF->api('taskStatus.getSetList', $this->paramsDefault)['data']['taskStatusSets'];
            
        }
        
        function GetUserGroupsList () {
            
            return $this->PF->api('userGroup.getList', $this->paramsDefault)['data']['userGroup'];
            
        }
        
        function GetTaskStatusListByProces ($params = []) {
            
            return $this->PF->api('taskStatus.getListOfSet', array_merge($params, $this->paramsDefault))['data']['taskStatuses'];
            
        }

        function UpdateTask($task = []) {
            
            return $this->PF->api('task.update', array_merge($this->paramsDefault, $task));
            
        }

        function SetTaskAllPriority($taskFilter, $taskList = array()) {
            
            if(empty($taskList)) {
                
                $taskList = $this->GetTaskList($taskFilter);
                
                if (count($taskList['task']) == $this->pageSize) {
                    
                    $this->SetTaskAllPriority($taskFilter, $taskList);
                    
                } else {
                    
                    return $taskList;
                    
                }
                
            } else {
                
                $this->paramsDefault['pageCurrent'] = $this->paramsDefault['pageCurrent']+1;
                $newTaskList = $this->GetTaskList($taskFilter);
                $taskList['task'] = array_merge($taskList['task'], $newTaskList['task']);
                
                if(count($taskList['task']) == $this->paramsDefault['pageSize'] * $this->paramsDefault['pageCurrent']) {
                    
                    $this->SetTaskAllPriority($taskFilter, $taskList);
                    
                } else {
                    
                    foreach ($taskList['task'] as $keyTask => $task) {
                        
                        foreach ($task['customData']['customValue'] as $key => $value) {
                            
                            if ($value['field']['id'] == 113884) {
                                
                                if (strtolower($value['value']) == 'обычный') {
                                    $this->autoIndexPriority *= 1.2;
                                } elseif(strtolower($value['value']) == 'низкий') {
                                    $this->autoIndexPriority *= 1;
                                } elseif (strtolower($value['value']) == 'высокий') {
                                    $this->autoIndexPriority *= 1.5;
                                }
                            }
                            
                            if ($value['field']['id'] == 113896) {
                                
                                $arData = [
                                    "task" => [
                                        "id" => $task["id"],
                                        "customData" => [
                                            "customValue" => [
                                                "id" => $value["field"]["id"],
                                                "value" => floatVal($value["value"]) + $this->autoIndexPriority,
                                            ]
                                        ]
                                    ]
                                ];
                                
                                $this->UpdateTask($arData);
                            }
                        }
                        
                        $this->autoIndexPriority = 10;
                    }
                    
                    return 'ok';
                    
                }
            }
        }
        
    }
    
?>
