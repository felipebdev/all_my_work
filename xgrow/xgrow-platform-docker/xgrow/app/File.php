<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as InterventionImage;
use stdClass;
use Webpatser\Uuid\Uuid;

/*
    Próximas versões:
    implementar redimensionamento de iamgens
    implementar exclusão lógica/fisica dos arquivos
*/

class File extends Model
{

    protected $fillable = ['original_name', 'filename', 'obs', 'type', 'size', 'external', 'copyright', 'filable_id', 'status', 'filable_type'];

    // const DEFAULT_IMAGE = '48ed8348-2564-4a22-a551-cb596b7dbd30.png';
    const DEFAULT_IMAGE = 'big-file.png';
    const DEFAULT_THUMB_IMAGE = 'icon-file.png';
    const DEFAULT_PATH = 'xgrow-vendor/assets/img/';

    public function filable()
    {
        return $this->morphTo();
    }

    static function getImage($model, $field, $proportion = null)
    {
        return $image = ($model->id == 0 or $model["{$field}_id"] == 0)
            ? self::getThumbByProportion($proportion)
            : $model["{$field}"]->filename;
    }

    static function getCopyright($model, $field)
    {
        $copyright = ($model->id == 0 or $model["{$field}_id"] == 0) ? "" : $model["{$field}"]->copyright;
        return $copyright;
    }

    static function getImageTag($model, $field, $id = "", $classes = "")
    {
        $image = old("{$field}_upimage_url", File::getImage($model, $field, $classes));
        $file_id = old("{$field}_upimage_file_id", ($model->id == 0 ? 0 : $model["{$field}_id"]));
        $copyright = old("{$field}_upimage_copyright", File::getCopyright($model, $field));
        $id = ($id != "") ? $id : $field;
        $classes = ($classes != "") ? " class='$classes'" : '';
        $tag = "<img src=\"{$image}\" id=\"{$id}\"{$classes} style='" . File::getSizeByFormat($classes) . "object-fit:cover'>
                <input type='hidden' name=\"{$field}_upimage_url\" id=\"{$field}_upimage_url\" value=\"{$image}\" />
                <input type='hidden' name=\"{$field}_upimage_file_id\" id=\"{$field}_upimage_file_id\" value=\"{$file_id}\" />
                <input type='hidden' name=\"{$field}_upimage_copyright\" id=\"{$field}_upimage_copyright\" value=\"{$copyright}\" />";
        return $tag;
    }

    static function getUploadButton($field, $classes = "", $title = "", $tab_hide = "", $style = "")
    {
        $upimage_type = old("{$field}_upimage_type", "local");
        $title = ($title != "") ? $title : 'Upload';
        $input = "
        <button type=\"button\" class=\"{$classes} up_image_button btn xgrow-upload-btn-lg\" data-toggle=\"modal\" data-target=\"#upImageModal\" data-source=\"{$field}\"  data-tab_hide=\"{$tab_hide}\" data-bs-toggle=\"modal\" data-bs-target=\"#upImageModal\" style='{$style}'><i class=\"fa fa-upload\"></i> {$title}</button>
        <input id=\"{$field}_upimage_type\" name=\"{$field}_upimage_type\" type=\"hidden\" value=\"$upimage_type\">";

        return $input;
    }

    static function getRules($request, $field, $required, $proportion)
    {
        $validation = [];
        $validation["{$field}_upimage_type"] = 'required_if:id,0';
        if ($request->input("{$field}_upimage_type") == 'local') {
            $uploaded_file = $request->file("{$field}_upimage");
            if ($required == 1) $validation["{$field}_upimage"] = 'required_if:id,0';
            if (isset($uploaded_file)) $validation["{$field}_upimage"] = "required|mimes:jpeg,jpg,png,gif,svg" . self::getRuleProportion($proportion);
        }

        return $validation;
    }

    static function setUploadedFile($request, $field, $required = 1, $proportion = null)
    {
        $file_type = $request->input("{$field}_upimage_type");
        $file_url = $request->input("{$field}_upimage_url");
        $file_id = $request->input("{$field}_upimage_file_id");
        $copyright = $request->input("{$field}_upimage_copyright");
        $file_uploaded = $request->file("{$field}_upimage");

        if ($file_id == 0) {
            $rules = File::getRules($request, $field, $required, $proportion);
            $validator = Validator::make($request->all(), $rules);
            $validator->validate();

            if ($file_type == 'local') {
                if ($file_uploaded) {
                    $file = File::saveFile($file_uploaded, $file_type, $copyright);
                } else {
                    $file = new stdClass;
                    $file->id = 0;
                    $file->filename = asset('xgrow-vendor/assets/img/' . File::DEFAULT_IMAGE);
                }
                $file_url = asset('uploads/' . $file->filename);

            } else {
                $file = File::saveFile($file_url, $file_type, $copyright);
            }

            $request->request->add(["{$field}_upimage_url" => $file_url]);
            $request->request->add(["{$field}_upimage_file_id" => $file->id]);

        } else if ($file_id > 0) {
            $file = File::find($file_id);
        } else {
            $file = null;
        }
        return $file;
    }

    static function saveFile($file_uploaded, $type = 'local', $copyright = null)
    {
        if ($file_uploaded != null) {
            if ($type == 'local') {
                $data = File::getDataUploadedFile($file_uploaded);
                $driver = 'images';
                Storage::disk($driver)->put($data->filename, FacadesFile::get($file_uploaded));
                $filename = Storage::disk($driver)->url($data->filename);
            } else {
                $data = File::getDataExternalFile($file_uploaded);
                $filename = $file_uploaded;
            }

            $file = File::create([
                'original_name' => $data->name,
                'filename' => $filename,
                'obs' => $data->obs,
                'status' => $data->status,
                'type' => $data->extension,
                'copyright' => $copyright,
                'size' => $data->size
            ]);

            return $file;
        }
    }

