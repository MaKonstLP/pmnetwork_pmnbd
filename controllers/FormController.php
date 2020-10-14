<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;

class FormController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionSend()
    {
        $to  = ['kornilov@liderpoiska.ru', 'birthday-place@yandex.ru', 'sites@plusmedia.ru'];
        $messages = [
            'successTitle' => 'Заявка успешно отправлена',
            'errorTitle' => 'К сожалению, не удалось обработать заявку',
            'successBody' => 'Спасибо за проявленный интерес. Мы свяжемся с вами в течение рабочего дня, чтобы обсудить детали.',
            'errorBody' => 'Попробуйте, пожалуйста, позднее или свяжитесь с нами по телефону.',
        ];

        if ($_POST['type'] == 'main' || $_POST['type'] == 'header') {
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

    public function sendApi($name, $phone, $date, $count)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/venues/all/request?model_type=restaurants&model_id=1&city_id=4400');
        $payload = json_encode([
            "name"      => $name,
            "phone"     => $phone,
            "date"      => $date,
            "guests"    => $count,
            'budget' => null,
            'details' => null,
            'drinks' => null,
            'event_type' => "1",
            'food' => null,
            'line' => null,
            'page_type' => null,
            'telegram' => null,
            'viaLine' => null,
            'viaPhone' => 1,
            'viaTelegram' => null,
            'viaViber' => null,
            'viaWhatsApp' => null,
            'viber' => null,
            'whatsapp' => null,
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $response;
    }
}
