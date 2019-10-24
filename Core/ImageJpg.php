<?php


class ImageJpg
{
    var $image;
    var $image_type;

    function load($filename) {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            imagepng($this->image,$filename);
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }

    function output($image_type=IMAGETYPE_JPEG) {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            imagepng($this->image);
        }
    }

    function getWidth() {
        return imagesx($this->image);
    }

    function getHeight() {
        return imagesy($this->image);
    }

    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }

    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }

    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    /**
     * создает квдратную миниатюру из оригинальной фотографии
     * если фотка не квадратная, то добавляются поля, чтобы минматюра поучилась квадратной
     *
     * @param $miniSize - размер квадратной миниатюры
     */
    function resizeMini($miniSize) {
        $new_image = imagecreatetruecolor($miniSize, $miniSize);

        $white = imagecolorallocate ( $new_image , 255, 255, 255);  //создаем цвет
        imagefill ( $new_image , 0 , 0 , $white );                              // заливаем фон миниатюры белым

        $curWidth = $this->getWidth();
        $curHeight = $this->getHeight();
        $fCurAspectRatio = (float)($curHeight / $curWidth);     // >1 для вертикальной фото, <1 для горизонтальной, 1для квадратной


        if ($curWidth == $curHeight) {
            //оригинальное фото квадратное
            //
            imagecopyresampled(
                $new_image,
                $this->image,
                0,   //х-координата начала оригинального изобраения В МИНИАТЮРЕ. отличается от 0 потомучто есть поля
                0,
                0,
                0,
                $miniSize,    //ширина оригинального изображения после сжатия  равна размеру миниатюры
                $miniSize,    //высота оригинального изображения после сжатия  равна размеру миниатюры
                $curWidth,    //берем для копироания весь оригинал
                $curHeight    //берем для копироания весь оригинал
            );


        }
        elseif ($fCurAspectRatio > 1) {
            //вертикальняа фотка
            //по бокам будут поля

            //ширина оригинального изображения после сжатия до миниатюры (это НЕ ширина миниатюры, это еще меньше)
            $newWidth = (int)($miniSize / $fCurAspectRatio);

            imagecopyresampled(
                $new_image,
                $this->image,
                (int)(($miniSize - $newWidth)/2),   //х-координата начала оригинального изобраения В МИНИАТЮРЕ. отличается от 0 потомучто есть поля
                0,
                0,
                0,
                $newWidth,    //dest_width
                $miniSize,    //dest_heigh во всю высоту миниатюры
                $curWidth,
                $curHeight
            );
        }
        else {
            //оригиналная фотка - горизонатльная


            //высота оригинального изображения после сжатия до миниатюры (это НЕ высота миниатюры, это еще меньше)
            $newHeigh = (int)($miniSize / $fCurAspectRatio);


            imagecopyresampled(
                $new_image,
                $this->image,
                0,
                ($miniSize - $newHeigh)/2,   //y-координата начала оригинального изобраения В МИНИАТЮРЕ. отличается от 0 потомучто есть поля сверху снизу,
                0,
                0,
                $miniSize,    //dest_width - во всю ширину миниатбры
                $newHeigh,    //dest_height
                $curWidth,
                $curHeight
            );
        }

        $this->image = $new_image;
    }
}