    /**
     * Return WEBP image to upload for storage S3
     * @param $file
     * @return \Intervention\Image\Image | stdClass
     * @throws Exception
     */
    static function getDataUploadedFile($file)
    {
        $extensions = $file->getClientOriginalExtension();
        if (in_array($extensions, ['jpg', 'jpeg', 'png', 'ico', 'gif', 'bmp', 'WebP'])) {
            $name = explode('.', $file->getClientOriginalName());
            $webPFile = InterventionImage::make($file)->encode('webp', 95);
            $webPFile->extension = 'webp';
            $webPFile->name = implode('.', array_splice($name, 0, count($name) - 1));

            $uuid = (string)Uuid::generate(4);
            $webPFile->filename = sprintf('%s.%s',
                $uuid,
                $webPFile->extension
            );

            $webPFile->obs = (isset($file->obs)) ? $file->obs : '';
            $webPFile->status = (isset($file->status)) ?? $file->status;
            $webPFile->size = $webPFile->filesize();

            return $webPFile;
        } else {
            $data = new stdClass;
            $data->extension = $file->getClientOriginalExtension();
            $data->name = $file->getClientOriginalName();

            $uuid = (string)Uuid::generate(4);
            $data->filename = sprintf('%s.%s',
                $uuid,
                $data->extension
            );

            $data->obs = (isset($file->obs)) ? $file->obs : '';
            $data->status = (isset($file->status)) ?? $file->status;
            $data->size = $file->getSize();

            return $data;
        }
    }

    static function getDataExternalFile($url)
    {
        $data = new stdClass;
        $headers = get_headers($url, 1);

        $data->obs = null;
        $data->status = null;
        $data->extension = explode('/', $headers["Content-Type"])[1];
        $data->size = $headers["Content-Length"];
        $data->name = explode('?', $url)[0];

        $uuid = (string)Uuid::generate(4);
        $data->filename = sprintf('%s.%s',
            $uuid,
            $data->extension
        );
        return $data;
    }

    static function saveUploadedFile($model, $file, $field = null)
    {
        if ($file and $file->id > 0) {
            $file->update([
                "filable_id" => $model->id,
                "filable_type" => get_class($model),
            ]);

            if ($field != null) {
                $model->update([
                    "{$field}" => $file->id
                ]);
            }
        }
    }

    static function setUploadedAudio($request, $field)
    {
        $file = $request->file($field);
        $file = File::saveFile($file);
        return $file;
    }


    // Using where has multiple uploads (array)
    static function setUploadedSingleFile($uploadedFile)
    {
        if ($uploadedFile) {
            $file = self::saveFile($uploadedFile);
            return $file;
        }
        return 0;
    }


    /**
     * Return Size/5 for better frontend image view
     * Look XP-1073 for reference
     * @param $class
     * @return string
     */
    static function getSizeByFormat($class): string
    {
        if (strpos($class, '3x2') !== false) {
            // Proportion/5
            return 'width:256px;height:169.6px;';
        }
        if (strpos($class, '2x3') !== false) {
            // Proportion/5
            return 'width:169.6px;height:256px;';
        }
        if (strpos($class, '3x1') !== false) {
            // Not Resized
            return 'width:320px;height:180px;';
        }
        if (strpos($class, '1x1') !== false) {
            // Not Resized
            return 'width:180px;height:180px;';
        }
        if (strpos($class, '16x9') !== false) {
            // Proportion/5
            return 'width:288px;height:162px;';
        }
        if (strpos($class, '25x45') !== false) {
            // Proportion/5
            return 'width:276px;height:138px;';
        }
        return '';
    }

    /**
     * Return correct thumb image by proportion
     * Look XP-1073 for reference
     * @param $class
     * @return string
     */
    static function getThumbByProportion($class): string
    {
        $bigImage = '/' . self::DEFAULT_PATH . self::DEFAULT_IMAGE;
        $smallImage = '/' . self::DEFAULT_PATH . self::DEFAULT_THUMB_IMAGE;

        if (strpos($class, '3x2') !== false) {
            return $bigImage;
        }
        if (strpos($class, '2x3') !== false) {
            return $smallImage;
        }
        if (strpos($class, '3x1') !== false) {
            return $bigImage;
        }
        if (strpos($class, '1x1') !== false) {
            return $smallImage;
        }
        if (strpos($class, '16x9') !== false) {
            return $bigImage;
        }
        if (strpos($class, '25x45') !== false) {
            return $smallImage;
        }
        return $bigImage;
    }

    /**
     * Return Rules for proportions
     * Look XP-1073 for reference
     * Ref dimensions: https://laravel.com/docs/8.x/validation#rule-dimensions
     * Obs:: removed the minimum size by request
     * @param $class
     * @return string
     */
    static function getRuleProportion($class): string
    {
        if (strpos($class, '3x2') !== false) {
            return '|dimensions:max_width=5120,max_height=3390';
        }
        if (strpos($class, '2x3') !== false) {
            return '|dimensions:max_width=3390,max_height=5120';
        }
        if (strpos($class, '3x1') !== false) {
            return '|dimensions:max_width=1280,max_height=720';
        }
        if (strpos($class, '1x1') !== false) {
            return '|dimensions:max_width=720,max_height=720';
        }
        if (strpos($class, '16x9') !== false) {
            return '|dimensions:max_width=5760,max_height=3240';
        }
        if (strpos($class, '25x45') !== false) {
            return '|dimensions:max_width=5520,max_height=2760';
        }
        return '';
    }
}
