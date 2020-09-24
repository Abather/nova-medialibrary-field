<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;

class TestPost extends Resource
{
    public static $model = 'DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost';

    public function fields(Request $request)
    {
        return [
            Medialibrary::make('Media')
                ->fields(function ($request) {
                    return [
                        Text::make('File Name')
                            ->onlyOnForms(),

                        Text::make('Disk')
                            ->onlyOnDetail(),
                    ];
                })
                ->attachExisting(function (Builder $query, Request $request, HasMedia $model): void {
                    if ($request->name) {
                        $query->where('name', $request->name);
                    }
                })
                ->resolveMediaUsing(function (HasMedia $media, string $collectionName) {
                    return $media->getMedia($collectionName)->where('file_name', '!=', 'ignored.txt');
                })
                ->copyAs('Url', function (Media $media) {
                    return $media->getFullUrl();
                })
                ->copyAs('Html', function (Media $media) {
                    return $media->img();
                }, 'custom-icon'),

            Medialibrary::make('Media testing', 'testing')
                ->rules('required', 'array')
                ->creationRules('min:1')
                ->updateRules('min:2')
                ->attribute('media_testing_custom_attribute'),

            Medialibrary::make('Media testing single', 'testing_single')
                ->single(),

            Medialibrary::make('Media testing validation', 'testing_validation')
                ->attachRules('required', 'image'),

            new Panel('Panel', [
                Medialibrary::make('Media testing panel', 'testing_panel'),
            ]),

            ContainerField::make('Container', [
                Medialibrary::make('Media testing container', 'testing_container'),
            ]),
        ];
    }
}
