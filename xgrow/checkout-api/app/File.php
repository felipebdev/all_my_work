<?php

namespace App;

use Faker\Provider\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use stdClass;

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

    static function getImage($model, $field)
    {
        if ($field === "thumb") {
            $image = ($model->id == 0 or $model["{$field}_id"] == 0)
                ? asset(self::DEFAULT_PATH . File::DEFAULT_THUMB_IMAGE)
                : $model["{$field}"]->filename;
        }
        if ($field === "upsell_image" || $field === "order_bump_image") {
            $image = ($model->id == 0 or $model["{$field}_id"] == 0)
                ? asset(url('xgrow-vendor/assets/img/products/img_default.svg'))
                : $model["{$field}"]->filename;
        } else {
            $image = ($model->id == 0 or $model["{$field}_id"] == 0)
                ? asset(self::DEFAULT_PATH . File::DEFAULT_IMAGE)
                : $model["{$field}"]->filename;
        }
        return $image;
    }

    static function getCopyright($model, $field)
    {
        $copyright = ($model->id == 0 or $model["{$field}_id"] == 0) ? "" : $model["{$field}"]->copyright;
        return $copyright;
    }

    static function getImageTag($model, $field, $id = "", $classes = "")
    {
        $image = old("{$field}_upimage_url", File::getImage($model, $field));
        $file_id = old("{$field}_upimage_file_id", ($model->id == 0 ? 0 : $model["{$field}_id"]));
        $copyright = old("{$field}_upimage_copyright", File::getCopyright($model, $field));
        $id = ($id != "") ? $id : $field;
        $classes = ($classes != "") ? " class='$classes'" : '';
        $tag = "<img src=\"{$image}\" id=\"{$id}\"{$classes} style='width:70%'>
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

    static function getRules($request, $field, $required)
    {
        $validation = [];
        $validation["{$field}_upimage_type"] = 'required_if:id,0';
        if ($request->input("{$field}_upimage_type") == 'local') {
            $uploaded_file = $request->file("{$field}_upimage");
            if ($required == 1)
                $validation["{$field}_upimage"] = 'required_if:id,0';
            if (isset($uploaded_file)) {
                $validation["{$field}_upimage"] = "required|mimes:jpeg,jpg,png,gif,svg";
            }
        }
        return $validation;
    }

    static function setUploadedFile($request, $field, $required = 1)
    {

        $file_type = $request->input("{$field}_upimage_type");
        $file_url = $request->input("{$field}_upimage_url");
        $file_id = $request->input("{$field}_upimage_file_id");
        $copyright = $request->input("{$field}_upimage_copyright");

        $file_uploaded = $request->file("{$field}_upimage");

        if ($file_id == 0) {

            $rules = File::getRules($request, $field, $required);

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
                $driver = (config('app.env') == 'production') ? 'linode' : 'public_local';
                Storage::disk($driver)->put($data->filename, FacadesFile::get($file_uploaded));
                $filename = Storage::disk($driver)->url($data->filename);
            } else {
                $data = File::getDataExternalFile($file_uploaded);
                $filename = $file_uploaded;
                //salva local
                //file_put_contents("uploads/" . $data->filename, file_get_contents($file_uploaded));
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

    static function getDataUploadedFile($file)
    {
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

            $file->update(
                [
                    "filable_id" => $model->id,
                    "filable_type" => get_class($model),
                ]
            );

            if ($field != null) {
                $model->update(
                    [
                        "{$field}" => $file->id
                    ]
                );
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
}
