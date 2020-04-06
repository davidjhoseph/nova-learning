<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use App\Nova\Metrics\PostCount;
use App\Nova\Metrics\PostTrend;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use App\Nova\Actions\PublishPost;
use Laravel\Nova\Fields\DateTime;
use App\Nova\Filters\PostCategory;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Actions\UnpublishPost;
use App\Nova\Filters\PostPublished;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Metrics\PostCategory as PostCat;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Post';

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', $request->user()->id);
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title', 'body'
    ];

    public function title () {
        return $this->title . '-' . $this->category;
    }
    public function subtitle () {
        return 'Author: ' . $this->user->name;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('title')->rules('required'),
            Trix::make('body')->rules('required'),
            DateTime::make('Publish At', 'published_at')->hideFromIndex()->rules('after_or_equal:today'),
            DateTime::make('Publish Until', 'published_until')->hideFromIndex()->rules('after_or_equal:published_at'),
            Boolean::make('Is Published'),
            Select::make('category')->options([
                'news' => 'News',
                'tutorials' => 'Tutorials',
                'blogpost' => 'Blog Post',
            ])->displayUsingLabels()->rules('required'),
            BelongsTo::make('User'),
            BelongsToMany::make('Tags')
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new PostCount,
            new PostTrend,
            new PostCat
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new PostPublished,
            new PostCategory
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new PublishPost,
            new UnpublishPost
        ];
    }
}
