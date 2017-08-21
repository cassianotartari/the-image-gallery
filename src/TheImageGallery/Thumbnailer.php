<?php

namespace TheImageGallery;

use Imanee\Imanee;

class Thumbnailer {

    public $imageLocation;
    public $thumbLocation;

    const SMALL = 'small';
    const MEDIUM = 'medium';
    const ORIGINAL = 'original';

    public function __construct($imageLocation, $thumbnailLocation) {
        $this->imageLocation = $imageLocation;
        $this->thumbLocation = $thumbnailLocation;
    }

    public function createFromId($id, $size) {
        if (!file_exists($this->imageLocation . '/' . 'img_' . $id . '.jpg')) {
            throw new \Exception('File not found');
        }

        $thumb_name = '';
        $thumb_width = null;
        $thumb_height = null;

        switch ($size) {
            default:
            case self::SMALL:
                $thumb_name = $this->thumbLocation . '/sm_' . $id . '.jpg';
                $thumb_width = 320;
                $thumb_height = 240;
                break;

            case self::MEDIUM:
                $thumb_name = $this->thumbLocation . '/md_' . $id . '.jpg';
                $thumb_width = 1024;
                $thumb_height = 768;
                break;
        }
        if (!file_exists($thumb_name)) {
            $img = new Imanee($this->imageLocation . '/' . 'img_' . $id . '.jpg');
            $img->thumbnail($thumb_width, $thumb_height, true);
            file_put_contents($thumb_name, $img->output('jpg'));
        }

        return $thumb_name;
    }

}
