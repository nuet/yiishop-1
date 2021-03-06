<?php
/**
 * User: 张世路
 * Date: 2016/10/5
 * Time: 22:22
 */

namespace app\admin\controllers;

use app\models\Category;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;

class CategoryController extends BaseController{


    public $layout = 'admin_layout';

    public function actionList()
    {

        $model = new Category();
        $list = $model->getOption();

        return $this->render('list',compact('list'));

    }


    public function actionAdd()
    {
        $model = new Category();
        $list = $model ->getOption();

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->add($post)){
                Yii::$app->session->setFlash('info','添加成功!');
                $this->redirect(['category/add']);
            }else{
                Yii::$app->session->setFlash('info','添加失败!');
            }
        }

       return $this->render('add',compact('model','list'));
    }


    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');

        $model = Category::findOne($id);
        $list = $model->getOption();

        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->save()){
                Yii::$app->session->setFlash('info','修改成功!');
            }
        }
        return $this->render('add',compact('model','list'));
    }


    public function actionDel()
    {
        try{
            $id = Yii::$app->request->get('id');
            if(empty($id)){
                throw new Exception('id不存在!');
            }
            //有子分类的分类不允许删除
            $r = Category::find()->where(['pid'=>$id])->one();
            if($r){
                throw new Exception('当前分类还有子分类,不允许删除!');
            }
            if(!Category::deleteAll(['id'=>$id])){
                throw new Exception('删除失败!');
            }


        }catch (Exception $e){
           Yii::$app->session->setFlash('info',$e->getMessage());
            return $this->redirect(['category/list']);
        }
        Yii::$app->session->setFlash('info','删除成功!');
        return $this->redirect(['category/list']);
    }



}