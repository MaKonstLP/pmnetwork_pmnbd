<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;
use common\components\GorkoLeadApi;

class FormController extends Controller
{
    public function beforeAction($action){
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSend()
    {
        $messages = [
            'successTitle' => 'Заявка успешно отправлена',
            'errorTitle' => 'К сожалению, не удалось обработать заявку',
            'successBody' => 'Спасибо за проявленный интерес. Мы свяжемся с вами в течение рабочего дня, чтобы обсудить детали.',
            'errorBody' => 'Попробуйте, пожалуйста, позднее или свяжитесь с нами по телефону.',
        ];
        $post_data = json_decode(json_encode($_POST), true);

        $payload = [];
        $payload['city_id'] = Yii::$app->params['subdomen_id'];
        $payload['event_type'] = $post_data['event_type'];

        if ($post_data['event_type'] == 'Corporate_NY')
            unset($post_data['date']);

        if(!$post_data['check'])
            return false;

        foreach ($post_data as $key => $value) {
//            if(empty($value))
//                continue;

            switch ($key) {
                case 'date':
                    if($value)    
                        $payload['date'] = $newDate = date("Y.m.d", strtotime($value));
                    break;
                case 'name':
                    $payload['name'] = $value;
                    break;
                case 'email':
                    $payload['email'] = $value;    
                    break;
                case 'guests':
                    $payload['guests'] = $value;
                    break;
                case 'phone':
                    $payload['phone'] = $value;
                    break;
                case 'venue_id':
                    $payload['venue_id'] = $value;
                    break;
                case 'comment':
                    $payload['details'] = $value;
                    break;
                case 'max_budget':
                    $payload['max_budget'] = $value;
                    break;
                case 'url':
                    isset($payload['details']) ? $payload['details'] .= ' Заявка отправлена с '.$value : $payload['details'] = 'Заявка отправлена с '.$value;
                    break;
                default:
                    break;
            }
        }

        $messageApi = GorkoLeadApi::send_lead('v.gorko.ru', 'birthday-place', $payload);

        if ($messageApi) {
            $resp = [
                'error' => 0,
                'title' => $messages['successTitle'],
                'body' => $messages['successBody'],
                'name' => isset($_POST['name']) ? $_POST['name'] : '',
                'phone' => $_POST['phone'],
                'guests' => isset($_POST['guests']) ? $_POST['guests'] : '',
                'messageApi' => $messageApi
            ];
        } else {
            $resp = [
                'error' => 1,
                'title' => $messages['errorTitle'],
                'body' => $messages['errorBody'],
            ];
        }


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $resp;
    }

    public function actionFormAdvertising() {
        $from = Yii::$app->params['senderEmail'];
        $to = ['birthday-place@yandex.ru', 'ab@liderpoiska.ru'];
        $sub = 'Заявка со страницы рекламы на birthday-place.ru';

        $msg = '';
        $resp = [];
        $resp['name'] = $_POST['name'];
        $resp['phone'] = $_POST['phone'];

        if(isset($_POST['name']))
            $msg.='Имя: '.$_POST['name'].'<br>';
        if(isset($_POST['position']))
            $msg.='Должность: '.$_POST['position'].'<br>';
        if(isset($_POST['phone']))
            $msg.='Номер: '.$_POST['phone'].'<br>';
        if(isset($_POST['email']))
            $msg.='Email: '.$_POST['email'].'<br>';
        if(isset($_POST['name_room']))
            $msg.='Название площадки: '.$_POST['name_room'].'<br>';
        if(isset($_POST['city']))
            $msg.='Город: '.$_POST['city'].'<br>';

        $message  = Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($sub)
            ->setCharset('utf-8')
            ->setHtmlBody($msg);

        $message->send();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }
}
