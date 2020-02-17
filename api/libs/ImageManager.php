<?php

namespace api\libs;

use Intervention\Image\ImageManagerStatic as Image;

class ImageManager
{
    use HelpersTrait;

    public static $rules = [
        'required' => 'file'
    ];

    public static $errors = array();

    public $currentImage;
    public $folderImages;

    public function __construct($pathForSave)
    {
        Image::configure(array('driver' => 'imagick'));

        $this->folderImages = $pathForSave;

//        $this->checkExistPath($this->folderImages);
    }

    public function validate($file)
    {
        $errorCode = $file['error'];

        if ($errorCode !== UPLOAD_ERR_OK) {

            // Массив с названиями ошибок
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
                UPLOAD_ERR_FORM_SIZE => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
                UPLOAD_ERR_PARTIAL => 'Загружаемый файл был получен только частично.',
                UPLOAD_ERR_NO_FILE => 'Файл не был загружен.',
                UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
                UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
                UPLOAD_ERR_EXTENSION => 'PHP-расширение остановило загрузку файла.',
            ];
            // Зададим неизвестную ошибку
            $unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';

            // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
            $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;

            // Выведем название ошибки

            self::$errors['error'][] = $outputMessage;

            return false;
        }

        // проверка mime-типа файла
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = (string)finfo_file($finfo, $file['tmp_name']);

        $allowedMimeTypes = [
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];

        if (in_array($mime, $allowedMimeTypes) == false) {
            self::$errors['error'][] = 'Выбранный файл должен быть картинкой jpeg/jpg/png';
            return false;
        }

        return true;
    }

    public function uploadTo($image)
    {
        if (!$this->validate($image)) {
            return false;
        };

        if ($this->currentImage){
            $this->delete();
        }

        $filename = $this->randomString() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);

        Image::make($image['tmp_name'])->save($this->folderImages . '/' . $filename);

        return $filename;
    }

    public function delete($path = null)
    {
        if ($path && file_exists($path)){
            unlink($path);
        }

        if (
            file_exists($this->folderImages . "/" . $this->currentImage)
            &&
            $this->currentImage !== null
        ) {
            unlink($this->folderImages . "/" . $this->currentImage);
        }
    }

//    public function getErrors(){
//
//        $errors = [];
//        foreach ($this->errors as $error) {
//            foreach ($error as $item) {
//                $errors[] .= $item;
//            }
//
//        }
//        return $errors;
//    }

}