<?php

namespace app\controllers;

use app\models\Auth;
use app\models\User;
use Yii;
use yii\authclient\AuthAction;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','login','sign'],
                'rules' => [
                    [
                        'actions' =>['login', 'sign'],
                        'allow' =>true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if(Yii::$app->user->isGuest){
            if($auth){
                $user = $auth->user;
                Yii::$app->user->login($user);
            }else{
                if(isset($attributes['email']) && User::find()->where(['email'=>$attributes['email']])->exists()){
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $client->getTitle()]),
                    ]);
                }
            }
        }
    }

    public function actionSign()
    {
        $a = Yii::$app->request->userHost;
        var_dump($a);die;
        $encryptedData = Yii::$app->getSecurity()->encryptByPassword(123, '123456');
        echo $encryptedData;
        $data = Yii::$app->getSecurity()->decryptByPassword($encryptedData, '123456');
        var_dump($data);
//        $hash = Yii::$app->getSecurity()->generatePasswordHash('123456');
//        echo $hash;
    }

    public function actionLogin()
    {
        $encryptedData = '��E2Oh�y��"�Ϲ�_5bfffe366fe794e9adf2efacd33bee4511f48b41168d25c7682839941f9ff5de�|H�"Q�����u.��8Sd�A���,~[�!';
        $data = Yii::$app->getSecurity()->decryptByPassword($encryptedData, '123456');
        var_dump($data);die;
        if(Yii::$app->getSecurity()->validatePassword('123456', '$2y$13$0TpoAgxfoBbzy.M.oBzVGePNn2HHF1ONQsRigSoarQfiZeOl29GF.')){
            echo 'ok';
        }else{
            echo 'err';
        }
    }

    public function actionTest()
    {
        echo 111;die;
        $identity = Yii::$app->user->identity;
        $id = Yii::$app->user->id;
        $isGuest = Yii::$app->user->isGuest;
        var_dump($identity, $id, $isGuest);
        die;
        $identity = User::findOne(['username' => 'weixi']);
        Yii::$app->user->login($identity);
        var_dump(Yii::$app->user->isGuest);
        die;
        echo 'Test';
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
//    public function actionLogin()
//    {
//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        }
//
//        $model->password = '';
//        return $this->render('login', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
