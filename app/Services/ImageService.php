<?php
namespace App\Services;



use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;


class ImageService
{
    /** Stores an image library  */
    protected $library;

    
    /**
     * Sets our image library
     *
     * @param  mixed $imageLibrary
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->library =  $image;
    }

    
    /**
     * Compress an image 
     *
     * @param  mixed $image
     * @return void
     */
    public function compressImage(UploadedFile $image, string $storagePath, array $size = [null, null])
    {

        $img = Image::make($image);

        list($width, $height) = getimagesize($image);

        $filename = time().bin2hex(random_bytes(10));

        $imgName = $filename.'.'.$image->getClientOriginalExtension();

        $pathString = $storagePath.$imgName;

        $savePath = storage_path('app/'.$storagePath);

        if (! file_exists($savePath)) {

            mkdir($savePath, 755, true);
        }

        if ( is_writeable($savePath)) {

            $img->resize(
                $width, $height, function ($constraints) {
                    $constraints->aspectRatio();
                }
            )->save($savePath.$imgName, 30); // This scale here is responsible for quality and compression
    
            return $pathString;
        }

        throw new \UnexpectedValueException("Could not write image data to disk");

        
    }

    
    /**
     * Resizes an image
     *
     * @param  mixed $image
     * @param  mixed $storagePath
     * @param  mixed $size
     * @return void
     */
    public function resizeImage(UploadedFile $image, string $storagePath, $size=[255, 255])
    {
        $imageName = time().$image->getClientOriginalName();

        $pathString = $storagePath.$imageName;

        $image = Image::make($image)->resize($size[0], $size[1]);

        $image->save(storage_path('app/'.$pathString));

        return $pathString;
    }


    public function saveEncodedString(string $encodedData, string $storagePath)
    {

        $filename = time().bin2hex(random_bytes(10)).'.png';

        $pathString = $storagePath.$filename;

        $image = Image::make($encodedData);

        $image->save(storage_path('app/'.$pathString));

        return $pathString;
    }
}