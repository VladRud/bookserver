<?php

namespace api\modules\core\helpers;

/**
 * Class FileUploaderHelper
 *
 * @author Stableflow
 */
class FileUploaderHelper {

    /**
     * Save image file.
     * 
     * @param \yii\web\UploadedFile $instance
     * @param string $dirname
     * @param array $crop 
     * @return mixed 
     */
    public static function saveImage($instance, $dirname, $crop = null) {
        if ($instance instanceof \yii\web\UploadedFile) {
            $basePath = \Yii::getAlias("@webroot");
            $filename = md5($instance->baseName . time());
            $path = DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . substr($filename, 0, 2) . DIRECTORY_SEPARATOR . substr($filename, 2, 2);

            if (!is_dir($basePath . $path)) {
                $oldmask = umask(0);
                mkdir($basePath . $path, 0777, true);
                umask($oldmask);
            }

            if ($instance->saveAs($basePath . $path . DIRECTORY_SEPARATOR . $filename . '.' . $instance->extension)) {
                return $path . DIRECTORY_SEPARATOR . $filename . '.' . $instance->extension;
            }
        }

        return false;
    }
    

}
