<?php

namespace App\Repositories;

use App\Platform;
use App\Services\LearningAreaAPI\LearningAreaService;
use App\Services\Objects\PlatformFilter;
use App\Services\Storage\UploadedImage;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class PlatformRepository
{
    /**
     * Get Platforms
     * @param PlatformFilter|null $filter
     * @return Builder
     */
    public function listAll(?PlatformFilter $filter = null): Builder
    {
        return  Platform::when($filter, function ($query, $filter) {
            return Platform::when($filter->search, function ($query, $search) {
                $query->where('platforms.name', 'LIKE', "%{$search}%");
            })
                ->when($filter->createdPeriod, function ($query, $search) {
                    $query->whereBetween('platforms.created_at', [$search->startDate, $search->endDate]);
                })
                ->when($filter->clientId, function ($query, $clientId) {
                    $query->where('platforms.customer_id', $clientId);
                })
                ->when($filter->platformId, function ($query, $platformId) {
                    $query->where('platforms.id', $platformId);
                });
        });
    }

    /**
     * Get Platforms Client
     * @param PlatformFilter $filter
     * @return Builder
     */
    public function listPlatformClient(PlatformFilter $filter): Builder
    {
        return $this->listAll($filter)->select(
            'platforms.id',
            'platforms.created_at',
            'platforms.updated_at',
            'platforms.name',
            'platforms.url',
            'clients.company_name'
        )
            ->join('clients', 'platforms.customer_id', '=', 'clients.id');
    }

    //Get Products by Platform
    public function listPlatformProductsAndPlans(PlatformFilter $filter)
    {
        return $this->listAll($filter)
            ->join('products', 'products.platform_id', '=', 'platforms.id')
            ->join('clients', 'platforms.customer_id', '=', 'clients.id')
            ->leftJoin('plan_categories', 'products.category_id', '=', 'plan_categories.id')
            ->join('plans', 'products.id', '=', 'plans.product_id');
    }

    /**
     * Get Platform by ID
     *
     * @param string $id
     * @return mixed
     */
    public function findById(string $id)
    {
        return Platform::select(
            DB::raw('CONCAT(clients.first_name, " ", clients.last_name) as customer_name'),
            'platforms.id',
            'platforms.customer_id',
            'platforms.name',
            'platforms.name_slug',
            'platforms.url',
            'platforms.restrict_ips',
            'platforms.ips_available',
            'platforms.cover',
        )->leftJoin('clients', 'platforms.customer_id', '=', 'clients.id')->findOrFail($id);
    }

    /**
     * Save platform
     *
     *
     * @param array $data
     * @param UploadedFile|null $image
     * @return mixed
     */
    public function create(array $data, ?UploadedFile $image = null)
    {
        $uuid = Uuid::uuid4();
        $cover =  $this->setImage($uuid, $image);

        return (new Platform())->create([
            'id' => $uuid,
            'name' => $data['name'],
            'url' => $data['url'],
            'name_slug' => Str::slug($data['name'], '-'),
            'slug' => $data['slug'] ?? null,
            'cover' => $cover,
            'customer_id' => $data['customer_id'],
            'ips_available' => $data['ips_available'] ?? 0,
            'restrict_ips' => $data['restrict_ips'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     * @param UploadedFile|null $image
     * @return array
     */
    public function update(string $id, array $data, ?UploadedFile $image = null): array
    {
        $platform = Platform::findOrFail($id);
        $data['cover'] =  $this->setImage($id, $image);
        $platform->fill($data)->save();
        return $data;
    }

    /**
     * Delete Platform
     *
     * @param string $id
     */
    public function delete(string $id)
    {
        $platform = Platform::findOrFail($id);
        $platform->delete();
    }

    /**
     * Save image
     * @param $uuid
     * @param $image
     * @return string|null
     */
    private function setImage($uuid, $image): ?string
    {
        if (isset($image)) {
            $uploadImage = new UploadedImage($uuid, $image, Storage::disk('images'));
            $stored = $uploadImage->store();
            return $stored->converted;
        }
        return null;
    }


    /** REPOSITORY CORRECT FORM */

    /**
     * @param mixed $request
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function createPlatform($request)
    {
        $uuid = Uuid::uuid4()->toString();
        $cover = null;

        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            $uploadImage = new UploadedImage($uuid, $request->file('cover'), Storage::disk('images'));
            $stored = $uploadImage->store();
            $cover =  $stored->converted;
        }

        Platform::create([
            'id' => $uuid,
            'name' => $request->name,
            'url' => config('services.learninarea.url') . "/$uuid",
            'name_slug' => Str::slug($request->name, '-'),
            'slug' => Str::slug($request->name, '-'),
            'cover' => $cover,
            'customer_id' => $request->customer_id,
            'ips_available' => $request->ips_available ?? 0,
            'restrict_ips' => $request->restrict_ips,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->createDefaultLATheme($uuid, (string)$request->customer_id);
    }

    /**
     * Create default Layout on LA
     * @param string $platformId
     * @param string $producerId
     * @return mixed
     */
    private function createDefaultLATheme(string $platformId, string $producerId)
    {
        $defaultTheme = [
            "backgroundColor" => "#191414",
            "backgroundImageUrl" => config('services.learninarea.image'),
            "logoUrl" => "https://la-xgrow.sfo3.digitaloceanspaces.com/180d5f0237148d777de10569e24bfae6-grayLogo.svg",
            "faviconUrl" => "https://la-xgrow.sfo3.digitaloceanspaces.com/5d537df9692dba4e6d13769f3fb6da5d-favicon.ico",
            "bannerUrl" => "https://xgrow-dev.us-east-1.linodeobjects.com/54138015-2c29-4e80-b949-291ce481ddf7.jpg",
            "description" => "Meta descricao da plataforma",
            "keywords" => "xgrow",
            "platformId" => $platformId,
            "platformName" => "Nome da Plataforma",
            "primaryColor" => "#91bc1e",
            "secondaryColor" => "#c3ce01",
            "tertiaryColor" => "#282a2b",
            "textColor" => "#ffffff",
            "inputColor" => "#292929",
            "backgroundType" => "gradient",
            "backgroundGradientFirst" => "#0f1314",
            "backgroundGradientSecond" => "#262829",
            "backgroundGradientDegree" => 86,
            "borderRadius" => 50,
            "title" => "Titulo da Plataforma",
            "footer" => "Agradecimentos plataforma Xgrow. (Rodapé)",
            "supportNumber" => "",
            "supportEmail" => 'suporte@email.com',
            "supportLink" => "",
            "supportType" => "email",
        ];

        $response = (new LearningAreaService())->setLearningAreaTheme($platformId, $producerId, $defaultTheme);

        return $response;
    }
}
