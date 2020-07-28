<?
    namespace AppController;

    class ExceptionController extends \Exception {

        public $className;

        public $exludedMethods;

        function __construct (string $className = "", array $excludedMethods = array()) {
            if (empty($className))
                $this->cExceptionClassNameIsNotSet();
            else
                $this->className = $className;

            $this->exludedMethods = array_merge(array(
                '__construct', '__destruct', 'indexAction'
            ), $excludedMethods);
        }

        public function cExceptionClassNameIsNotSet () {
            // throw new \Exception ('Class name is not set!!! Set this into class params!!!');
            \botTools::throwError(1506, 'Class name is not set!!! Set this into class params!!!');die();
        }

        public function cExceptionGetAccessedMethods () {
            return array_diff(get_class_methods($this->className), $this->exludedMethods);
        }

        public function cExceptionMethodNotFound (string $string = "") {
            \botTools::throwError(1508, 'The method ' . $string . ' is not found!!! Pleace read user doc!!!');die();
            // throw new \Exception ('The method ' . $string . ' is not found!!! Pleace read user doc!!!');
        }

    }
?>
