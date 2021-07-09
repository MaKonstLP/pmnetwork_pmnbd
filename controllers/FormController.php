<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;

class FormController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionSend()
    {
        //$to  = ['kornilov@liderpoiska.ru', 'birthday-place@yandex.ru', 'sites@plusmedia.ru'];
        $messages = [
            'successTitle' => 'Заявка успешно отправлена',
            'errorTitle' => 'К сожалению, не удалось обработать заявку',
            'successBody' => 'Спасибо за проявленный интерес. Мы свяжемся с вами в течение рабочего дня, чтобы обсудить детали.',
            'errorBody' => 'Попробуйте, пожалуйста, позднее или свяжитесь с нами по телефону.',
        ];

        /*if ($_POST['type'] == 'main' || $_POST['type'] == 'header') {
            $subj = "Заявка на подбор зала.";
        } else {
            $subj = "Заявка на бронирование зала.";
        }

        $post_string_array = [
            'name'  => 'Имя',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'comment' => 'Комментарий',
            'date'  => 'Дата',
            'url'   => 'Страница отправки'
        ];
        
        $msg  = "";
        foreach ($post_string_array as $key => $value) {
            if (!empty($_POST[$key])) {
                $msg .= $value . ': ' . $_POST[$key] . '<br/>';
            }
        }

        $message = $this->sendMail($to, $subj, $msg);
        if ($message) {
            $resp = [
                'error' => 0,
                'title' => $messages['successTitle'],
                'body' => $messages['successBody'],
                'name' => isset($_POST['name']) ? $_POST['name'] : '',
                'phone' => $_POST['phone'],
            ];
        } else {
            $resp = [
                'error' => 1,
                'title' => $messages['errorTitle'],
                'body' => $messages['errorBody'],
            ];
        }*/

        $messageApi = $this->sendApi($_POST);

        $log = file_get_contents('/var/www/pmnetwork/log/manual.log');
        $log .= json_encode($messageApi);
        file_put_contents('/var/www/pmnetwork/log/manual.log', $log);

        if ($messageApi) {
            $resp = [
                'error' => 0,
                'title' => $messages['successTitle'],
                'body' => $messages['successBody'],
                'name' => isset($_POST['name']) ? $_POST['name'] : '',
                'phone' => $_POST['phone'],
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

    public function sendMail($to, $subj, $msg)
    {
        $message = Yii::$app->mailer->compose()
            ->setFrom(['post@smilerooms.ru' => 'День Рождения'])
            ->setTo($to)
            ->setSubject($subj)
            ->setCharset('utf-8')
            //->setTextBody('Plain text content')
            ->setHtmlBody($msg . '.');
        if (count($_FILES) > 0) {
            foreach ($_FILES['files']['tmp_name'] as $k => $v) {
                $message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
            }
        }
        return $message->send();
    }

    public function sendApi($post) {
        $post_data = json_decode(json_encode($_POST), true);

        //$log = file_get_contents('/var/www/pmnetwork/pmnetwork/log/manual.log');
        //$log .= json_encode($post_data);
        //file_put_contents('/var/www/pmnetwork/pmnetwork/log/manual.log', $log);

        $payload = [];

        $payload['city_id'] = Yii::$app->params['subdomen_id'];
        $payload['event_type'] = "Birthday";

        foreach ($post_data as $key => $value) {
            switch ($key) {
                case 'date':
                    if($value)    
                        $payload['date'] = $newDate = date("Y.m.d", strtotime($value));
                    break;
                case 'name':
                    $payload['name'] = $value;
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
                default:
                    break;
            }
        }

        //return $payload;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://v.gorko.ru/api/birthday-place/inquiry/put');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);



        return json_encode([
            'response' => $response,
            'info' => $info,
            'payload' => $payload,
        ]);
    }
}
