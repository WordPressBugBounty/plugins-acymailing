<?php

namespace AcyMailing\Helpers;

use AcyMailing\Core\AcymObject;

class ImageHelper extends AcymObject
{
    public string $error = '';
    public int $maxHeight;
    public int $maxWidth;
    public string $destination;

    public function removePictures(string $text): string
    {
        $return = preg_replace('#< *img((?!content_main_image)[^>])*>#Ui', '', $text);

        return preg_replace('#< *div[^>]*class="jce_caption"[^>]*>[^<]*(< *div[^>]*>[^<]*<\/div>)*[^<]*<\/div>#Ui', '', $return);
    }

    public function available(): bool
    {
        if (!function_exists('gd_info')) {
            $this->error = 'The GD library is not installed.';

            return false;
        }
        if (!function_exists('getimagesize')) {
            $this->error = 'Cound not find getimagesize function';

            return false;
        }
        if (!function_exists('imagealphablending')) {
            $this->error = "Please make sure you're using GD 2.0.1 or later version";

            return false;
        }

        return true;
    }

    public function resizePictures(string $input): string
    {
        $this->destination = ACYM_MEDIA.'resized'.DS;
        acym_createDir($this->destination);
        $content = acym_absoluteURL($input);

        preg_match_all('#<img([^>]*)>#Ui', $content, $results);
        if (empty($results[1])) {
            return $input;
        }

        $replace = [];

        foreach ($results[1] as $onepicture) {
            if (strpos($onepicture, 'donotresize') !== false) {
                continue;
            }

            if (!preg_match('#src="([^"]*)"#Ui', $onepicture, $path)) {
                continue;
            }
            $imageUrl = $path[1];

            $imageUrl = preg_replace('/#joomlaImage.*/', '', $imageUrl);

            $imageUrl = acym_internalUrlToPath($imageUrl);

            $newPicture = $this->generateThumbnail($imageUrl);

            $newDimension = 'max-width:'.$this->maxWidth.'px;max-height:'.$this->maxHeight.'px;';

            if (empty($newPicture)) {
                if (strpos($onepicture, 'style="') !== false) {
                    $replace[$onepicture] = preg_replace('#style="([^"]*)"#Uis', 'style="'.$newDimension.'$1"', $onepicture);
                } else {
                    $replace[$onepicture] = ' style="'.$newDimension.'" '.$onepicture;
                }
                continue;
            }

            $newPicture['file'] = preg_replace('#^'.preg_quote(ACYM_ROOT, '#').'#i', ACYM_LIVE, $newPicture['file']);
            $newPicture['file'] = str_replace(DS, '/', $newPicture['file']);
            $replaceImage = [];
            $replaceImage[$path[1]] = $newPicture['file'];
            if (preg_match_all('#(width|height)(:|=) *"?([0-9]+)#i', $onepicture, $resultsSize)) {
                foreach ($resultsSize[0] as $i => $oneArg) {
                    $newVal = (strtolower($resultsSize[1][$i]) == 'width') ? $newPicture['width'] : $newPicture['height'];
                    if ($newVal > $resultsSize[3][$i]) {
                        continue;
                    }
                    $replaceImage[$oneArg] = str_replace($resultsSize[3][$i], $newVal, $oneArg);
                }
            }

            $replace[$onepicture] = str_replace(array_keys($replaceImage), $replaceImage, $onepicture);

            if (strpos($replace[$onepicture], 'width') === false) {
                if (strpos($onepicture, 'style="') !== false) {
                    $replace[$onepicture] = preg_replace('#style="([^"]*)"#Uis', 'style="'.$newDimension.'$1"', $replace[$onepicture]);
                } else {
                    $replace[$onepicture] = ' style="'.$newDimension.'" '.$replace[$onepicture];
                }
            }
        }

        if (!empty($replace)) {
            $input = str_replace(array_keys($replace), $replace, $content);
        }

        return $input;
    }

    public function generateThumbnail(string $picturePath): array
    {
        $paramsPos = strpos($picturePath, '?');
        if ($paramsPos !== false) {
            $picturePath = substr($picturePath, 0, $paramsPos);
        }

        [$currentwidth, $currentheight] = @getimagesize($picturePath);
        if (empty($currentwidth) || empty($currentheight)) {
            return [];
        }
        $factor = min($this->maxWidth / $currentwidth, $this->maxHeight / $currentheight);
        if ($factor >= 1) {
            return [];
        }
        $newWidth = round($currentwidth * $factor);
        $newHeight = round($currentheight * $factor);

        if (strpos($picturePath, 'http') === 0) {
            $filename = substr($picturePath, strrpos($picturePath, '/') + 1);
        } else {
            $filename = basename($picturePath);
        }

        if (substr($picturePath, 0, 10) == 'data:image') {
            preg_match('#data:image/([^;]{1,5});#', $picturePath, $resultextension);
            if (empty($resultextension[1])) {
                return [];
            }
            $extension = $resultextension[1];
            $name = md5($picturePath);
        } else {
            $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
            $name = strtolower(substr($filename, 0, strrpos($filename, '.')));
            $name .= substr(@filemtime($picturePath), -4);
        }

        $newImage = md5($picturePath).'-'.$name.'thumb'.$this->maxWidth.'x'.$this->maxHeight.'.'.$extension;
        if (empty($this->destination)) {
            $newFile = dirname($picturePath).DS.$newImage;
        } else {
            $newFile = $this->destination.$newImage;
        }

        if (file_exists($newFile)) {
            return [
                'file' => $newFile,
                'width' => $newWidth,
                'height' => $newHeight,
            ];
        }

        if (function_exists('exif_imagetype')) {
            $imageRealType = exif_imagetype($picturePath);
        } else {
            if ($extension === 'gif') {
                $imageRealType = IMAGETYPE_GIF;
            } elseif ($extension === 'jpg' || $extension === 'jpeg') {
                $imageRealType = IMAGETYPE_JPEG;
            } elseif ($extension === 'png') {
                $imageRealType = IMAGETYPE_PNG;
            } elseif ($extension === 'webp') {
                $imageRealType = IMAGETYPE_WEBP;
            } else {
                return [];
            }
        }

        switch ($imageRealType) {
            case IMAGETYPE_GIF:
                $img = imagecreatefromgif($picturePath);
                break;
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($picturePath);
                break;
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($picturePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $img = imagecreatefromwebp($picturePath);
                }
                break;
            default:
                return [];
        }

        if (empty($img)) {
            return [];
        }

        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($imageRealType, [IMAGETYPE_GIF, IMAGETYPE_PNG])) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $currentwidth, $currentheight);
        } else {
            ImageCopyResized($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $currentwidth, $currentheight);
        }

        ob_start();
        switch ($imageRealType) {
            case IMAGETYPE_GIF:
                $status = imagegif($thumb);
                break;
            case IMAGETYPE_JPEG:
                $status = imagejpeg($thumb, null, 100);
                break;
            case IMAGETYPE_PNG:
                $status = imagepng($thumb, null, 0);
                break;
            case IMAGETYPE_WEBP:
                $status = imagewebp($thumb, null, 100);
                break;
        }
        $imageContent = ob_get_clean();

        $status = $status && acym_writeFile($newFile, $imageContent);
        imagedestroy($thumb);
        imagedestroy($img);

        if (!$status) {
            $newFile = $picturePath;
        }

        return [
            'file' => $newFile,
            'width' => $newWidth,
            'height' => $newHeight,
        ];
    }
}
