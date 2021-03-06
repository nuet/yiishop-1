<?php
/**
 * Created by PhpStorm.
 * User: MBENBEN
 * Date: 2016/8/21
 * Time: 13:07
 */

//用户模型类
namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord{

    public $repass;//确认密码;
    public $rememberMe = false;//默认不"记住我";

    public static function tableName()
    {
        //指定该模型映射的表名{{%}}写法表示自动调用/config/db.php里设置的表前缀;
        return "{{%user}}";
    }

    /* label字段名(显示于表单{label}) */
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'useremail'=>'电子邮箱',
            'userpass'=>'用户密码',
            'repass'=>'确认密码',
        ];
    }

    /* 验证规则(显示于表单{error}) */
    public function rules()
    {
        return [
            ['username','required','message'=>'用户名不能为空!','on'=>['addUser']],
            ['username','unique','message'=>'用户名已存在!','on'=>['addUser']],
            ['userpass','required','message'=>'密码不能为空!','on'=>['addUser','userLogin']],
            ['repass','required','message'=>'确认密码不能为空!','on'=>['addUser']],
            ['rememberMe','boolean','on'=>'userLogin'],
            ['userpass','validatePass','on'=>['userLogin']],
            ['useremail','required','message'=>'邮箱不能为空!','on'=>['addUser','userRegister','userLogin']],
            ['useremail','email','message'=>'邮箱格式不正确!','on'=>['addUser','userRegister','userLogin']],
            ['useremail','unique','message'=>'邮箱已存在!','on'=>['addUser','userRegister']],
            ['repass','compare','compareAttribute'=>'userpass','message'=>'2次密码不一致!','on'=>'addUser'],

        ];
    }

    /* 自定义验证密码 */
    public function validatePass(){

        //如果前面没有错误
        if(!$this->hasErrors()){
            $data = self::find()->where(['useremail'=>$this->useremail,'userpass'=>md5($this->userpass)])->one();

            if(is_null($data)){
                $this->addError('admin_pass','邮箱或密码错误!');
            }
        }
    }


    /* 添加会员 */
    public function addUser($data){

        $this->scenario = 'addUser';

        if($this->load($data) && $this->validate()){

            $this->createtime = time();
            $this->userpass = md5($this->userpass);

            if($this->save(false)){

                //同时向用户详细表中也插入条关联数据
                $last_user_id = \Yii::$app->db->getLastInsertID();
                $profile = new Profile();
                $profile->user_id = $last_user_id;
                $profile->save(false);

                return true;
            }
            return false;
        }

        return false;
    }

    /* 前台用户注册 */
    public function userRegister($data){

        $this->scenario = 'userRegister';

        //生成账号密码
        $data['User']['username'] = 'a'.uniqid();
        $data['User']['userpass'] = uniqid();
        $data['User']['repass'] = $data['User']['userpass'];

        if($this->load($data) && $this->validate()){

            //发送邮件
            $mail = \Yii::$app->mailer->compose('layouts/userRegister',['username'=>$data['User']['useremail'],'userpass'=>$data['User']['userpass']]);
            $mail ->setFrom(\Yii::$app->params['defaultValue']['admin_email']);
            $mail ->setTo($this->useremail);
            $mail ->setSubject('木瓜商城-账号注册');
            if($mail->send() && $this->addUser($data)){
                return true;
            }
        }
        return false;
    }

    /* 前台用户登入 */
    public function userLogin($data){

        $this->scenario = 'userLogin';

        if($this->load($data) && $this->validate()){

            $lifttime = $this->rememberMe ? 24*3600*7:0;//记住密码为7天
            session_set_cookie_params($lifttime);
            \Yii::$app->session['username']=$this->useremail;
            \Yii::$app->session['isLogin']=1;
            return true;
        }
    }


    /* 获取用户关联表数据(用于关联查询,user_id 是 profile表中的字段) */
    public function getProfile(){

        return $this->hasOne(Profile::className(),['user_id'=>'id']);

    }

}