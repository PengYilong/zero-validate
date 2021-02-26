<?php
namespace Zero;

class Validate
{

    /**
     * @var array
     */
    protected $field = [];

    /**
     * @var array
     */
    protected $rule = []; 

    /**
     * @var array
     */
    protected $message = [];

    /**
     * @var array
     */
    protected $sampleMessage = [];
    
    /**
     * whether batch check 
     * @var bool
     */
    private $batch;

    /**
     * @var string|array
     */
    protected $error;

    protected $language;

    /**
     * @var array
     */
    protected $regex = [

    ];

    protected $scene = [];

    /**
     * append the rules of the check data
     * @var array
     */
    protected $append = [];

    /**
     * remove the rules of the check data 
     * @var array
     */
    protected $remove = [];

    /**
     * limit the rules of the check data
     * @var array
     */
    protected $only = [];

    /**
     * 
     * @var string
     */
    protected $currentScene;

    /**
     * filter_var
     */
    protected $filter = [
        'email' => FILTER_VALIDATE_EMAIL,
        'integer' => FILTER_VALIDATE_INT,
    ];

    public function __construct($language = 'zh-cn')
    {
        $this->language = $language;
        $file =  __DIR__.'/language/'.$language.'.php';
        if( file_exists($file) ){
            $this->sampleMessage = $file;
        }
    }

    /**
     * @param  array 
     * @return bool 
     */
    public function check($data, $rules = [], $scene = '')
    {
        if( empty($rules) ){
            $rules = $this->rule;
        }

        $this->getScene($scene);

        foreach($rules as $key => $rule){
            if( strpos($key, '|') ){
                list($key, $title) = explode('|', $key);
            } else {
                $title = $this->field[$key] ?? $key;
            }

            if( !empty($this->only) && !in_array($key, $this->only) ){
                continue;
            }

            $value = $data[$key];
            $result = $this->checkItem($key, $value, $rule, $title);
            if( true !== $result  ){
                if( $this->batch ){ //batch
                    $this->error[$key] = $result;
                } else { //single
                    $this->error = $result;
                    break;
                }
            }
        }
        return empty($this->error);
    }

    public function checkItem($field, $value, $rules, $title)
    {
        // $this->rule = ['field'=>'require|number'] or ['field'=>['require', 'number']] 
        if( is_string($rules) ){
            $rules = explode('|', $rules);
        }
        foreach($rules as $val){
            $result = $this->is($value, $val);
            if( false === $result ){
                return $this->getRuleMsg($field, $title, $val);
            }
        }
        return true; 
    }

    public function is($value, $rule)
    {
        switch(lcfirst($rule)) {
            case 'require':
                $result = !empty($value) || '0' == $value;
                break;
            case 'number':
                $result = is_numeric($value);
                break;
            default:
                if( isset($this->filter[$rule]) ){
                    $result = filter_var($value, $this->filter[$rule]);
                } else {
                    $result = true; 
                }
        }
        return $result;
    }

    public function batch($batch = true)
    {
        $this->batch = $batch;
        return $this;
    }

    protected function getValidateType($value){
        $type = 'is';
        return [0];
    }

    public function getRuleMsg($field, $title, $rule)
    {
        if( isset($this->message[$field.'.'.$rule]) ){
            $msg = $this->message[$field.'.'.$rule];
        } elseif( isset( $this->message[$field][$rule] ) ){
            $msg = $this->message[$field][$rule];
        } elseif( isset( $this->message[$field] ) ){
            $msg = $this->message[$field]; 
        } elseif( isset($this->sampleMessage[$field]) ){
            $msg = $this->sampleMessage[$filed]; 
        } else {
            $msg = $title . '规则不符'; 
        }
        if( is_string($msg) && is_scalar($msg) && false !== strpos(':', $msg) ){
            $msg = str_replace(':attribute', $title, $this->sampleMessage[$rule]);
        }
        return $msg;
    }

    /**
     * sets the $scene
     */
    public function scene($scene)
    {
        $currentScene = $scene;
        return $this;
    }

    public function getScene($scene)
    {
        $scene = !empty($scene) ?: $this->currentScene;
        if( empty($scene) ){
            return ;
        }
        $scene = $this->scene[$scene];
        if( is_string($arr) ){
            $arr = explode(',', $scene);
        }
        $this->only = $arr;
    }

    /**
     * remove the rules of the check data
     */
    public function append()
    {

    }

    /**
     * remove the rules of the check data 
     */
    public function remove()
    {

    }

    public function getError()
    {
        return $this->error;
    }

    public function __call($method, $args)
    {
        if( 'is' == strtolower(substr($method, 0, 2)) ){
            $method = substr($method, 2);
        } 
        array_push($args, lcfirst($method));   
        return call_user_func_array([$this, 'is'], $args);
    }

}
