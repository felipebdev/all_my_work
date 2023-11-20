<?php

namespace App;

use Faker\Provider\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

/*
    Próximas versões:
    implementar redimensionamento de iamgens
    implementar exclusão lógica/fisica dos arquivos
*/

class File extends Model
{
    
	protected $fillable = ['file', 'resize', 'width', 'height'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if(isset($model->file)){

                $file = $model->file;

                $extension = $file->getClientOriginalExtension();
                $name = $file->getClientOriginalName();

                $uuid = (string) Uuid::generate(4);
                $filename = sprintf('%s.%s',
                                    $uuid,
                                    $extension
                                    );

                $obs = (isset($file->obs)) ? $file->obs : null;
                $status = (isset($file->status)) ? $file->status : null;

                unset($model->file);                
                $model->original_name = $name;
                $model->obs = $obs;
                $model->status = $status;
                $model->filename = $filename;
                $model->type = $extension;
                $model->size = $file->getSize();
                $model->filable_id = $model->filable_id;
                $model->filable_type = $model->filable_type;
                
                Storage::disk('public_local')->put($filename, FacadesFile::get($file));

            }
           

        });

    }

     public function filable()
    {
        return $this->morphTo();
    }

    
}
