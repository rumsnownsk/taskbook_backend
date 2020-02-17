<?php
namespace api\libs;

trait HelpersTrait {

    public static function getErrors(){

        $errors = [];
        foreach (self::$errors as $error) {
            foreach ($error as $item) {
                $errors[] .= $item;
            }

        }
        return [
            'errors' => $errors
        ];
    }

    public function randomString($length = 9) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}